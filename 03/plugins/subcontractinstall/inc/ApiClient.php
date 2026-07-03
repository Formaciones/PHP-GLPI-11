<?php

namespace Plugin\SubcontractInstall;

use CommonDBTM;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class ApiClient
{
    public static function sendComputer(CommonDBTM $computer, string $event): void
    {
        // Cliente HTTP de Guzzle apuntando al endpoint local de pruebas.
        $client = new Client([
            'base_uri' => 'http://host.docker.internal/labs/03/api/hook/',
            'timeout' => 15
        ]);

        // Datos que enviaremos a la API externa en formato JSON.
        $payload = [
            'event'     => $event,
            'id'        => $computer->fields['id'] ?? null,
            'name'      => $computer->fields['name'] ?? '',
            'serial'    => $computer->fields['serial'] ?? '',
            'inventory' => $computer->fields['otherserial'] ?? ''
        ];

        \Toolbox::logInFile(
            'subcontractinstall',
            'JSON prepara para enviar: ' . json_encode($payload));

        try {
            $response = $client->post(
                'installations/',
                [
                    'headers' => [
                        //'User-Agent'        => 'GLPI-Plugin-Client/1.0',
                        'Authorization'     => 'Bearer 123456',
                        'Content-Type'      => 'application/json; charset=utf-8',
                        'Accept'            => 'application/json',
                        //'Content-Length'    => (string)strlen(json_encode($payload)),
                        //'Connection'        => 'keep-alive'
                    ],
                    'json' => $payload
                ]
            );

            \Toolbox::logInFile(
                'subcontractinstall',
                'Datos de equipo enviado correctamente.');
        } catch (GuzzleException $e) {
            \Toolbox::logInFile(
                'subcontractinstall',
                $e->getMessage() . '\n');
        }
    }
}