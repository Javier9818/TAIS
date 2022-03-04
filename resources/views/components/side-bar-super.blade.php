<!-- Sidebar  -->
<nav id="sidebar">
    <div class="sidebar-header">
        <h3>Cadena de Suministro</h3>
        <strong>JB</strong>
    </div>

    <ul class="list-unstyled components">
        <li>
            <a href="/home" class="{{ $isTab('1') ? 'active' : '' }}">
                <i class="fas fa-briefcase"></i>
                Empresas
            </a>

            <a href="/users"  class="{{ $isTab('2') ? 'active' : '' }}">
                <i class="fas fa-briefcase"></i>
                Usuarios
            </a>

            <a href="/transacciones"  class="{{ $isTab('3') ? 'active' : '' }}">
                <i class="fas fa-briefcase"></i>
                Transacciones
            </a>
            
        </li>
    </ul>

</nav>