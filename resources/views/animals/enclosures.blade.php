@extends('animals.layout')

@section('title', 'Kifutók listázása')

@section('content')
    <h1>Kifutók listája:</h1>
    <ol>
        @foreach ($filteredEnclosures as $e)
            <li>Kifutó neve: {{ $e->name }}, állat limitje: {{ $e->limit }}, állatszáma:
                {{ count($e->animals) }}</li>
            <a href="{{ route('getEnclosure', ['enclosure' => $e->id]) }}">Megjelenítés</a>
            @if (auth()->user()->admin)
                <a href="{{ route('getEditEnclosure', ['enclosure' => $e->id]) }}">Szerkesztés</a>
                <form action="{{ route('deleteEnclosure', ['enclosure' => $e->id]) }}" method="post">
                    @csrf
                    @method('delete')
                    <button type="submit">Törlés</button>
                </form>
            @endif
        @endforeach
    </ol>
@endsection
