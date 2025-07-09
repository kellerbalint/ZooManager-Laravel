<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Enclosure;
use App\Models\Animal;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ZooController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $enclosures = Enclosure::all();
        $animals = Animal::all();
        $filteredEnclosures = Auth::user()->enclosures
            ->where('feeding_at', '>', Carbon::now('Europe/Budapest'))
            ->sortBy('feeding_at');

        return view('animals.main', [
            'enclosures' => $enclosures,
            'animals' => $animals,
            'filteredEnclosures' => $filteredEnclosures,
        ]);
    }

    public function list()
    {
        $filteredEnclosures = null;
        if (Auth::user()->admin == true) {
            $filteredEnclosures = Enclosure::all()->sortBy('name');
        } else {
            $filteredEnclosures = Auth::user()->enclosures->sortBy('name');
        }
        return view('animals.enclosures', ['filteredEnclosures' => $filteredEnclosures]);
    }

    public function getCreateEnclosure()
    {
        if (!Auth::user()->admin) {
            abort(401);
        }
        return view('animals.createEnclosure');
    }

    public function createEnclosure(Request $request)
    {
        if (!Auth::user()->admin) {
            abort(401);
        }

        $validated = $request->validate([
            'name' => 'required|string|min:4|max:20',
            'limit' => 'required|integer|min:1|max:10',
            'feeding_at' => 'required|date_format:H:i',
        ]);

        $enclosure = Enclosure::create($validated);
        return redirect()->route('getEnclosures')->with('success', 'Sikeres kifutó létrehozás!');
    }

    public function getEditEnclosure(string $id)
    {
        if (!Auth::user()->admin) {
            abort(401);
        }

        $enclosure = Enclosure::findOrFail($id);
        $users = User::all();
        $notOccupied = [];
        foreach ($users as $u) {
            if (!$enclosure->users->contains($u)) {
                $notOccupied[] = $u;
            }
        }
        return view('animals.editEnclosure', ['enclosure' => $enclosure, 'notOccupied' => $notOccupied]);
    }

    public function editEnclosure(string $id, Request $request)
    {
        if (!Auth::user()->admin) {
            abort(401);
        }

        $validated = $request->validate([
            'name' => 'required|string|min:4|max:20',
            'limit' => 'required|integer|min:1|max:10',
            'feeding_at' => 'required|date_format:H:i',
            'users' => 'nullable|array',
        ]);

        $users = User::all();
        if (isset($validated['users'])) {
            foreach ($validated['users'] as $uid) {
                if (!$users->contains('id', $uid)) {
                    abort(401); //Nem létező felhasználó id lett megadva
                }
                if (!filter_var($uid, FILTER_VALIDATE_INT)) {
                    abort(401); //Nem integer felhasználó id lett megadva
                }
            }
        }

        $enclosure = Enclosure::findOrFail($id);
        $enclosure->update($validated);

        $userIds = $request->input('users', []);
        $enclosure->users()->sync($userIds);

        return redirect()->route('getEnclosures')->with('success', 'Sikeres kifutó szerkesztés!');
    }

    public function deleteEnclosure(string $id)
    {
        $enclosure = Enclosure::findOrFail($id);

        // Authorization
        if (!Auth::user()->admin) {
            abort(401); //Nem admin nem törölhet
        }

        if (count($enclosure->animals) != 0) {
            abort(409, 'A kifutó nem törölhető, mert még tartalmaz állatokat.'); //Nem üres enclosure nem törölhető
        }

        $enclosure->users()->detach();
        $enclosure->delete();

        return redirect()->route('getEnclosures')->with('success', 'Sikeres kifutó törlés!');;
    }

    public function getEnclosure(string $id)
    {
        $enclosure = Enclosure::findOrFail($id);
        $animals = $enclosure->animals->sortBy([
            ['species', 'asc'],
            ['born_at', 'asc'],
        ])->values();

        // Authorization
        if (!Auth::user()->enclosures->contains($enclosure) && !Auth::user()->admin) {
            abort(401);
        }

        return view('animals.enclosure', ['enclosure' => $enclosure, 'animals' => $animals]);
    }

    public function getCreateAnimal()
    {
        if (!Auth::user()->admin) {
            abort(401);
        }

        $enclosures = Enclosure::all();
        $predators = [];
        $herbivores = [];
        $empties = [];
        foreach ($enclosures as $e) {
            if (count($e->animals) == 0) {
                $empties[] = $e;
            } else if ($e->animals[0]->is_predator) {
                $predators[] = $e;
            } else {
                $herbivores[] = $e;
            }
        }
        return view('animals.createAnimal', ['empties' => $empties, 'predators' => $predators, 'herbivores' => $herbivores]);
    }

    public function createAnimal(Request $request)
    {
        if (!Auth::user()->admin) {
            abort(401);
        }

        $validated = $request->validate([
            'name' => 'required|string|min:3|max:20',
            'species' => 'required|string|min:3|max:10',
            'is_predator' => 'nullable',
            'enclosure_id' => 'required|integer',
            'image' => 'nullable|image|mimes:jpg,jpeg,png',
            'born_at' => 'required|date_format:Y-m-d'
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('animals', 'public');
            $validated['image'] = '/storage/' . $path;
        }

        if (!isset($validated['is_predator'])) {
            $validated['is_predator'] = false;
        }

        $enclosure = Enclosure::findOrFail($validated['enclosure_id']);
        //Megfelelő állat validálás
        if (count($enclosure->animals) > 0) {
            if ($validated['is_predator'] && !$enclosure->animals[0]->is_predator) {
                return redirect()->back()->withErrors(['enclosure_id' => 'Ragadozó állat nem kerülhet növényevőek közé.'])->withInput();
            } else if (!$validated['is_predator'] && $enclosure->animals[0]->is_predator) {
                return redirect()->back()->withErrors(['enclosure_id' => 'Növényevő állat nem kerülhet ragadozók közé.'])->withInput();
            }
        }

        if (count($enclosure->animals) == $enclosure->limit) {
            return redirect()->back()->withErrors(['enclosure_id' => 'A kifutó ' . $enclosure->name . ' már megtelt.'])->withInput();
        }

        $animal = Animal::create($validated);
        return redirect()->route('getEnclosures')->with('success', 'Sikeres állat létrehozás!');
    }

    public function getEditAnimal(string $id)
    {
        if (!Auth::user()->admin) {
            abort(401);
        }

        $animal = Animal::findOrFail($id);

        $enclosures = Enclosure::all();
        $predators = [];
        $herbivores = [];
        $empties = [];
        foreach ($enclosures as $e) {
            if (count($e->animals) == 0) {
                $empties[] = $e;
            } else if ($e->animals[0]->is_predator) {
                $predators[] = $e;
            } else {
                $herbivores[] = $e;
            }
        }
        return view('animals.editAnimal', ['animal' => $animal, 'empties' => $empties, 'predators' => $predators, 'herbivores' => $herbivores]);
    }

    public function editAnimal(string $id, Request $request)
    {
        if (!Auth::user()->admin) {
            abort(401);
        }

        $validated = $request->validate([
            'name' => 'required|string|min:3|max:20',
            'species' => 'required|string|min:3|max:10',
            'is_predator' => 'nullable',
            'enclosure_id' => 'required|integer',
            'image' => 'nullable|image|mimes:jpg,jpeg,png',
            'born_at' => 'required|date_format:Y-m-d'
        ]);

        $animal = Animal::findOrFail($id);

        if ($request->hasFile('image')) {
            $path = str_replace('/storage/', '', $animal->image);
            Storage::disk('public')->delete($path);
            $path = $request->file('image')->store('animals', 'public');
            $validated['image'] = '/storage/' . $path;
        }

        if (!isset($validated['is_predator'])) {
            $validated['is_predator'] = false;
        }

        $enclosure = Enclosure::findOrFail($validated['enclosure_id']);
        //Megfelelő állat validálás
        if (count($enclosure->animals) > 0) {
            if ($validated['is_predator'] && !$enclosure->animals[0]->is_predator) {
                return redirect()->back()->withErrors(['enclosure_id' => 'Ragadozó állat nem kerülhet növényevőek közé.'])->withInput();
            } else if (!$validated['is_predator'] && $enclosure->animals[0]->is_predator) {
                return redirect()->back()->withErrors(['enclosure_id' => 'Növényevő állat nem kerülhet ragadozók közé.'])->withInput();
            }
        }

        if ($animal->enclosure_id != $enclosure->id && count($enclosure->animals) == $enclosure->limit) {
            return redirect()->back()->withErrors(['enclosure_id' => 'A kifutó ' . $enclosure->name . ' már megtelt.'])->withInput();
        }

        $animal->update($validated);
        return redirect()->route('getEnclosures')->with('success', 'Sikeres állat létrehozás!');
    }

    public function archiveAnimal(string $id)
    {
        if (!Auth::user()->admin) {
            abort(401); //Nem admin nem törölhet
        }

        $animal = Animal::findOrFail($id);
        $animal->enclosure_id = null;
        $animal->save();
        $animal->delete();

        return redirect()->back();
    }

    public function getArchivedAnimals()
    {
        if (!Auth::user()->admin) {
            abort(401);
        }

        $enclosures = Enclosure::all();
        $predators = [];
        $herbivores = [];
        $empties = [];
        foreach ($enclosures as $e) {
            if (count($e->animals) == 0) {
                $empties[] = $e;
            } else if ($e->animals[0]->is_predator) {
                $predators[] = $e;
            } else {
                $herbivores[] = $e;
            }
        }

        $animals = Animal::onlyTrashed()->orderBy('deleted_at', 'desc')->get();
        return view('animals.archivedAnimals', ['animals' => $animals, 'empties' => $empties, 'predators' => $predators, 'herbivores' => $herbivores]);
    }

    public function restoreArchivedAnimal(string $id, Request $request)
    {
        if (!Auth::user()->admin) {
            abort(401);
        }

        $animal = Animal::onlyTrashed()->where('id', $id)->first();

        $validated = $request->validate([
            'enclosure_id' => 'required|integer|exists:enclosures,id',
        ]);

        $enclosure = Enclosure::findOrFail($validated['enclosure_id']);
        //Megfelelő állat validálás
        if (count($enclosure->animals) > 0) {
            if ($animal->is_predator && !$enclosure->animals[0]->is_predator) {
                return redirect()->back()->withErrors(['enclosure_id' => 'Ragadozó állat nem kerülhet növényevőek közé.'])->withInput();
            } else if (!$animal->is_predator && $enclosure->animals[0]->is_predator) {
                return redirect()->back()->withErrors(['enclosure_id' => 'Növényevő állat nem kerülhet ragadozók közé.'])->withInput();
            }
        }

        if (count($enclosure->animals) == $enclosure->limit) {
            return redirect()->back()->withErrors(['enclosure_id' => 'A kifutó ' . $enclosure->name . ' már megtelt.'])->withInput();
        }

        $animal->enclosure_id = $validated['enclosure_id'];
        $animal->save();
        $animal->restore();
        return redirect()->back();
    }
}
