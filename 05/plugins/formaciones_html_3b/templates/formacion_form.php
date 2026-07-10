<?php

if (!defined('GLPI_ROOT')) {
    die('Sorry. You cannot access this file directly');
}

// La plantilla se incluye desde PluginFormacionesFormacion::showForm().
// Por eso aqui existe $this, que representa la formacion que se esta editando.
global $DB;

// Preparamos los equipos junto con la ubicacion indicada en su ficha.
$computers = [];
$used_location_ids = [];
$computer_rows = $DB->request([
    'FROM'  => Computer::getTable(),
    'WHERE' => ['is_deleted' => 0],
    'ORDER' => ['name', 'id']
]);

foreach ($computer_rows as $computer) {
    $computer_id = (int) $computer['id'];
    $computer_name = trim((string) ($computer['name'] ?? ''));

    // Si algun equipo no tiene nombre, mostramos su ID para que siga siendo
    // seleccionable y no aparezca como una opcion vacia.
    if ($computer_name === '') {
        $computer_name = sprintf(__('ID %s'), $computer_id);
    }

    $location_id = (int) ($computer['locations_id'] ?? 0);
    $computers[$computer_id] = [
        'name'         => $computer_name,
        'locations_id' => $location_id
    ];

    if ($location_id > 0) {
        $used_location_ids[$location_id] = $location_id;
    }
}

// Solo ofrecemos ubicaciones que tienen al menos un equipo no eliminado.
$locations = [];
if ($used_location_ids !== []) {
    $location_rows = $DB->request([
        'FROM'  => Location::getTable(),
        'WHERE' => ['id' => $used_location_ids],
        'ORDER' => ['completename', 'name', 'id']
    ]);

    foreach ($location_rows as $location) {
        $location_id = (int) $location['id'];
        $location_name = trim((string) ($location['completename'] ?? ''));
        if ($location_name === '') {
            $location_name = trim((string) ($location['name'] ?? ''));
        }
        if ($location_name === '') {
            $location_name = sprintf(__('ID %s'), $location_id);
        }

        $locations[$location_id] = $location_name;
    }
}

$selected_computer_id = (int) ($this->fields['computers_id'] ?? 0);
$selected_location_id = $computers[$selected_computer_id]['locations_id'] ?? 0;

?>

<style>
    /* Estas clases ajustan la tabla generada por GLPI sin cambiar su estructura. */
    .plugin-formaciones-label {
        width: 18%;
        min-width: 150px;
        vertical-align: top;
        font-weight: 600;
    }

    .plugin-formaciones-value {
        width: 32%;
        vertical-align: top;
    }

    .plugin-formaciones-value input,
    .plugin-formaciones-value select,
    .plugin-formaciones-value textarea {
        max-width: 100%;
        width: 100%;
        box-sizing: border-box;
    }

    .plugin-formaciones-location-filter {
        margin-bottom: 6px;
    }

    /* En pantallas pequenas cada celda pasa a ocupar una linea completa. */
    @media (max-width: 700px) {
        .plugin-formaciones-row,
        .plugin-formaciones-label,
        .plugin-formaciones-value {
            display: block;
            width: 100%;
            min-width: 0;
        }

        .plugin-formaciones-label {
            padding-bottom: 4px;
        }

        .plugin-formaciones-value {
            padding-bottom: 12px;
        }
    }
</style>

<!-- Primera fila: datos principales de la formacion. -->
<tr class="tab_bg_1 plugin-formaciones-row">
    <td class="plugin-formaciones-label"><?= __('Nombre', 'formaciones') ?></td>
    <td class="plugin-formaciones-value">
        <!-- $this->fields contiene los valores actuales del registro. -->
        <?= Html::input('name', [
            'value' => $this->fields['name'] ?? '',
            'size'  => 60
        ]) ?>
    </td>

    <td class="plugin-formaciones-label"><?= __('Estado', 'formaciones') ?></td>
    <td class="plugin-formaciones-value">
        <?php
        // showFromArray crea un desplegable a partir del array devuelto por getStates().
        Dropdown::showFromArray('state', self::getStates(), [
            'value' => $this->fields['state'] ?? self::STATE_ACTIVE
        ]);
        ?>
    </td>
</tr>

