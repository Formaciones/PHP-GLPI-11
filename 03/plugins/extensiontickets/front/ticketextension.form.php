<?php


include('../../../inc/includes.php');

$tickets_id = (int) ($_POST['tickets_id'] ?? 0);

Session::checkRight('ticket', UPDATE);

$ticket = new Ticket();
if ($tickets_id <= 0 || !$ticket->getFromDB($tickets_id)) {
    Session::addMessageAfterRedirect(
        __('No se ha encontrado el ticket relacionado.', 'extensiontickets'),
        false,
        ERROR
    );
    Html::back();
    exit;
}

$extension = new PluginExtensionticketsTicketExtension();

if (isset($_POST['save_extension'])) {
    $extension->saveForTicket($_POST);
    Session::addMessageAfterRedirect(
        __('Datos de extension guardados.', 'extensiontickets')
    );

    Html::back();
    exit;
}


if (isset($_POST['run_demo_action'])) {
    Session::addMessageAfterRedirect(
        __('Boton pulsado.', 'extensiontickets')
    );
    Html::back();
    exit;
}


Html::back();
exit;

