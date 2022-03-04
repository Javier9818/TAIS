@extends('layouts.my_app')
@section('title')
    Empresa - Panel Administrativo
@endsection

@section('sidenav')
<x-side-bar tab="1" selected='2' empresa="{{$empresa->id}}"/>
@endsection

@section('content')
<div class="container">
    <div class="row  align-items-center mt-3">
        <h5 class="card-title text-left ml-3">Objetivos estratégicos del negocio</h5>
        <table-entidades entidad="cliente"></table-entidades>
    </div>
</div>
@endsection

@section('scripts')
    <script>
        let empresa = @json($empresa);
        let objetivos = @json($objetivos);
        let perspectivas = @json($perspectivas);
    </script>
@endsection
