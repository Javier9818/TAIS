@extends('layouts.my_app')
@section('title')
    Empresa - Panel Administrativo
@endsection

@section('sidenav')
<x-side-bar tab="2" selected='1' empresa="{{$empresa->id}}"/>
@endsection

@section('content')
<div class="container">
    <div class="row justify-content-center align-items-center mt-3 ml-3">
        <h5 class="card-title text-left">Objetivos estratégicos del negocio vs Metas empresariales COBIT 2019</h5>
        <cascada-metas></cascada-metas>
    </div>
</div>
@endsection

@section('scripts')
    <script>
        let empresa = @json($empresa);
        let objetivos = @json($objetivos);
        let perspectivas = @json($perspectivas);
        let metas_resultantes = @json($metas_resultantes);
        
        let versiones = @json($versiones);
        console.log(objetivos)
        let version = @json(session('version', 1));
    </script>
@endsection
