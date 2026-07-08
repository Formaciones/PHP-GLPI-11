<?php


include('../../../inc/includes.php');


$instructor = new PluginFormacionesInstructor();


if (isset($_POST['add'])) {
    $instructor->check(-1, CREATE, $_POST);
    $new_id = $instructor->add($_POST);
    Html::redirect(PluginFormacionesInstructor::getFormURLWithID($new_id));
}


if (isset($_POST['update'])) {
    $instructor->check((int) $_POST['id'], UPDATE);
    $instructor->update($_POST);

    Html::back();
}


if (isset($_POST['delete'])) {
    $instructor->check((int) $_POST['id'], DELETE);
    $instructor->delete($_POST);
    $instructor->redirectToList();
}


if (isset($_POST['purge'])) {
    $instructor->check((int) $_POST['id'], PURGE);
    $instructor->delete($_POST, 1);
    $instructor->redirectToList();
}


if (isset($_POST['restore'])) {
    $instructor->check((int) $_POST['id'], PURGE);
    $instructor->restore($_POST);
    Html::back();
}


$id = $_GET['id'] ?? -1;


if ($id > 0) {
    $instructor->check((int) $id, READ);
} else {

    $instructor->check(-1, CREATE);
}


Html::header(
    PluginFormacionesInstructor::getTypeName(1),
    $_SERVER['PHP_SELF'],
    'assets',
    PluginFormacionesInstructor::class
);


$instructor->display([
    'id' => $id
]);


Html::footer();

