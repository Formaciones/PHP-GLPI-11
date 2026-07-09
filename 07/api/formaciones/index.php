
<?php
/**
 * Microservicio ficticio: listado de formaciones disponibles
 *
 * Este endpoint devuelve una colección JSON con objetos que contienen
 * los campos `name` y `description`. Retorna 5 formaciones seleccionadas
 * aleatoriamente de un conjunto total de 10.
 *
 * Ejemplos de prueba (desde la máquina host con XAMPP):
 *   curl -i http://localhost/labs/07/api/formaciones/
 *
 * Desde un contenedor Docker (usar esta URL dentro del contenedor):
 *   curl -i http://host.docker.internal/labs/07/api/formaciones/
 *
 * Respuesta ejemplo:
 * [
 *   {"name": "PHP + GLPI", "description": "Cursos PHP para la extensión y customización de GLPI"},
 *   {"name": "SQL y Bases de Datos", "description": "Fundamentos de SQL y optimización de queries"}
 * ]
 *
 * Comentarios para alumnos:
 * - `name`: nombre de la formación.
 * - `description`: descripción detallada de los contenidos.
 * - En cada petición se devuelven 5 formaciones elegidas aleatoriamente.
 * - En entornos reales, la lista provendría de una BD o servicio.
 */

header('Content-Type: application/json; charset=utf-8');
// Permitimos CORS para facilitar pruebas desde otros orígenes (solo para desarrollo)
header('Access-Control-Allow-Origin: *');

// Total de formaciones disponibles (10)
$todasFormaciones = [
	['name' => 'PHP + GLPI', 'description' => 'Cursos PHP para la extensión y customización de GLPI'],
	['name' => 'SQL y Bases de Datos', 'description' => 'Fundamentos de SQL y optimización de queries en sistemas de gestión'],
	['name' => 'JavaScript Avanzado', 'description' => 'Técnicas modernas de JavaScript para aplicaciones web interactivas'],
	['name' => 'REST APIs', 'description' => 'Diseño e implementación de APIs REST escalables y seguras'],
	['name' => 'Docker y Contenedores', 'description' => 'Introducción a Docker y orquestación de contenedores con Docker Compose'],
	['name' => 'Angular Framework', 'description' => 'Desarrollo de aplicaciones empresariales con Angular framework'],
	['name' => 'Git y Control de Versiones', 'description' => 'Workflow colaborativo con Git y mejores prácticas en repositorios'],
	['name' => 'Seguridad en Aplicaciones Web', 'description' => 'Prácticas de seguridad OWASP y protección contra vulnerabilidades'],
	['name' => 'Testing Automatizado', 'description' => 'Unit testing, integración testing y testing e2e en aplicaciones modernas'],
	['name' => 'DevOps y CI/CD', 'description' => 'Pipelines de integración continua y entrega continua con herramientas modernas']
];

// Seleccionar 5 formaciones aleatoriamente del total de 10
$indicesAleatorios = array_rand($todasFormaciones, 5);
$formacionesSeleccionadas = array_map(function($indice) use ($todasFormaciones) {
	return $todasFormaciones[$indice];
}, $indicesAleatorios);

// Devolver la colección como JSON. En producción no usar JSON_PRETTY_PRINT.
echo json_encode($formacionesSeleccionadas, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

?>

