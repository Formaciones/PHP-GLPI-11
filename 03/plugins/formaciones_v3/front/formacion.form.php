<?php

// Carga el entorno completo de GLPI.
include('../../../inc/includes.php');

// Instancia el objeto que representa una formacion.
$formacion = new PluginFormacionesFormacion();

// Bloque de alta: se ejecuta cuando el formulario envia el boton "add".
if (isset($_POST['add'])) {
    // Comprueba permiso CREATE antes de insertar.
    $formacion->check(-1, CREATE, $_POST);

    // Inserta el registro y obtiene el nuevo ID.
    $new_id = $formacion->add($_POST);

    // Redirige al formulario del registro recien creado.
    Html::redirect(PluginFormacionesFormacion::getFormURLWithID($new_id));
}

// Bloque de actualizacion: se ejecuta al guardar un registro existente.
if (isset($_POST['update'])) {
    // Comprueba permiso UPDATE sobre el ID recibido.
    $formacion->check((int) $_POST['id'], UPDATE);

    // Actualiza el registro con los datos del formulario.
    $formacion->update($_POST);

    // Vuelve a la pagina anterior, normalmente el mismo formulario.
    Html::back();
}

// Bloque de borrado logico: marca el registro como eliminado.
if (isset($_POST['delete'])) {
    // Comprueba permiso DELETE sobre el registro.
    $formacion->check((int) $_POST['id'], DELETE);

    // Borrado logico: GLPI puede restaurarlo si el objeto lo soporta.
    $formacion->delete($_POST);

    // Vuelve al listado.
    $formacion->redirectToList();
}

// Bloque de purga: elimina definitivamente el registro.
if (isset($_POST['purge'])) {
    // Comprueba permiso PURGE, mas fuerte que DELETE.
    $formacion->check((int) $_POST['id'], PURGE);

    // Segundo parametro a 1 indica borrado definitivo.
    $formacion->delete($_POST, 1);

    // Vuelve al listado.
    $formacion->redirectToList();
}

// Bloque de restauracion: recupera un registro borrado logicamente.
if (isset($_POST['restore'])) {
    // GLPI usa PURGE para controlar acciones avanzadas de papelera.
    $formacion->check((int) $_POST['id'], PURGE);

    // Restaura el registro.
    $formacion->restore($_POST);

    // Vuelve a la pagina anterior.
    Html::back();
}

// ID recibido por GET. -1 significa nuevo registro.
$id = $_GET['id'] ?? -1;

// Si el ID es positivo, se edita/consulta un registro existente.
if ($id > 0) {
    // Comprueba permiso READ sobre el registro.
    $formacion->check((int) $id, READ);
} else {
    // Si no hay ID, se comprueba permiso CREATE para mostrar alta.
    $formacion->check(-1, CREATE);
}

// Cabecera estandar de GLPI para el formulario.
Html::header(
    PluginFormacionesFormacion::getTypeName(1),
    $_SERVER['PHP_SELF'],
    'assets',
    PluginFormacionesFormacion::class
);

// Muestra el formulario definido en PluginFormacionesFormacion::showForm().
$formacion->display([
    'id' => $id
]);

// Pie estandar de GLPI.
Html::footer();
