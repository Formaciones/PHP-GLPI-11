<?php

    require __DIR__ . '/vendor/autoload.php';

    use GuzzleHttp\Client;
    use GuzzleHttp\Exception\RequestException;

    $baseUrl = 'http://localhost:8080/apirest.php';
    //$baseUrl = 'http://localhost:8080/api.php/v2.3';

    // Token de aplicación generado en GLPI
    $appToken = 'vHh73xn031pjuY0SxwV7qq1WJK4JuB3TvYeWUuM5';
    $userToken = 'TDozUhL4x97J8OXvrNEjNjcvDYAASKvfP9jAlJst';

    $client = new Client([
        'base_uri' => $baseUrl . '/',
        'timeout'  => 10,
    ]);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GLPI Test API Connection</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>
<body>
    <br />
    <div class="container">
        <div class="col"> </div>
        <div class="col">
            <h1>GLPI Test Connection</h1>
            <hr />
            <pre style="font-size:130%">
<?php 

    try {
        echo "1) Iniciando sesión en GLPI..." ;
        echo "<br />";
        echo "<ul>";

        $response = $client->get('initSession', [
            'headers' => [
                'Content-Type'  => 'application/json',
                'App-Token'    => $appToken,
                'Authorization' => 'user_token ' . $userToken,
            ],
        ]);
        

        $data = json_decode($response->getBody()->getContents(), true);
        $sessionToken = $data['session_token'];
        
        echo "<li>Sesión iniciada correctamente.</li>";
        echo "<li>Session-Token:</li>";
        echo '<textarea class="form-control">' . $sessionToken . '</textarea>';
        echo "</ul>";
        echo "<br />";

        echo "2) Consultando ordenadores..." ;
        echo "<br />";
        echo "<ul>";

        $response = $client->get('Computer', [
            'headers' => [
                'Content-Type'      => 'application/json',
                'App-Token'         => $appToken,
                'Session-Token'     => $sessionToken,
            ],
            'query' => [
                'range' => '0-10',
            ],
        ]);

        $computers = json_decode($response->getBody()->getContents(), true);
        foreach($computers as $item) {
            echo "<li>" . $item["name"] . "</li>";
        }
        
        echo "</ul>";
        echo "<br />";

        echo "3) Cerrando sesión..." ;
        echo "<ul>";
        
        $client->get('killSession', [
            'headers' => [
                'Content-Type'   => 'application/json',
                'App-Token'     => $appToken,
                'Session-Token' => $sessionToken,
            ],
        ]);

        echo "<li>Sesión cerrada correctamente.</li>" ;
        echo "</ul>";

    } catch (RequestException $e) {
        echo "Error HTTP:" ;

        if ($e->hasResponse()) {
            echo $e->getResponse()->getStatusCode() . "<br />";
            echo $e->getResponse()->getBody()->getContents() . "<br />";
        } else {
            echo $e->getMessage() . "<br />";
        }
    }

?>
            </pre>
        </div>
        <div class="col"> </div>
    </div>
</body>
</html>