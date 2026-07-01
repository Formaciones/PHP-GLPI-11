<?php
declare(strict_types=1);

/**
 * Autoloader PSR-4 simplificado para el prefijo App\.
 */
spl_autoload_register(static function (string $class): void {
    $prefix = 'App\\';
    $baseDir = __DIR__ . '/src/';

    // Si la clase no comienza por el prefijo configurado, no se procesa.
    if (strncmp($class, $prefix, strlen($prefix)) !== 0) {
        return;
    }

    // Convierte App\Northwind\Customer en src/Northwind/Customer.php
    $relativeClass = substr($class, strlen($prefix));
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

    if (is_file($file)) {
        require $file;
    }
});