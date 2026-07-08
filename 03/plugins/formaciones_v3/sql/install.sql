-- SQL de referencia para entender la tabla del plugin.
-- La instalacion real se realiza desde setup.php cuando GLPI instala el plugin.

-- Tabla principal del plugin Formaciones.
CREATE TABLE `glpi_plugin_formaciones_formaciones` (
  -- Identificador interno autoincremental de cada formacion.
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  -- Nombre corto de la formacion.
  `name` varchar(255) NOT NULL DEFAULT '',
  -- Tipo de formacion mantenido desde Configuracion > Desplegables.
  `plugin_formaciones_tipoformacions_id` int unsigned NOT NULL DEFAULT 0,
  -- Instructor relacionado con la formacion.
  `plugin_formaciones_instructors_id` int unsigned NOT NULL DEFAULT 0,
  -- Descripcion larga de la formacion.
  `description` text,
  -- Estado de la formacion: 1 = Activo, 0 = Inactivo.
  `state` tinyint NOT NULL DEFAULT 1,
  -- Fecha de creacion registrada por GLPI.
  `date_creation` timestamp NULL DEFAULT NULL,
  -- Fecha de ultima modificacion registrada por GLPI.
  `date_mod` timestamp NULL DEFAULT NULL,
  -- Clave primaria usada por GLPI para identificar registros.
  PRIMARY KEY (`id`),
  -- Indice para acelerar busquedas por nombre.
  KEY `name` (`name`),
  -- Indice para acelerar filtros por tipo.
  KEY `plugin_formaciones_tipoformacions_id` (`plugin_formaciones_tipoformacions_id`),
  -- Indice para acelerar filtros por instructor.
  KEY `plugin_formaciones_instructors_id` (`plugin_formaciones_instructors_id`),
  -- Indice para acelerar filtros por estado.
  KEY `state` (`state`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de instructores del plugin.
CREATE TABLE `glpi_plugin_formaciones_instructors` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  -- Apellidos del instructor. Se usa como campo name para GLPI.
  `name` varchar(255) NOT NULL DEFAULT '',
  -- Nombre del instructor.
  `firstname` varchar(255) NOT NULL DEFAULT '',
  -- Numero de matricula del instructor.
  `registration_number` varchar(255) NOT NULL DEFAULT '',
  `date_creation` timestamp NULL DEFAULT NULL,
  `date_mod` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `name` (`name`),
  KEY `firstname` (`firstname`),
  KEY `registration_number` (`registration_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Desplegable Tipo - Formaciones.
CREATE TABLE `glpi_plugin_formaciones_tipoformacions` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `entities_id` int unsigned NOT NULL DEFAULT 0,
  `is_recursive` tinyint NOT NULL DEFAULT 0,
  `name` varchar(255) NOT NULL DEFAULT '',
  `comment` text DEFAULT NULL,
  `date_creation` timestamp NULL DEFAULT NULL,
  `date_mod` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `name` (`name`),
  KEY `entities_id` (`entities_id`),
  KEY `is_recursive` (`is_recursive`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
