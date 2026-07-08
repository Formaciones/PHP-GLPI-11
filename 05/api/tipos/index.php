
<?php
/**
 * Microservicio ficticio: lista de tipos de formación
 *
 * Este endpoint devuelve una colección JSON con objetos que contienen
 * los campos `codigo` y `descripcion`.
 *
 * Ejemplos de prueba (desde la máquina host con XAMPP):
 *   curl -i http://localhost/labs/05/api/tipos/
 *
 * Desde un contenedor Docker (usar esta URL dentro del contenedor):
 *   curl -i http://host.docker.internal/labs/05/api/tipos/
 *
 * Respuesta ejemplo:
 * [
 *   {"codigo": "PR", "descripcion": "Presencial"},
 *   {"codigo": "ON", "descripcion": "Online"}
 * ]
 *
 * Comentarios para alumnos:
 * - `codigo`: identificador corto del tipo (2-3 letras).
 * - `descripcion`: texto legible para mostrar en interfaces.
 * - En entornos reales, la lista provendría de una BD o servicio.
 */

header('Content-Type: application/json; charset=utf-8');
// Permitimos CORS para facilitar pruebas desde otros orígenes (solo para desarrollo)
header('Access-Control-Allow-Origin: *');

$tipos = [
	['codigo' => 'PR', 'descripcion' => 'Presencial'],
	['codigo' => 'ON', 'descripcion' => 'Online'],
	['codigo' => 'HY', 'descripcion' => 'Híbrido'],
	['codigo' => 'BL', 'descripcion' => 'Blended']
];

// Devolver la colección como JSON. En producción no usar JSON_PRETTY_PRINT.
echo json_encode($tipos, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

?>

