@extends('animals.layout')

@section('title', 'Archivált állatok')

@section('content')
    <h1>Archivált állatok</h1>
    <ul>
        @foreach ($animals as $a)
            <li>Név: {{ $a->name }}, faj: {{ $a->species }}, archiválás dátuma: {{ $a->deleted_at }}</li>
            <form action="{{ route('restoreArchivedAnimal', ['animal' => $a->id]) }}" method="POST">
                @csrf
                <label for="enclosure_id">Válassz kifutót: </label>
                <select name="enclosure_id" id="enclosure_id" class="@error('enclosure_id') is-invalid @enderror">
                    <optgroup label="Üres kifutók">
                        @foreach ($empties as $e)
                            <option value="{{ $e->id }}" @selected(old('enclosure_id') == $e->id)>
                                {{ $e->name }}</option>
                        @endforeach
                    </optgroup>

                    <optgroup label="Ragadozós kifutók">
                        @foreach ($predators as $e)
                            <option value="{{ $e->id }}" @selected(old('enclosure_id') == $e->id)>{{ $e->name }}</option>
                        @endforeach
                    </optgroup>

                    <optgroup label="Növényevős kifutók">
                        @foreach ($herbivores as $e)
                            <option value="{{ $e->id }}" @selected(old('enclosure_id') == $e->id)>{{ $e->name }}</option>
                        @endforeach
                    </optgroup>
                </select>
                <button type="submit">Visszaállítás</button>
            </form>
        @endforeach
    </ul>
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
@endsection
