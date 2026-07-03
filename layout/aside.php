<?php
    $baseUrl = '/labs';
?>

        <aside class="sidebar text-white p-4">
            <div class="d-flex align-items-center gap-3 mb-4">
                <span class="brand-mark">F</span>
                <div>
                    <div class="fw-semibold">PHP + GLPI</div>
                    <small class="text-white-50">Ejercicios y demos</small>
                </div>
            </div>

            <nav class="nav nav-pills flex-column gap-1">
                <a class="nav-link <?php echo $page === 'home' ? 'active' : ''; ?>" href="<?php echo $baseUrl; ?>/">Home</a>
                <a class="nav-link <?php echo $page === 'demos' ? 'active' : ''; ?>" href="<?php echo $baseUrl; ?>/01/">PHP Variables</a>                
                <a class="nav-link <?php echo $page === 'form' ? 'active' : ''; ?>" href="<?php echo $baseUrl; ?>/01/formulario.php">PHP Formulario</a>
                <a class="nav-link <?php echo $page === 'autoloadoff' ? 'active' : ''; ?>" href="<?php echo $baseUrl; ?>/02/">PHP Autoloading OFF</a>
                <a class="nav-link <?php echo $page === 'autoloadon' ? 'active' : ''; ?>" href="<?php echo $baseUrl; ?>/02/index_namespace.php">PHP Autoloading ON</a>
                <a class="nav-link <?php echo $page === 'poo1' ? 'active' : ''; ?>" href="<?php echo $baseUrl; ?>/02/poo_array.php">PHP POO - Array</a>
                <a class="nav-link <?php echo $page === 'poo2' ? 'active' : ''; ?>" href="<?php echo $baseUrl; ?>/02/poo_db.php">PHP POO - DB</a>
                <a class="nav-link <?php echo $page === 'hook-view' ? 'active' : ''; ?>" href="<?php echo $baseUrl; ?>/03/api/hook/view.php">GLPI Visor</a>
            </nav>
        </aside>
        
