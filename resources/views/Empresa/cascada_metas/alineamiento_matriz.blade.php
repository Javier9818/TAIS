@extends('layouts.my_app')
@section('title')
    Empresa - Panel Administrativo
@endsection

@section('sidenav')
<x-side-bar tab="2" selected='2' empresa="{{$empresa->id}}"/>
@endsection

@section('content')
<div class="container">
    <div class="row justify-content-center align-items-center mt-3 ml-3">
        <h5 class="card-title text-left">Metas de alineamiento COBIT 2019 vs Metas empresariales resultantes</h5>
        <matriz-alineamiento></matriz-alineamiento>
    </div>
</div>
@endsection

@section('scripts')
    <script>
        let matriz = @json($matriz);
        let resultante = @json($resultante);
        let alineamiento = @json($alineamiento);
        let metas = @json($metas_resultantes);

        let versiones = @json($versiones);
        let version = @json(session('version'));
        let empresa = @json($empresa);
    </script>
@endsection
