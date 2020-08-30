@extends('layouts.my_app')
@section('title')
    Empresa - Panel Administrativo
@endsection

@section('sidenav')
<x-side-bar tab="2" selected='1' empresa="{{$empresa->id}}"/>
@endsection

@section('content')
<div class="container">
    <div class="row justify-content-center align-items-center mt-3">
        <componente-procesos></componente-procesos>
    </div>
</div>
@endsection

@section('scripts')
    <script>
        let procesos = @json($procesos);
        let unidad = @json(session('unidad'));
    </script>
@endsection
