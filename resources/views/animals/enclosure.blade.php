@extends('animals.layout')

@section('title', 'Kifutók listázása')

@section('content')
    <h1>Kifutó adatai:</h1>
    <ul>
        <li>Név: {{ $enclosure->name }}</li>
        <li>Limit: {{ $enclosure->limit }}</li>
        <li>Állatok száma: {{ count($enclosure->animals) }}</li>
    </ul>
    @if (count($enclosure->animals) > 0 && $enclosure->animals[0]->is_predator)
        <p style="color: red">Vigyázat a kifutóban ragadozók vannak!</p>
    @endif


    <h1>Állatok listája:</h1>
    <ol>
        @foreach ($animals as $a)
            <li>Állat neve: {{ $a->name }}, állat faja: {{ $a->species }}, állat születési ideje:
                {{ $a->born_at }}</li>
            @if ($a->image)
                <img src="{{ $a->image }}" alt="" style="width: 100px">
            @else
                <img src="/placeholder.jpg" alt="" style="width: 100px">
            @endif

            @if (auth()->user()->admin)
                <a href="{{ route('getEditAnimal', ['animal' => $a->id]) }}">Szerkesztés</a>
                <form action="{{ route('archiveAnimal', ['animal' => $a->id]) }}" method="POST">
                    @csrf
                    @method('delete')
                    <button type="submit">Archiválás</button>
                </form>
            @endif
        @endforeach
    </ol>
@endsection
