-- Consultas de ejemplo sobre la tabla del plugin Formaciones.

-- Listado completo ordenado por nombre.
SELECT
    id,
    name,
    description,
    state,
    date_creation,
    date_mod
FROM glpi_plugin_formaciones_formaciones
ORDER BY name, id;

-- Buscar formaciones cuya descripcion contenga una palabra.
SELECT
    id,
    name,
    description,
    state,
    date_creation,
    date_mod
FROM glpi_plugin_formaciones_formaciones
WHERE description LIKE '%GLPI%'
ORDER BY id DESC;

-- Contar registros activos e inactivos.
SELECT
    state,
    COUNT(*) AS total
FROM glpi_plugin_formaciones_formaciones
GROUP BY state;
