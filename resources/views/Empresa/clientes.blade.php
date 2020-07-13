@extends('layouts.app')
@section('title')
    Empresa - Panel Administrativo
@endsection

@section('content')
<header>
    <x-side-bar tab="1" selected='2' empresa="{{$empresa->id}}"/>
</header>

<div class="container">
    <div class="row justify-content-center align-items-center mt-3">
        <h5 class="card-title text-left">Clientes registrados</h5>
        <table-clientes></table-clientes>
    </div>
</div>
@endsection

@section('scripts')
    <script>
        let empresa = @json($empresa);
        let clientes = @json($clientes);
    </script>
@endsection
