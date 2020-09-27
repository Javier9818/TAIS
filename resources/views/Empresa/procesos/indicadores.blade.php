@extends('layouts.my_app')
@section('title')
    Empresa - Panel Administrativo
@endsection

@section('sidenav')
<x-side-bar tab="2" selected='7' empresa="{{$empresa->id}}"/>
@endsection

@section('content')
<div class="container">
    <div class="row  align-items-center mt-3">
       <componente-gestion-indicadores></componente-gestion-indicadores>
    </div>
</div>
@endsection

@section('scripts')
    <script>
        let unidad = @json(session('unidad'));
    </script>
@endsection
