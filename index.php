<?php 
    $page = "home";
?>

<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PHP + GLPI 11 | Home</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/labs/css/style.css" rel="stylesheet">
</head>
<body>
    <div class="layout d-flex">
        <?php
            include_once __DIR__ . '/layout/aside.php';
        ?>

        <main class="flex-grow-1 p-4 p-lg-5">
            <div class="container-fluid">
                <header class="mb-4">
                    <h1 class="display-6 fw-bold mb-3">Proyecto de ejercicios y demos para PHP en GLPI 11</h1>
                    <hr class="border-secondary-subtle opacity-100">
                </header>

                <section class="welcome-panel bg-white p-4 p-lg-5 shadow-sm">
                    <div class="logo-strip d-flex flex-column flex-md-row align-items-center justify-content-center gap-4 gap-md-5 mb-4">
                        <img src="https://www.php.net/images/logos/new-php-logo.svg" alt="Logo de PHP">
                        <img src="https://comptoir-du-libre.org/img/files/Softwares/GLPI/avatar/logo-glpi-bleu-1.png" alt="Logo de GLPI">
                    </div>

                    <div class="text-center mx-auto" style="max-width: 720px;">
                        <p class="lead mb-2">Ejercicios y Demos del curso</p>
                        <p class="text-secondary mb-0">
                            Índice de prácticas para la formación de PHP orientada a GLPI 11.
                        </p>
                    </div>
                </section>
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
