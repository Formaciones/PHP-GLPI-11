<?php 
    $page = "autoloadoff";

    // Autoloading OFF: hacemos la cargar de las clases mediante require, require_once, include, include_one
    require_once __DIR__ . '/class/customer.php';
    require_once __DIR__ . '/class/product.php';

    $customer = new Customer(
        'DEMO1',
        'Empresa Uno, SL',
        'Borja Cabeza',
        'CEO',
        'Calle Uno, S/N',
        'Málaga',
        'Andalucia',
        'España',
        '900 100 200',
        '900 300 400'
    );

    $product = new Product(
        1, 
        'Producto Demostración',
        0,
        0,
        '',
        10.25,
        10,
        0,
        0,
        false
    );
?>

<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PHP + GLPI 11 | Autoloading OFF</title>
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
                    <h1 class="display-6 fw-bold mb-3">PHP Autoloading OFF</h1>
                    <hr class="border-secondary-subtle opacity-100">
                </header>

                <section class="welcome-panel bg-white p-4 p-lg-5 shadow-sm">
                    <div class="text-start mx-auto" style="max-width: 720px;">
                        <h2>Carga de clases manual sin Autoload PSR-4</h2>
                        <br />
                        <p><b>Instancia de Cliente</b></p>
                        <hr />
                        <p><b>Empresa:</b> <?= htmlspecialchars($customer->getCompanyName()) ?></p>
                        <p><b>País:</b> <?= htmlspecialchars($customer->getCountry()) ?></p>
                        <br />
                        <br />
                        <p><b>Instancia de Cliente</b></p>
                        <hr />
                        <p><b>Producto:</b> <?= htmlspecialchars($product->getProductName()) ?></p>
                        <p><b>Stock:</b> <?= htmlspecialchars((string) $product->getUnitsInStock()) ?></p>                        
                    </div>
                </section>
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
