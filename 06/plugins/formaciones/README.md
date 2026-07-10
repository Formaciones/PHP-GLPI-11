# Formaciones

Plugin para GLPI 11 que anade un inventario de formaciones dentro del menu Activos.

Este README resume la finalidad de cada parte del plugin para que los alumnos
puedan orientarse antes de leer el codigo.

## Campos

- ID
- Nombre
- Descripcion
- Estado
- Fecha de inicio
- Fecha de fin
- Numero de plazas

## Estructura

- `setup.php`: fichero que GLPI lee para detectar, instalar, desinstalar y registrar el plugin.
- `hook.php`: fichero reservado para hooks y relaciones de base de datos.
- `inc/formacion.class.php`: clase principal del objeto Formacion; contiene formulario, estados y definicion de busqueda.
- `public/css/formaciones.css`: estilos propios, registrados por el hook `add_css` y limitados al formulario del plugin.
- `public/js/formaciones.js`: validacion de fechas en el navegador, registrada por el hook `add_javascript`.
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

El acceso al objeto se controla con el permiso estandar `computer`, por lo que no se instala ningun permiso propio del plugin.

## Flujo basico

1. GLPI carga `setup.php`.
2. `plugin_init_formaciones()` registra el menu en Activos.
3. Al pulsar en Formaciones se abre `front/formacion.php`.
4. Al pulsar Anadir o un registro existente se abre `front/formacion.form.php`.
5. El formulario usa la clase `PluginFormacionesFormacion` para validar permisos y guardar datos.
6. La hoja CSS modifica el aspecto nativo solo dentro de `.plugin-formaciones`.
7. JavaScript advierte y bloquea el envio si la fecha inicial es posterior a la final.

## Ejemplo de CSS y JavaScript

Los recursos se registran en `plugin_init_formaciones()` dentro de `setup.php`.
Las rutas de los hooks parten de `public/`, que es el directorio que GLPI 11
expone al navegador; por eso el hook usa `css/formaciones.css` aunque el archivo
fisico sea `public/css/formaciones.css`.
El prefijo de clases `plugin-formaciones` demuestra como sobrescribir estilos de
GLPI sin alterar las demas pantallas. La validacion tambien se repite en PHP,
porque los controles del navegador no sustituyen la validacion del servidor.
