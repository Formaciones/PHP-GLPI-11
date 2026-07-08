<?php

namespace Plugin\SubcontractInstall;

use CommonDBTM;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class ApiClient
{
    public static function sendComputer(CommonDBTM $computer, string $event): void
    {
        $client = new Client([
            'base_uri' => Config::API_BASE_URL,
            'timeout'  => Config::TIMEOUT,
            'verify'   => Config::VERIFY_SSL
        ]);

        $payload = [
            'event'     => $event,
            'id'        => $computer->fields['id'] ?? null,
            'name'      => $computer->fields['name'] ?? '',
            'serial'    => $computer->fields['serial'] ?? '',
            'inventory' => $computer->fields['otherserial'] ?? ''
        ];

        try {

            $response = $client->post('', [
                'headers' => [
                    'Accept' => 'application/json',
                    'X-API-Key' => Config::API_KEY
                ],
                'json' => $payload
            ]);

            Logger::info(
                'Equipo enviado. HTTP '
                . $response->getStatusCode()
            );

        }
        catch (GuzzleException $e) {
            Logger::error($e->getMessage());
        }
    }
}
