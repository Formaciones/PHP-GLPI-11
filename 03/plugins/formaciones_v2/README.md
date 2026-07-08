# Formaciones

Plugin para GLPI 11 que anade un inventario de formaciones dentro del menu Activos.

Este README resume la finalidad de cada parte del plugin para que los alumnos
puedan orientarse antes de leer el codigo.

## Campos

- ID
- Nombre
- Empresa
- Descripcion
- Estado

## Estructura

- `setup.php`: fichero que GLPI lee para detectar, instalar, desinstalar y registrar el plugin.
- `hook.php`: fichero reservado para hooks y relaciones de base de datos.
- `inc/formacion.class.php`: clase principal del objeto Formacion; contiene formulario, estados y definicion de busqueda.
- `inc/profile.class.php`: clase auxiliar para preparar permisos de perfiles.
- `front/formacion.php`: pagina de listado de formaciones.
- `front/formacion.form.php`: pagina que procesa altas, ediciones, borrados y muestra el formulario.
- `sql/install.sql`: SQL de referencia para estudiar la tabla.
- `pics/formaciones.svg`: icono visual del plugin.

## Instalacion

1. Copiar la carpeta `formaciones` dentro del directorio `plugins` de GLPI.
2. Entrar en GLPI como administrador.
3. Ir a `Configuracion > Plugins`.
4. Instalar y activar el plugin `Formaciones`.

El instalador crea la tabla `glpi_plugin_formaciones_formaciones`.
Desde la version 2.0.0, si la tabla ya existe, el instalador anade la columna
`company` sin borrar los datos anteriores.

## Flujo basico

1. GLPI carga `setup.php`.
2. `plugin_init_formaciones()` registra el menu en Activos.
3. Al pulsar en Formaciones se abre `front/formacion.php`.
4. Al pulsar Anadir o un registro existente se abre `front/formacion.form.php`.
5. El formulario usa la clase `PluginFormacionesFormacion` para validar permisos y guardar datos.
