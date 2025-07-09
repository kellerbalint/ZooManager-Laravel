@extends('animals.layout')

@section('title', 'Új kifutó')

@section('content')
    <form action="{{ route('createEnclosure') }}" method="post">
        @csrf

        <label for="name">Név: </label>
        <input name="name" type="text" value="{{ old('name') }}" class="@error('name') is-invalid @enderror">
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror

        <label for="limit">Limit: </label>
        <input name="limit" type="text" value="{{ old('limit') }}" class="@error('limit') is-invalid @enderror">
        @error('limit')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror

        <label for="feeding_at">Etetési idő: </label>
        <input name="feeding_at" type="time" value="{{ old('feeding_at') }}"
            class="@error('feeding_at') is-invalid @enderror">
        @error('feeding_at')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror

        <button type="submit">Küldés</button>
    </form>
@endsection
