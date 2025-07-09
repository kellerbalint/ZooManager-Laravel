<?php

use App\Http\Controllers\Controller;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ZooController;
use Illuminate\Support\Facades\Route;


Route::get('/dashboard', function () {
    return redirect('/main');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/', function () {
        return redirect('/main');
    });
    Route::get('/main', [ZooController::class, 'index'])->name('animals.index');
    Route::get('/enclosures', [ZooController::class, 'list'])->name('getEnclosures');

    Route::get('/createEnclosure', [ZooController::class, 'getCreateEnclosure'])->name('getCreateEnclosure');
    Route::post('/createEnclosure', [ZooController::class, 'createEnclosure'])->name('createEnclosure');

    Route::get('/editEnclosure/{enclosure}', [ZooController::class, 'getEditEnclosure'])->name('getEditEnclosure');
    Route::put('/editEnclosure/{enclosure}', [ZooController::class, 'editEnclosure'])->name('editEnclosure');

    Route::delete('/deleteEnclosure/{enclosure}', [ZooController::class, 'deleteEnclosure'])->name('deleteEnclosure');

    Route::get('/getEnclosure/{enclosure}', [ZooController::class, 'getEnclosure'])->name('getEnclosure');

    Route::get('/createAnimal', [ZooController::class, 'getCreateAnimal'])->name('getCreateAnimal');
    Route::post('/createAnimal', [ZooController::class, 'createAnimal'])->name('createAnimal');

    Route::get('/editAnimal/{animal}', [ZooController::class, 'getEditAnimal'])->name('getEditAnimal');
    Route::put('/editAnimal/{animal}', [ZooController::class, 'editAnimal'])->name('editAnimal');

    Route::delete('/archiveAnimal/{animal}', [ZooController::class, 'archiveAnimal'])->name('archiveAnimal');
    Route::get('/archivedAnimals', [ZooController::class, 'getArchivedAnimals'])->name('getArchivedAnimals');
    Route::post('/archivedAnimals/{animal}', [ZooController::class, 'restoreArchivedAnimal'])->name('restoreArchivedAnimal');
});


require __DIR__ . '/auth.php';
