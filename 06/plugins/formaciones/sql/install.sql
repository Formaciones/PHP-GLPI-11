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
  -- Primer dia de imparticion de la formacion.
  `begin_date` date DEFAULT NULL,
  -- Ultimo dia de imparticion de la formacion.
  `end_date` date DEFAULT NULL,
  -- Capacidad maxima; unsigned impide valores negativos en base de datos.
  `number_places` int unsigned NOT NULL DEFAULT 0,
  -- Fecha de creacion registrada por GLPI.
  `date_creation` timestamp NULL DEFAULT NULL,
  -- Fecha de ultima modificacion registrada por GLPI.
  `date_mod` timestamp NULL DEFAULT NULL,
  -- Clave primaria usada por GLPI para identificar registros.
  PRIMARY KEY (`id`),
  -- Indice para acelerar busquedas por nombre.
  KEY `name` (`name`),
  -- Indice para acelerar filtros por estado.
  KEY `state` (`state`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
