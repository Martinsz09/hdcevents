@php
    $eventItems = $event->items ?? [];
@endphp

@extends('layouts.main')

@section('title', 'Editando: ' . $event->title)

@section('content')

<div id="event-create-container" class="col-md-6 offset-md-3">

  <h1>Editando: {{ $event->title }}</h1>

  <form action="/events/update/{{ $event->id }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="form-group">
      <label>Imagem do Evento:</label>
      <input type="file" name="image" class="form-control-file">
      <img src="/img/events/{{ $event->image }}" class="img-preview">
    </div>

    <div class="form-group">
      <label>Evento:</label>
      <input type="text" name="title" class="form-control" value="{{ $event->title }}">
    </div>

    <div class="form-group">
      <label>Data:</label>
      <input type="date" name="date" class="form-control" value="{{ $event->date->format('Y-m-d') }}">
    </div>

    <div class="form-group">
      <label>Cidade:</label>
      <input type="text" name="city" class="form-control" value="{{ $event->city }}">
    </div>

    <div class="form-group">
      <label>Privado?</label>
      <select name="private" class="form-control">
        <option value="0">Não</option>
        <option value="1" {{ $event->private == 1 ? 'selected' : '' }}>Sim</option>
      </select>
    </div>

    <div class="form-group">
      <label>Descrição:</label>
      <textarea name="description" class="form-control">{{ $event->description }}</textarea>
    </div>

    <div class="form-group">
      <label>Itens:</label>

      <div><input type="checkbox" name="items[]" value="Cadeiras" {{ in_array('Cadeiras', $eventItems) ? 'checked' : '' }}> Cadeiras</div>
      <div><input type="checkbox" name="items[]" value="Palco" {{ in_array('Palco', $eventItems) ? 'checked' : '' }}> Palco</div>
      <div><input type="checkbox" name="items[]" value="Cerveja grátis" {{ in_array('Cerveja grátis', $eventItems) ? 'checked' : '' }}> Cerveja grátis</div>
      <div><input type="checkbox" name="items[]" value="Open Food" {{ in_array('Open Food', $eventItems) ? 'checked' : '' }}> Open food</div>
      <div><input type="checkbox" name="items[]" value="Brindes" {{ in_array('Brindes', $eventItems) ? 'checked' : '' }}> Brindes</div>

    </div>

    <input type="submit" class="btn btn-primary" value="Editar Evento">

  </form>

</div>

@endsection