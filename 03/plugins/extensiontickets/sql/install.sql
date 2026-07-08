-- SQL de referencia para entender la tabla del plugin.
-- La instalacion real se realiza desde setup.php cuando GLPI instala el plugin.

-- Tabla de campos extra para tickets.
CREATE TABLE `glpi_plugin_extensiontickets_ticketextensions` (
  -- Identificador interno autoincremental.
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  -- Ticket de GLPI al que pertenecen los datos extra.
  `tickets_id` int unsigned NOT NULL DEFAULT 0,
  -- Asignacion Externa: 1 = Si, 0 = No.
  `external_assignment` tinyint NOT NULL DEFAULT 0,
  -- Nombre de la empresa externa.
  `external_company` varchar(255) NOT NULL DEFAULT '',
  -- Coste asociado a la asignacion externa.
  `cost` decimal(20,2) NOT NULL DEFAULT 0.00,
  -- Fecha de creacion registrada por GLPI.
  `date_creation` timestamp NULL DEFAULT NULL,
  -- Fecha de ultima modificacion registrada por GLPI.
  `date_mod` timestamp NULL DEFAULT NULL,
  -- Clave primaria usada por GLPI.
  PRIMARY KEY (`id`),
  -- Solo debe existir un registro de extension por ticket.
  UNIQUE KEY `tickets_id` (`tickets_id`),
  -- Indice util para filtrar tickets con asignacion externa.
  KEY `external_assignment` (`external_assignment`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
