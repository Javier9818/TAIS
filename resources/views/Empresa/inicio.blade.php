@extends('layouts.my_app')
@section('title')
    Empresa - Panel Administrativo
@endsection

@section('sidenav')
<x-side-bar tab="1" selected='1' empresa="{{$empresa->id}}"/>
@endsection

@section('content')
<div class="container">
    <div class="row justify-content-center align-items-center mt-3">
        <h5 class="card-title text-left">Empresa "{{$empresa->nombre}}"</h5>
    </div>
    <form-empresa edit='true'></form-empresa>
</div>
@endsection

@section('scripts')
    <script>
        let empresa = @json($empresa);
    </script>
@endsection
