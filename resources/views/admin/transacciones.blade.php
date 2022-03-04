@extends('layouts.my_app')
@section('title')
    Transacciones - Panel General
@endsection

@section('sidenav')
<x-side-bar-super tab="3" selected='1'/>
@endsection

@section('content')
<div class="container">
    <div class="row justify-content-center align-items-center mt-3">
        <h5 class="card-title text-left">Trasacciones registradas</h5>
        <table-transacciones></table-transacciones>
    </div>
</div>
@endsection

@section('scripts')
    <script>
        let transacciones = @json($transacciones);
        let acciones = @json($acciones);
    </script>
@endsection
