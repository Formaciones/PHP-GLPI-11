<?php

// Carga el entorno completo de GLPI: sesion, base de datos, clases y permisos.
include('../../../inc/includes.php');

// Solo usuarios con permiso para modificar perfiles pueden guardar estos permisos.
Session::checkRight('profile', UPDATE);

// Este bloque se ejecuta cuando se pulsa el boton "Guardar" en la pestana
// de permisos de Formaciones dentro de un perfil.
if (isset($_POST['update_rights'])) {
    // ID del perfil que se esta editando. Si no llega, se usa 0 como valor seguro.
    $profiles_id = (int) ($_POST['profiles_id'] ?? 0);

    // Acumulador numerico de permisos. GLPI guarda permisos como suma de bits:
    // READ + CREATE + UPDATE, etc.
    $rights = 0;

    // rights_values llega desde los checkboxes del formulario.
    // Cada checkbox aporta el valor numerico de un permiso concreto.
    if (isset($_POST['rights_values']) && is_array($_POST['rights_values'])) {
        // Suma todos los permisos marcados por el usuario.
        foreach ($_POST['rights_values'] as $value) {
            $rights += (int) $value;
        }
    }

    // Guarda el permiso calculado en glpi_profilerights.
    PluginFormacionesProfile::updateProfileRight($profiles_id, $rights);

    // Vuelve a la ficha del perfil editado.
    Html::redirect(Profile::getFormURLWithID($profiles_id));
}

// Si se accede al archivo sin enviar el formulario, vuelve a la pagina anterior.
Html::back();
