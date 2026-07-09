# Formaciones

Plugin para GLPI 11 que anade un inventario de formaciones dentro del menu Activos.

Este README resume la finalidad de cada parte del plugin para que los alumnos
puedan orientarse antes de leer el codigo.

## Campos

- ID
- Nombre
- Tipo
- Instructor
- Descripcion
- Estado

El campo Tipo se mantiene desde `Configuracion > Desplegables` como
`Tipo - Formaciones`. El campo Instructor apunta a registros propios del plugin
disponibles desde el menu Activos.

## Estructura

- `setup.php`: fichero que GLPI lee para detectar, instalar, desinstalar y registrar el plugin.
- `hook.php`: fichero reservado para hooks y relaciones de base de datos.
- `inc/formacion.class.php`: clase principal del objeto Formacion; contiene formulario, estados y definicion de busqueda.
- `inc/instructor.class.php`: clase del objeto Instructor; contiene nombre, apellidos y numero de matricula.
- `inc/tipoformacion.class.php`: desplegable `Tipo - Formaciones`.
- `inc/profile.class.php`: clase auxiliar para preparar permisos de perfiles. NO SE USAN, DEFINIDOS COMO EJEMPLO.
- `front/formacion.php`: pagina de listado de formaciones.
- `front/formacion.form.php`: pagina que procesa altas, ediciones, borrados y muestra el formulario.
- `front/instructor.php`: pagina de listado de instructores.
- `front/instructor.form.php`: pagina que procesa altas, ediciones, borrados y muestra el formulario de instructores.
- `sql/install.sql`: SQL de referencia para estudiar la tabla.
- `pics/formaciones.svg`: icono visual del plugin.

## Instalacion

1. Copiar la carpeta `formaciones` dentro del directorio `plugins` de GLPI.
2. Entrar en GLPI como administrador.
3. Ir a `Configuracion > Plugins`.
4. Instalar y activar el plugin `Formaciones`.

El instalador crea las tablas `glpi_plugin_formaciones_formaciones`,
`glpi_plugin_formaciones_instructors` y
`glpi_plugin_formaciones_tipoformacions`.

## Flujo basico

1. GLPI carga `setup.php`.
2. `plugin_init_formaciones()` registra el menu en Activos.
3. Al pulsar en Formaciones se abre `front/formacion.php`.
4. Al pulsar Anadir o un registro existente se abre `front/formacion.form.php`.
5. El formulario usa la clase `PluginFormacionesFormacion` para validar permisos y guardar datos.
