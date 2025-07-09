@extends('animals.layout')

@section('title', 'Főoldal')

@section('content')
    <h1>Üdvözöllek az állatkertben!</h1>
    <h3>Kifutók száma: {{ count($enclosures) }}</h3>
    <h3>Állatok száma: {{ count($animals) }}</h3>
    <h3>Az ön kifutói: </h3>
    <ol>
        @foreach ($filteredEnclosures as $e)
            <li>Kifutó neve: {{ $e->name }}, Etetési ideje: {{ $e->feeding_at }}</li>
        @endforeach
    </ol>
@endsection
