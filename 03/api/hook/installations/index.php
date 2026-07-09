<?php

declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

$dbHost = '127.0.0.1';
$dbName = 'demoglpi';
$dbUser = 'dbuser';
$dbPass = 'dbpass';

try {
    $pdo = new PDO(
        "mysql:host=$dbHost;dbname=$dbName;charset=utf8mb4",
        $dbUser,
        $dbPass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );

    /////////////////////////////////////////////////////////
    // Utiliza en demostración de los Webhooks
    /////////////////////////////////////////////////////////
    
    // $apiKey = $_SERVER['HTTP_APIKEY'] ?? '';

    // if($apiKey !== '1234567890.') {
    //     http_response_code(401);

    //     echo json_encode([
    //         'ok' => false,
    //         'message' => 'No autorizado.'
    //     ]);

    //     exit;
    // }

    $rawBody = file_get_contents('php://input');

    if ($rawBody === false || trim($rawBody) === '') {
        http_response_code(400);

        echo json_encode([
            'ok' => false,
            'message' => 'No se ha recibido contenido JSON'
        ]);

        exit;
    }

    $json = json_decode($rawBody, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        http_response_code(400);

        echo json_encode([
            'ok' => false,
            'message' => 'JSON no válido',
            'error' => json_last_error_msg()
        ]);

        exit;
    }

    $sql = "INSERT INTO hook_logs (data) VALUES (:data)";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ':data' => json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
    ]);

    http_response_code(201);

    echo json_encode([
        'ok' => true,
        'message' => 'Datos recibidos correctamente',
        'id' => $pdo->lastInsertId()
    ]);
} catch (Throwable $e) {
    http_response_code(500);

    echo json_encode([
        'ok' => false,
        'message' => 'Error interno',
        'error' => $e->getMessage()
    ]);
}