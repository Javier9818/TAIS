@extends('layouts.app')
@section('title')
    Usuarios - Panel General
@endsection

@section('content')
<header>
    <x-side-bar-super tab="2" selected='1'/>
</header>

<div class="container">
    <div class="row justify-content-center align-items-center mt-3">
        <h5 class="card-title text-left">Usuarios registrados</h5>
        <table-users></table-users>
    </div>
</div>
@endsection

@section('scripts')
    <script>
        let users = @json($users);
        let empresas = @json($empresas);
        let scopes = @json($scopes);
        console.log(empresas);
        console.log(users);
    </script>
@endsection
