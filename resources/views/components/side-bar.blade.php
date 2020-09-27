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
                @can('gestionar-clientes')
                    <!-- <li>
                        <a href="/empresa/{{$empresa}}/clientes" class="{{ $isSelected('1', '2') ? 'active' : '' }}">Clientes</a>
                    </li> -->
                @endcan
                @can('gestionar-proveedores')
                    <!-- <li>
                        <a href="/empresa/{{$empresa}}/proveedores" class="{{ $isSelected('1', '3') ? 'active' : '' }}">Proveedores</a>
                    </li> -->
                @endcan
                @can('gestionar-unidades-negocio')
                    <li>
                        <a href="/empresa/{{$empresa}}/unidades-negocio" class="{{ $isSelected('1', '4') ? 'active' : '' }}">Unidades de negocio</a>
                    </li>
                @endcan
            </ul>

           
                <a href="#pageSubmenu2" data-toggle="collapse" aria-expanded="{{ $isTab('2') ? 'true' : 'false' }}" class="dropdown-toggle">
                    <i class="fas fa-copy"></i>
                    Cadena
                </a>
                <ul class="collapse list-unstyled {{ $isTab('2') ? 'show' : '' }}" id="pageSubmenu2">
                    @can('gestionar-procesos')
                    <li>
                        <a href="/empresa/{{$empresa}}/procesos" class="{{ $isSelected('2', '1') ? 'active' : '' }}">Procesos</a>
                    </li>
                    @endcan
                    @can('gestionar-mapa-procesos')
                    <li>
                        <a href="/empresa/{{$empresa}}/mapa-procesos" class="{{ $isSelected('2', '2') ? 'active' : '' }}">Mapa de procesos</a>
                    </li>
                    @endcan
                    @can('gestionar-priorizacion')
                    <li>
                        <a href="/empresa/{{$empresa}}/priorizacion" class="{{ $isSelected('2', '3') ? 'active' : '' }}">Priorización de procesos</a>
                    </li>
                    @endcan
                    @can('gestionar-caracterizacion')
                    <li>
                        <a href="/empresa/{{$empresa}}/caracterizacion" class="{{ $isSelected('2', '4') ? 'active' : '' }}">Caracterizacion</a>
                    </li>
                    @endcan
                    @can('gestionar-diagrama-flujo')
                    <li>
                        <a href="/empresa/{{$empresa}}/diagrama-flujo" class="{{ $isSelected('2', '5') ? 'active' : '' }}">Diagrama de flujo</a>
                    </li>
                    @endcan
                    @can('gestionar-diagrama-seguimiento')
                    <li>
                        <a href="/empresa/{{$empresa}}/diagrama-seguimiento" class="{{ $isSelected('2', '6') ? 'active' : '' }}">Diagrama de seguimiento</a>
                    </li>
                    @endcan
                    @can('gestionar-diagrama-seguimiento')
                    <li>
                        <a href="/empresa/{{$empresa}}/gestion-indicadores" class="{{ $isSelected('2', '7') ? 'active' : '' }}">Gestión de indicadores</a>
                    </li>
                    @endcan
                    <!-- <li>
                        <a href="/empresa/{{$empresa}}/administrar-cadena" class="{{ $isSelected('2', '1') ? 'active' : '' }}">Administrar</a>
                    </li>
                    <li>
                        <a href="/empresa/{{$empresa}}/generar-cadena" class="{{ $isSelected('2', '2') ? 'active' : '' }}">Generar</a>
                    </li> -->
                </ul> 
            
        </li>
    </ul>

</nav>