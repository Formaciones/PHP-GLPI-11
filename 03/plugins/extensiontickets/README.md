# Extension Tickets

Plugin formativo para GLPI 11 que extiende la ficha de un ticket con una
pestaña adicional.

## Campos

- Asignacion Externa: Si o No, por defecto No.
- Empresa Externa.
- Coste.

La pestaña tambien incluye un boton de demostracion. Al pulsarlo se muestra el
mensaje `Boton pulsado`.

## Estructura

- `setup.php`: registra el plugin, anade la pestaña al Ticket y crea la tabla.
- `hook.php`: reservado para hooks adicionales.
- `inc/ticketextension.class.php`: clase que muestra la pestaña y guarda los campos extra.
- `front/ticketextension.form.php`: procesa el guardado y el boton demo.
- `sql/install.sql`: SQL de referencia para estudiar la tabla.

## Instalacion

1. Copiar la carpeta `extensiontickets` dentro del directorio `plugins` de GLPI.
2. Entrar en GLPI como administrador.
3. Ir a `Configuracion > Plugins`.
4. Instalar y activar el plugin `Extension Tickets`.
5. Abrir un ticket y entrar en la pestaña `Extension tickets`.
