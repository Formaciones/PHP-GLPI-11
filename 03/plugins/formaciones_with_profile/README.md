# Formaciones

Plugin para GLPI 11 que anade un inventario simple de formaciones dentro del menu Activos.

Este README resume la finalidad de cada parte del plugin para que los alumnos puedan orientarse antes de leer el codigo.

## Campos

- ID
- Nombre
- Descripcion
- Estado
- Fecha de creacion
- Ultima modificacion

## Permisos

El plugin usa un permiso propio:

```php
plugin_formaciones_formacion
```

La clase `PluginFormacionesFormacion` lo usa mediante:

```php
public static $rightname = 'plugin_formaciones_formacion';
```

Los permisos se configuran desde:

`Configuracion > Perfiles > [perfil] > Formaciones`

El formulario actual de permisos esta en `PluginFormacionesProfile::showForm()`. Tambien existe `PluginFormacionesProfile::showFormEasy()` como alternativa mas sencilla basada en la matriz estandar de permisos de GLPI (`displayRightsChoiceMatrix()`), pero no sustituye al formulario actual.

## Estructura

- `setup.php`: fichero que GLPI lee para detectar, instalar, desinstalar y registrar el plugin.
- `hook.php`: fichero reservado para hooks; actualmente declara que no hay relaciones entre tablas.
- `inc/formacion.class.php`: clase principal del objeto Formacion; contiene formulario, estados, permisos y definicion de busqueda.
- `inc/profile.class.php`: clase auxiliar que instala el permiso propio y anade la pestana Formaciones dentro de los perfiles.
- `front/formacion.php`: pagina de listado de formaciones.
- `front/formacion.form.php`: pagina que procesa altas, ediciones, borrados y muestra el formulario de formacion.
- `front/profile.form.php`: pagina que guarda los permisos de Formaciones configurados en un perfil.

## Instalacion

1. Copiar la carpeta `formaciones` dentro del directorio `plugins` de GLPI.
2. Entrar en GLPI como administrador.
3. Ir a `Configuracion > Plugins`.
4. Instalar y activar el plugin `Formaciones`.

El instalador crea la tabla `glpi_plugin_formaciones_formaciones` y registra el permiso `plugin_formaciones_formacion` para los perfiles.

## Flujo basico

1. GLPI carga `setup.php`.
2. `plugin_init_formaciones()` registra la clase de Formaciones, la clase de perfiles y la entrada de menu en Activos.
3. `PluginFormacionesProfile::initProfile()` carga el permiso propio en la sesion activa.
4. Al pulsar en Formaciones se abre `front/formacion.php`.
5. Al pulsar Anadir o un registro existente se abre `front/formacion.form.php`.
6. El formulario usa `PluginFormacionesFormacion` para validar permisos y guardar datos.
7. La pestana Formaciones de un perfil usa `front/profile.form.php` para guardar los permisos.
