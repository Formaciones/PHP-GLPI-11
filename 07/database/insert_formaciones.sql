-- Inserta registros de ejemplo en la tabla del plugin Formaciones.
-- Base de datos: glpidb
-- Tabla: glpi_plugin_formaciones_formaciones

INSERT INTO glpi_plugin_formaciones_formaciones
    (name, description, state, date_creation, date_mod)
VALUES
    (
        'PHP + GLPI',
        'Curso PHP para extension y customizacion de GLPI',
        1,
        NOW(),
        NOW()
    ),
    (
        'SQL y Bases de Datos',
        'Fundamentos de SQL y optimizacion de consultas',
        1,
        NOW(),
        NOW()
    ),
    (
        'API REST de GLPI',
        'Integracion con GLPI usando endpoints REST y tokens de sesion',
        1,
        NOW(),
        NOW()
    );
