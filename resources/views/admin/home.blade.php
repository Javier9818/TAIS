@extends('layouts.my_app')
@section('title')
    Empresas - Panel General
@endsection

@section('sidenav')
<x-side-bar-super tab="1" selected='1'/>
@endsection

@section('content')
<div class="container">
    <div class="row justify-content-center align-items-center mt-3">
        <h5 class="card-title text-left">Empresas registradas</h5>
        <table-empresa></table-empresa>
    </div>
</div>
@endsection

@section('scripts')
    <script>
        let empresas = @json($empresas);
    </script>
@endsection
