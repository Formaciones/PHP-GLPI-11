<?php

use Plugin\SubcontractInstall\ApiClient;

function plugin_subcontractinstall_computer_added(CommonDBTM $computer)
{
    ApiClient::sendComputer($computer, 'created');
}

function plugin_subcontractinstall_computer_updated(CommonDBTM $computer)
{
    ApiClient::sendComputer($computer, 'updated');
}
