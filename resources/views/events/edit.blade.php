@extends('layouts.main')
@section('title', 'Editando: . $event->title')
@section('content')

    <div id="event-create-container" class="col-md-6 offset-md-3">
        <h1>Editando: {{ $event->title }}</h1>

        <form action="/events/update/{{ $event->id }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="form-group mb-2">
                <label for="title">Título:</label>
                <input type="text" class="form-control" id="title" name="title" value="{{ $event->title }}">
            </div>

            <div class="form-group mb-2">
                <label for="date">Título:</label>
                <input type="date" class="form-control" id="date" name="date" value="{{ date('Y-m-d', strtotime($event->date)) }}">
            </div>

            <div class="form-group mb-2">
                <label for="description">Descrição:</label>
                <textarea name="description" id="description" class="form-control">{{ $event->description }}</textarea>
            </div>

            <div class="form-group mb-2">
                <label for="city">Cidade:</label>
                <input type="text" class="form-control" id="city" name="city" value="{{ $event->city }}">
            </div>

            <div class="form-group mb-2">
                <label for="private">Evento Privado?</label>
                <input type="checkbox" id="private" name="private" {{ $event->private ? 'checked' : '' }}>
            </div>

            <div class="form-group mb-4">
                <label for="image">Imagem do Evento:</label>
                <input type="file" class="form-control-file" id="image" name="image">
                <img src="{{ asset($event->imageUrl) }}" alt="{{ $event->title }}" class="img-preview">
            </div>

            <div class="form-group mb-2">
                <label for="items">Adicione itens de infraestrutura:</label>
                <div class="form-group">
                    <input type="checkbox" name="items[]" value="Cadeiras"> Cadeiras
                </div>
                <div class="form-group">
                    <input type="checkbox" name="items[]" value="Palco"> Palco
                </div>
                <div class="form-group">
                    <input type="checkbox" name="items[]" value="Cerveja Grátis"> Cerveja Grátis
                </div>
                <div class="form-group">
                    <input type="checkbox" name="items[]" value="Open Food"> Open Food
                </div>
                <div class="form-group">
                    <input type="checkbox" name="items[]" value="Brindes"> Brindes
                </div>
            </div>

            <input type="submit" class="btn btn-primary" value="Editar evento">
        </form>
    </div>

@endsection
