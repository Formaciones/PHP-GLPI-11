<?php


include('../../../inc/includes.php');


Session::checkRight('profile', UPDATE);



if (isset($_POST['update_rights'])) {

    $profiles_id = (int) ($_POST['profiles_id'] ?? 0);



    $rights = 0;



    if (isset($_POST['rights_values']) && is_array($_POST['rights_values'])) {

        foreach ($_POST['rights_values'] as $value) {
            $rights += (int) $value;
        }
    }


    PluginFormacionesProfile::updateProfileRight($profiles_id, $rights);


    Html::redirect(Profile::getFormURLWithID($profiles_id));
}


Html::back();

