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
            </ul>
        </li>
    </ul>

</nav>