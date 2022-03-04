@extends('layouts.my_app')
@section('title')
    Empresa - Panel Administrativo
@endsection

@section('sidenav')
<x-side-bar tab="2" selected='3' empresa="{{$empresa->id}}"/>
@endsection

@section('content')
<div class="container">
    <div class="row justify-content-center align-items-center mt-3 ml-3">
        <h5 class="card-title text-left">Objetivos de control COBIT 2019 vs Metas de alineamiento resultantes</h5>
        <matriz-control></matriz-control>
    </div>
</div>
@endsection

@section('scripts')
    <script>
        let matriz = @json($matriz);
        let resultante = @json($resultante);
        let control = @json($control);
        let metas = @json($metas_resultantes);

        let versiones = @json($versiones);
        let version = @json(session('version'));
        let empresa = @json($empresa);
    </script>
@endsection
