<?php


if (!defined('GLPI_ROOT')) {
    die('Sorry. You cannot access this file directly');
}


class PluginExtensionticketsTicketExtension extends CommonDBTM
{
    public static $rightname = 'ticket';

    public static function getTypeName($nb = 0)
    {
        return _n('Soporte Externo', 'Soporte Externo', $nb, 'extensiontickets');
    }

    public static function getTable($classname = null)
    {
        return 'glpi_plugin_extensiontickets_ticketextensions';
    }

    public static function getIcon()
    {
        return 'ti ti-ticket';
    }

    // Retorna el nombre de la pestaña que contiene la extensión para Ticket
    public function getTabNameForItem(CommonGLPI $item, $withtemplate = 0)
    {
        if ($item instanceof Ticket) {
            return self::createTabEntry(
                __('Soporte Externo', 'extensiontickets'),
                0,
                self::getIcon()
            );
        }

        return '';
    }


    // Contenido de la nueva pestaña que representa la extensión de Ticket
    public static function displayTabContentForItem(CommonGLPI $item, $tabnum = 1, $withtemplate = 0)
    {
        if (!$item instanceof Ticket) {
            return false;
        }

        $extension = new self();
        $extension->showForTicket($item);  // Incorpora lo retornado por ShowForm()

        return true;
    }


    public function prepareInputForAdd($input)
    {
        return $this->cleanInput($input);
    }


    public function prepareInputForUpdate($input)
    {
        return $this->cleanInput($input);
    }


    private function cleanInput(array $input)
    {

        if (isset($input['tickets_id'])) {
            $input['tickets_id'] = (int) $input['tickets_id'];
        }

        if (isset($input['external_assignment'])) {
            $input['external_assignment'] = (int) $input['external_assignment'];
        }

        if (isset($input['external_company'])) {
            $input['external_company'] = trim($input['external_company']);
        }

        if (isset($input['cost'])) {
            $input['cost'] = str_replace(',', '.', trim((string) $input['cost']));
            $input['cost'] = is_numeric($input['cost']) ? (float) $input['cost'] : 0;
        }

        return $input;
    }


    public function getFromDBByTicket($tickets_id)
    {
        return $this->getFromDBByCrit([
            'tickets_id' => (int) $tickets_id
        ]);
    }

    // Guardar los datos extras cuando se guarda el ticket
    public function saveForTicket(array $input)
    {
        $tickets_id = (int) ($input['tickets_id'] ?? 0);

        if ($tickets_id <= 0) {
            return false;
        }

        if (!isset($input['external_assignment'])) {
            $input['external_assignment'] = 0;
        }

        if ($this->getFromDBByTicket($tickets_id)) {
            $input['id'] = $this->getID();
            return $this->update($input);
        }

        return $this->add($input);
    }


    public function showForTicket(Ticket $ticket)
    {
        $tickets_id = (int) $ticket->getID();

        if ($tickets_id <= 0) {
            echo "<div class='center'>";
            echo htmlescape(__('Guarde el ticket antes de completar la extension.', 'extensiontickets'));
            echo "</div>";
            return false;
        }

        $this->getFromDBByTicket($tickets_id);

        $external_assignment = (int) ($this->fields['external_assignment'] ?? 0);
        $external_company = $this->fields['external_company'] ?? '';
        $cost = $this->fields['cost'] ?? '0.00';
        $canedit = Session::haveRight(self::$rightname, UPDATE);
        $action = Plugin::getWebDir('extensiontickets') . '/front/ticketextension.form.php';

        echo "<form method='post' action='" . htmlescape($action) . "'>";
        echo "<div class='center'>";
        echo "<table class='tab_cadre_fixe'>";

        echo "<tr><th colspan='2'>" . htmlescape(__('Datos de asignacion externa', 'extensiontickets')) . "</th></tr>";

        echo "<tr class='tab_bg_1'>";
        echo "<td>" . htmlescape(__('Asignacion Externa', 'extensiontickets')) . "</td>";
        echo "<td>";
        echo Html::hidden('external_assignment', ['value' => 0]);
        echo "<input type='checkbox'
                     name='external_assignment'
                     value='1' "
                     . ($external_assignment === 1 ? "checked" : "") . " "
                     . (!$canedit ? "disabled" : "") .
             ">";
        echo "</td>";
        echo "</tr>";

        $company_options = [
            'value' => $external_company,
            'size'  => 60
        ];

        if (!$canedit) {
            $company_options['disabled'] = 'disabled';
        }

        echo "<tr class='tab_bg_1'>";
        echo "<td>" . htmlescape(__('Empresa Externa', 'extensiontickets')) . "</td>";
        echo "<td>";
        echo Html::input('external_company', $company_options);
        echo "</td>";
        echo "</tr>";

        $cost_options = [
            'value' => $cost,
            'size'  => 20
        ];

        if (!$canedit) {
            $cost_options['disabled'] = 'disabled';
        }

        echo "<tr class='tab_bg_1'>";
        echo "<td>" . htmlescape(__('Coste', 'extensiontickets')) . "</td>";
        echo "<td>";
        echo Html::input('cost', $cost_options);
        echo "</td>";
        echo "</tr>";

        echo "</table>";
        echo "</div>";

        if ($canedit) {
            echo Html::hidden('tickets_id', ['value' => $tickets_id]);
            echo "<div class='center'>";
            echo Html::submit(__('Guardar'), [
                'name'  => 'save_extension',
                'class' => 'btn btn-primary'
            ]);
            echo "&nbsp;";
            echo Html::submit(__('Ejecutar accion demo', 'extensiontickets'), [
                'name'  => 'run_demo_action',
                'class' => 'btn btn-secondary'
            ]);
            echo "</div>";
        }

        Html::closeForm();

        return true;
    }
}

