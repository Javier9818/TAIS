@extends('layouts.my_app')
@section('title')
    Empresa - Panel Administrativo
@endsection

@section('styles')
    <script src="https://unpkg.com/gojs@2.1/release/go.js"></script>
    <script src="https://gojs.net/latest/extensions/DoubleTreeLayout.js"></script>
@endsection

@section('sidenav')
<x-side-bar tab="2" selected='3' empresa="{{$empresa->id}}"/>
@endsection

@section('content')
<div class="container">
    <div class="row">
        <componente-priorizacion></componente-priorizacion>
    </div>
</div>
@endsection

@section('scripts')
    <script>
        let unidad = @json(session('unidad'));
    </script>
    <script src="/assets/js/macromapa.js"></script>
    <script src="/assets/js/detallemapa.js"></script>
    <script src="/assets/js/constructor.js"></script>

@endsection
