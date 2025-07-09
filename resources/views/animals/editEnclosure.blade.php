@extends('animals.layout')

@section('title', 'Új kifutó')

@section('content')
    <form action="{{ route('editEnclosure', ['enclosure' => $enclosure->id]) }}" method="post">
        @csrf
        @method('PUT')

        <label for="name">Név: </label>
        <input name="name" type="text" value="{{ old('name') ? old('name') : $enclosure->name }}"
            class="@error('name') is-invalid @enderror">
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror

        <label for="limit">Limit: </label>
        <input name="limit" type="text" value="{{ old('limit') ? old('limit') : $enclosure->limit }}"
            class="@error('limit') is-invalid @enderror">
        @error('limit')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror

        <label for="feeding_at">Etetési idő: </label>
        <input name="feeding_at" type="time" value="{{ old('feeding_at') ? old('feeding_at') : $enclosure->feeding_at }}"
            class="@error('feeding_at') is-invalid @enderror">
        @error('feeding_at')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror

        <h3>Gondozók:</h3>
        <ul>
            @foreach ($enclosure->users as $u)
                <li>
                    {{ $u->name }}
                    <input type="checkbox" name="users[]" value="{{ $u->id }}"
                        {{ in_array($u->id, old('users', $enclosure->users->pluck('id')->toArray())) ? 'checked' : '' }}>
                </li>
            @endforeach

            @foreach ($notOccupied as $u)
                <li>
                    {{ $u->name }}
                    <input type="checkbox" name="users[]" value="{{ $u->id }}"
                        {{ in_array($u->id, old('users', [])) ? 'checked' : '' }}>
                </li>
            @endforeach
        </ul>


        <button type="submit">Küldés</button>
    </form>
@endsection
