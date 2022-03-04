@extends('layouts.my_app')
@section('title')
    Empresa - Panel Administrativo
@endsection

@section('sidenav')
<x-side-bar tab="2" selected='4' empresa="{{$empresa->id}}"/>
@endsection

@section('content')
<div class="container">
    <div class="row justify-content-center align-items-center mt-3 ml-3">
        <h5 class="card-title">Objetivos de control</h5>
        <objetivos-control></objetivos-control>
    </div>
</div>
@endsection

@section('scripts')
    <script>
        let empresa = @json($empresa);
        let objetivos_control = @json($objetivos_control);
        let versiones = @json($versiones);
        let version = @json(session('version'));
    </script>
@endsection
