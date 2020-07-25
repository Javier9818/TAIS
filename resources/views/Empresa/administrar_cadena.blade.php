@extends('layouts.my_app')
@section('title')
    Empresa - Panel Administrativo
@endsection

@section('sidenav')
<x-side-bar tab="2" selected='1' empresa="{{$empresa->id}}"/>
@endsection

@section('content')
<div class="container">
    <h5 class="card-title text-left mt-4">Empresa "{{$empresa->nombre}}"</h5>
    <hr>
    <componente-administrar-cadena></componente-administrar-cadena>
</div>
@endsection

@section('scripts')
    <script>
        let empresa = @json($empresa);
        let unidades_negocio = @json($unidades_negocio);
        let proveedoresAll = @json($proveedores);
        let clientesAll = @json($clientes);
        console.log(proveedoresAll);
        console.log(clientesAll);
    </script>
@endsection
