<!-- Sidebar  -->
<nav id="sidebar">
    <div class="sidebar-header">
        <h3>Cadena de Suministro</h3>
        <strong>JB</strong>
    </div>

    <ul class="list-unstyled components">
        <li>
            <a href="#pageSubmenu" data-toggle="collapse" aria-expanded="{{ $isTab('1') ? 'true' : 'false' }}" class="dropdown-toggle">
                <i class="fas fa-copy"></i>
                Empresa
            </a>
            <ul class="collapse list-unstyled {{ $isTab('1') ? 'show' : '' }}" id="pageSubmenu">
                <li>
                    <a href="/empresa/{{$empresa}}" class="{{ $isSelected('1', '1') ? 'active' : '' }}">Información</a>
                </li>
                <li>
                    <a href="/empresa/{{$empresa}}/clientes" class="{{ $isSelected('1', '2') ? 'active' : '' }}">Clientes</a>
                </li>
                <li>
                    <a href="/empresa/{{$empresa}}/proveedores" class="{{ $isSelected('1', '3') ? 'active' : '' }}">Proveedores</a>
                </li>
                <li>
                    <a href="/empresa/{{$empresa}}/unidades-negocio" class="{{ $isSelected('1', '4') ? 'active' : '' }}">Unidades de negocio</a>
                </li>
            </ul>

            <a href="#pageSubmenu2" data-toggle="collapse" aria-expanded="{{ $isTab('2') ? 'true' : 'false' }}" class="dropdown-toggle">
                <i class="fas fa-copy"></i>
                Cadena de Suministro
            </a>
            <ul class="collapse list-unstyled {{ $isTab('2') ? 'show' : '' }}" id="pageSubmenu2">
                <li>
                    <a href="/empresa/{{$empresa}}/administrar-cadena" class="{{ $isSelected('2', '1') ? 'active' : '' }}">Administrar</a>
                </li>
                <li>
                    <a href="/empresa/{{$empresa}}/generar-cadena" class="{{ $isSelected('2', '2') ? 'active' : '' }}">Generar</a>
                </li>
            </ul>
        </li>
    </ul>

</nav>