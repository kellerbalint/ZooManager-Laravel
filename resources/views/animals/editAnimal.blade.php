@extends('animals.layout')

@section('title', 'Új állat')

@section('content')
    <form action="{{ route('editAnimal', ['animal' => $animal->id]) }}" method="post" enctype="multipart/form-data">
        @csrf
        @method('put')

        <label for="name">Név: </label>
        <input name="name" type="text" value="{{ old('name') !== null ? old('name') : $animal->name }}"
            class="@error('name') is-invalid @enderror">
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror

        <label for="species">Faj: </label>
        <input name="species" type="text" value="{{ old('species') !== null ? old('species') : $animal->species }}"
            class="@error('species') is-invalid @enderror">
        @error('species')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror

        <p>
            <label for="is_predator">Ragadozó: </label>
            <input type="checkbox" name="is_predator" id="" @checked(old('is_predator') !== null ? old('is_predator') : $animal->is_predator)>
        </p>

        <p>
            <label for="enclosure_id">Kifutó: </label>
            <select name="enclosure_id" id="enclosure_id" class="@error('enclosure_id') is-invalid @enderror">
                <optgroup label="Üres kifutók">
                    @foreach ($empties as $e)
                        <option value="{{ $e->id }}" @selected(old('enclosure_id') !== null ? old('enclosure_id') == $e->id : $animal->enclosure_id == $e->id)>
                            {{ $e->name }}</option>
                    @endforeach
                </optgroup>

                <optgroup label="Ragadozós kifutók">
                    @foreach ($predators as $e)
                        <option value="{{ $e->id }}" @selected(old('enclosure_id') !== null ? old('enclosure_id') == $e->id : $animal->enclosure_id == $e->id)>{{ $e->name }}</option>
                    @endforeach
                </optgroup>

                <optgroup label="Növényevős kifutók">
                    @foreach ($herbivores as $e)
                        <option value="{{ $e->id }}" @selected(old('enclosure_id') !== null ? old('enclosure_id') == $e->id : $animal->enclosure_id == $e->id)>{{ $e->name }}</option>
                    @endforeach
                </optgroup>
            </select>
        </p>
        @error('enclosure_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror

        <p>
            <label for="image">Kép: </label>
            <input name="image" type="file" value="{{ old('image') }}" class="@error('image') is-invalid @enderror">
        </p>
        @error('image')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror

        <p>
            <label for="born_at">Születési idő: </label>
            <input name="born_at" type="date"
                value="{{ old('born_at') !== null ? old('born_at') : $animal->born_at->format('Y-m-d') }}"
                class="@error('born_at') is-invalid @enderror">
        </p>
        @error('born_at')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror

        <button type="submit">Küldés</button>
    </form>

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