<!-- Campo relacionado con Computer: se guarda el ID en computers_id. -->
<tr class="tab_bg_1 plugin-formaciones-row">
    <td class="plugin-formaciones-label"><?= __('Equipo', 'formaciones') ?></td>
    <td class="plugin-formaciones-value" colspan="3">
        <!-- La ubicacion se usa para limitar los equipos mostrados. -->
        <select name="computer_location_filter"
            id="plugin_formaciones_location_filter"
            class="plugin-formaciones-location-filter">
            <option value="0"><?= __('Seleccione una ubicacion', 'formaciones') ?></option>
            <?php foreach ($locations as $location_id => $location_name): ?>
                <option value="<?= $location_id ?>"
                    <?= ($selected_location_id === $location_id) ? 'selected' : '' ?>>
                    <?= htmlescape($location_name) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <!--
            Este campo oculto es el que se envia al guardar.
            El selector visible solo sirve para elegir y actualizar este valor.
        -->
        <input type="hidden" name="computers_id" id="plugin_formaciones_computers_id"
            value="<?= (int) ($this->fields['computers_id'] ?? 0) ?>">

        <!-- El value de cada option es el ID real del equipo en glpi_computers. -->
        <select id="plugin_formaciones_computer_picker">
            <option value="0"><?= __('Ninguno', 'formaciones') ?></option>
            <?php foreach ($computers as $computer_id => $computer): ?>
                <option value="<?= $computer_id ?>"
                    data-location-id="<?= $computer['locations_id'] ?>"
                    <?= ($selected_computer_id === $computer_id) ? 'selected' : '' ?>>
                    <?= htmlescape($computer['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </td>
</tr>

<!-- colspan="3" permite que la descripcion use todo el ancho restante. -->
<tr class="tab_bg_1 plugin-formaciones-row">
    <td class="plugin-formaciones-label"><?= __('Descripcion', 'formaciones') ?></td>
    <td class="plugin-formaciones-value" colspan="3">
        <?php Html::textarea([
            'name'  => 'description',
            'value' => $this->fields['description'] ?? '',
            'cols'  => 100,
            'rows'  => 6
        ]) ?>
    </td>
</tr>

<script>
function pluginFormacionesInitComputerFilter() {
    var locationFilter = document.getElementById('plugin_formaciones_location_filter');
    var hiddenInput = document.getElementById('plugin_formaciones_computers_id');
    var select = document.getElementById('plugin_formaciones_computer_picker');

    if (!locationFilter || !hiddenInput || !select) {
        return;
    }

    // Guardamos la lista original porque algunos navegadores no ocultan bien
    // elementos <option> dentro de un <select>.
    var originalOptions = Array.prototype.map.call(select.options, function (option) {
        return {
            value: option.value,
            text: option.text,
            locationId: option.dataset.locationId || '0'
        };
    });

    function refreshComputerOptions() {
        var locationId = locationFilter.value;
        var selectedValue = hiddenInput.value;
        var hasSelectedOption = false;

        select.innerHTML = '';

        originalOptions.forEach(function (option) {
            var isEmptyOption = option.value === '0';
            // Sin ubicacion solo se ofrece "Ninguno". Con una ubicacion se
            // muestran exclusivamente los equipos asociados a ella.
            if (!isEmptyOption && option.locationId !== locationId) {
                return;
            }

            var newOption = document.createElement('option');
            newOption.value = option.value;
            newOption.text = option.text;
            newOption.dataset.locationId = option.locationId;
            newOption.selected = option.value === selectedValue;
            hasSelectedOption = hasSelectedOption || newOption.selected;
            select.appendChild(newOption);
        });

        // Si el valor guardado no esta dentro del filtro, no marcamos otra
        // opcion automaticamente para no cambiar el equipo sin querer.
        if (!hasSelectedOption) {
            select.selectedIndex = -1;
        }
    }

    locationFilter.addEventListener('change', function () {
        // Al cambiar de ubicacion se descarta un equipo de la ubicacion anterior.
        hiddenInput.value = '0';
        refreshComputerOptions();
        select.value = '0';
    });
    select.addEventListener('change', function () {
        hiddenInput.value = select.value;
    });

    refreshComputerOptions();
}

// En GLPI la plantilla puede cargarse cuando DOMContentLoaded ya ha ocurrido.
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', pluginFormacionesInitComputerFilter);
} else {
    pluginFormacionesInitComputerFilter();
}
</script>
