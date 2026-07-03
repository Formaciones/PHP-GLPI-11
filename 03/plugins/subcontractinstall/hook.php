<?php

use CommonDBTM;
use Plugin\SubcontractInstall\ApiClient;

function plugin_subcontractinstall_computer_add(CommonDBTM $computer)
{
    ApiClient::sendComputer($computer, 'crear');
}

function plugin_subcontractinstall_computer_update(CommonDBTM $computer)
{
    ApiClient::sendComputer($computer, 'actualizar');
}
