@extends('layouts.app')
@section('title')
    Empresas - Panel General
@endsection

@section('content')
<header>
    <x-side-bar-super tab="1" selected='1'/>
</header>

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
