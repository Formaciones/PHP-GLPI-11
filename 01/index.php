<?php 
    $page = "demos";
?>

<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PHP + GLPI 11 | PHP Demo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/labs/css/style.css" rel="stylesheet">
</head>
<body>
    <div class="layout d-flex">
        <?php
            include_once __DIR__ . '/../layout/aside.php';
        ?>

        <main class="flex-grow-1 p-4 p-lg-5">
            <div class="container-fluid">
                <header class="mb-4">
                    <h1 class="display-6 fw-bold mb-3">PHP Demos</h1>
                    <hr class="border-secondary-subtle opacity-100">
                </header>

                <section class="welcome-panel bg-white p-4 p-lg-5 shadow-sm">
                    <h3>Variables, tipos y conversiones</h3>
                    <hr />
                    <?php 
                        // Comentarios de una línea
                        # Comentarios de una línea
                        /*
                            Comenytario en bloque
                        */

                        $usuario = "Ana Sanz   "; 
                        $edad = 28;
                        $altura = 1.82;
                        $activo = true;
                        $sinValor = null;      
                        
                        $textonumero = "42AA1";
                        $entero = (int) $textonumero;
                        $suma = 10 + $entero;

                        const APP_VERSION = "1.0.0";
                        define("APP_NAME", "Demos PHP + GLPI");
                    ?>
                    <!-- Opcion 1 -->
                    <?php echo '<p><b>Usuario:</b> ' . $usuario . '</p>' ?>
                    <!-- Opcion 2 -->
                    <?php echo "<p><b>Usuario:</b> $usuario</p>" ?>
                    <!-- Opcion 3 -->
                    <p><b>Usuario:</b> <?= $usuario ?></p>
                    <br />
                    <p><b>Tipo de Edad:</b> <?= gettype($edad); ?></p>
                    <p><b>Tipo de Activo:</b> <?= gettype($activo); ?></p>
                    <p><b>Tipo y Valor de Nombre:</b> <?= var_dump($usuario); ?></p>
                    <br />
                    <p><b>is_int($edad)</b> <?= is_int($edad); ?></p>
                    <p><b>is_float($altura)</b> <?= is_float($altura); ?></p>
                    <p><b>is_nan($edad)</b> <?= is_nan($edad); ?></p>
                    <p><b>is_numeric($edad)</b> <?= is_numeric($edad); ?></p>
                    <p><b>is_numeric($usuario)</b> <?= is_numeric($usuario); ?></p>
                    <p><b>is_numeric($activo)</b> <?= is_numeric($activo); ?></p>
                    <br />
                    <p><b>Suma "<?= $textonumero ?>" + 10 = <?= $suma ?></b></p>
                    <br />
                    <p><b>Constante APP_VERSION:</b> <?= APP_VERSION ?></p>
                    <p><b>Constante APP_NAME:</b> <?= APP_NAME ?></p>
                    <br />
                    <p><b><?php echo "\"Hola\" - C:\\xampp - \n Salto de Linea" ?></b></p>
                    <p><b>strlen($usuario):</b> <?= strlen($usuario) ?></p>
                    <p><b>trim($usuario):</b> <?= trim($usuario) ?></p>
                    <p><b>strlen(trim($usuario)):</b> <?= strlen(trim($usuario)) ?></p>
                    <p><b>$usuario[2]:</b> <?= $usuario[2] ?></p>
                    <br />
                    <?php 
                        $matricula1 = 'A7716 CD';
                        $matricula2 = '1234ABC';
                        $patron = '/^[0-9]{4}[A-Z]{3}/i'                    
                    ?>
                    <p><b>Patrón:</b> <?= $patron ?></p>
                    <p><b>Matricula <?= $matricula1 ?>:</b> <?= preg_match($patron, $matricula1) ? 'true' : 'false' ?></p>
                    <p><b>Matricula <?= $matricula2 ?>:</b> <?= preg_match($patron, $matricula2) ? 'true' : 'false' ?></p>
                </section>
                <br />
                <section class="welcome-panel bg-white p-4 p-lg-5 shadow-sm">
                    <br />
                    <?php phpinfo(); ?>    
                </section>
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
