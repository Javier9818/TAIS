@extends('layouts.my_app')
@section('title')
    Empresa - Panel Administrativo
@endsection

@section('sidenav')
<x-side-bar tab="1" selected='2' empresa="{{$empresa->id}}"/>
@endsection

@section('content')
<div class="container">
    <div class="row justify-content-center align-items-center mt-3">
        <h5 class="card-title text-left">Clientes registrados</h5>
        <table-entidades entidad="cliente"></table-entidades>
    </div>
</div>
@endsection

@section('scripts')
    <script>
        let empresa = @json($empresa);
        let entidades = @json($clientes);
    </script>
@endsection
