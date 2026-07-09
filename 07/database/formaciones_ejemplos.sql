-- Ejemplo completo: insertar, actualizar y consultar formaciones.
-- Ejecutar en la base de datos glpidb.

INSERT INTO glpi_plugin_formaciones_formaciones
    (name, description, state, date_creation, date_mod)
VALUES
    (
        'Docker para GLPI',
        'Uso de contenedores Docker para desplegar GLPI y MariaDB',
        1,
        NOW(),
        NOW()
    ),
    (
        'Administracion de GLPI',
        'Gestion de activos, usuarios, perfiles y plugins',
        1,
        NOW(),
        NOW()
    );

UPDATE glpi_plugin_formaciones_formaciones
SET
    description = 'Gestion de activos, usuarios, perfiles, plugins y reglas',
    date_mod = NOW()
WHERE name = 'Administracion de GLPI';

SELECT
    id,
    name,
    description,
    state,
    date_creation,
    date_mod
FROM glpi_plugin_formaciones_formaciones
ORDER BY id DESC;
