-- SQL de referencia para entender la tabla del plugin.
-- La instalacion real se realiza desde setup.php cuando GLPI instala el plugin.

-- Tabla principal del plugin Formaciones.
CREATE TABLE `glpi_plugin_formaciones_formaciones` (
  -- Identificador interno autoincremental de cada formacion.
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  -- Nombre corto de la formacion.
  `name` varchar(255) NOT NULL DEFAULT '',
  -- Descripcion larga de la formacion.
  `description` text,
  -- Estado de la formacion: 1 = Activo, 0 = Inactivo.
  `state` tinyint NOT NULL DEFAULT 1,
  -- Formato de imparticion: online o presencial.
  `format` varchar(20) NOT NULL DEFAULT 'online',
  -- Formador asignado. En la demo se elige desde una lista ficticia.
  `trainer` varchar(100) NOT NULL DEFAULT '',
  -- Fecha prevista de inicio.
  `start_date` date DEFAULT NULL,
  -- Fecha prevista de fin.
  `end_date` date DEFAULT NULL,
  -- Duracion total en horas.
  `duration_hours` decimal(6,2) NOT NULL DEFAULT 0,
  -- Numero maximo de plazas disponibles.
  `capacity` int NOT NULL DEFAULT 0,
  -- Coste estimado de la formacion.
  `cost` decimal(10,2) NOT NULL DEFAULT 0,
  -- Moneda del coste.
  `currency` varchar(3) NOT NULL DEFAULT 'EUR',
  -- Aula, sala o ubicacion fisica.
  `location` varchar(255) NOT NULL DEFAULT '',
  -- Enlace de reunion si la formacion es online.
  `meeting_url` varchar(255) NOT NULL DEFAULT '',
  -- Nivel de dificultad.
  `level` varchar(50) NOT NULL DEFAULT 'inicial',
  -- Publico objetivo o departamento destinatario.
  `target_audience` varchar(255) NOT NULL DEFAULT '',
  -- Indica si la formacion emite certificado: 1 = si, 0 = no.
  `certificate` tinyint NOT NULL DEFAULT 1,
  -- Notas internas de organizacion.
  `observations` text,
  -- Fecha de creacion registrada por GLPI.
  `date_creation` timestamp NULL DEFAULT NULL,
  -- Fecha de ultima modificacion registrada por GLPI.
  `date_mod` timestamp NULL DEFAULT NULL,
  -- Clave primaria usada por GLPI para identificar registros.
  PRIMARY KEY (`id`),
  -- Indice para acelerar busquedas por nombre.
  KEY `name` (`name`),
  -- Indice para acelerar filtros por estado.
  KEY `state` (`state`),
  -- Indices utiles para la demo de busqueda.
  KEY `format` (`format`),
  KEY `trainer` (`trainer`),
  KEY `start_date` (`start_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
