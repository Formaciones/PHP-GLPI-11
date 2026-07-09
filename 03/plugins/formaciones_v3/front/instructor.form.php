<?php

// Carga el entorno completo de GLPI.
include('../../../inc/includes.php');

// Instancia el objeto que representa un instructor.
$instructor = new PluginFormacionesInstructor();

// Bloque de alta: se ejecuta cuando el formulario envia el boton "add".
if (isset($_POST['add'])) {
    // Comprueba permiso CREATE antes de insertar.
    $instructor->check(-1, CREATE, $_POST);

    // Inserta el registro y obtiene el nuevo ID.
    $new_id = $instructor->add($_POST);

    // Redirige al formulario del instructor recien creado.
    Html::redirect(PluginFormacionesInstructor::getFormURLWithID($new_id));
}

// Bloque de actualizacion: se ejecuta al guardar un registro existente.
if (isset($_POST['update'])) {
    // Comprueba permiso UPDATE sobre el ID recibido.
    $instructor->check((int) $_POST['id'], UPDATE);

    // Actualiza el registro con los datos del formulario.
    $instructor->update($_POST);

    // Vuelve a la pagina anterior, normalmente el mismo formulario.
    Html::back();
}

// Bloque de borrado logico: marca el registro como eliminado.
if (isset($_POST['delete'])) {
    // Comprueba permiso DELETE sobre el registro.
    $instructor->check((int) $_POST['id'], DELETE);

    // Borrado logico: GLPI puede restaurarlo si el objeto lo soporta.
    $instructor->delete($_POST);

    // Vuelve al listado.
    $instructor->redirectToList();
}

// Bloque de purga: elimina definitivamente el registro.
if (isset($_POST['purge'])) {
    // Comprueba permiso PURGE, mas fuerte que DELETE.
    $instructor->check((int) $_POST['id'], PURGE);

    // Segundo parametro a 1 indica borrado definitivo.
    $instructor->delete($_POST, 1);

    // Vuelve al listado.
    $instructor->redirectToList();
}

// Bloque de restauracion: recupera un registro borrado logicamente.
if (isset($_POST['restore'])) {
    // GLPI usa PURGE para controlar acciones avanzadas de papelera.
    $instructor->check((int) $_POST['id'], PURGE);

    // Restaura el registro.
    $instructor->restore($_POST);

    // Vuelve a la pagina anterior.
    Html::back();
}

// ID recibido por GET. -1 significa nuevo registro.
$id = $_GET['id'] ?? -1;

// Si el ID es positivo, se edita/consulta un instructor existente.
if ($id > 0) {
    // Comprueba permiso READ sobre el registro.
    $instructor->check((int) $id, READ);
} else {
    // Si no hay ID, se comprueba permiso CREATE para mostrar alta.
    $instructor->check(-1, CREATE);
}

// Cabecera estandar de GLPI para el formulario.
Html::header(
    PluginFormacionesInstructor::getTypeName(1),
    $_SERVER['PHP_SELF'],
    'assets',
    PluginFormacionesInstructor::class
);

// Muestra el formulario definido en PluginFormacionesInstructor::showForm().
$instructor->display([
    'id' => $id
]);

// Pie estandar de GLPI.
Html::footer();
