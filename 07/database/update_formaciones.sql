-- Actualizaciones de ejemplo sobre la tabla del plugin Formaciones.

-- Actualiza la descripcion de una formacion por nombre.
UPDATE glpi_plugin_formaciones_formaciones
SET
    description = 'Curso actualizado desde SQL directo en MariaDB',
    date_mod = NOW()
WHERE name = 'PHP + GLPI';

-- Desactiva una formacion.
UPDATE glpi_plugin_formaciones_formaciones
SET
    state = 0,
    date_mod = NOW()
WHERE name = 'SQL y Bases de Datos';

-- Reactiva una formacion.
UPDATE glpi_plugin_formaciones_formaciones
SET
    state = 1,
    date_mod = NOW()
WHERE name = 'SQL y Bases de Datos';
