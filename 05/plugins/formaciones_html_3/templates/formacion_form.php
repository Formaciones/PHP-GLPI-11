<?php

if (!defined('GLPI_ROOT')) {
    die('Sorry. You cannot access this file directly');
}

global $DB;

$computers = [];
$computer_rows = $DB->request([
    'FROM'  => Computer::getTable(),
    'WHERE' => ['is_deleted' => 0],
    'ORDER' => ['name', 'id']
]);

foreach ($computer_rows as $computer) {
    $computer_id = (int) $computer['id'];
    $computer_name = trim((string) ($computer['name'] ?? ''));

    if ($computer_name === '') {
        $computer_name = sprintf(__('ID %s'), $computer_id);
    }

    $computers[$computer_id] = $computer_name;
}

?>

<style>

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

    .plugin-formaciones-computer-filter {
        margin-bottom: 6px;
    }

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

<tr class="tab_bg_1 plugin-formaciones-row">
    <td class="plugin-formaciones-label"><?= __('Nombre', 'formaciones') ?></td>
    <td class="plugin-formaciones-value">

        <?= Html::input('name', [
            'value' => $this->fields['name'] ?? '',
            'size'  => 60
        ]) ?>
    </td>

    <td class="plugin-formaciones-label"><?= __('Estado', 'formaciones') ?></td>
    <td class="plugin-formaciones-value">
        <?php

        Dropdown::showFromArray('state', self::getStates(), [
            'value' => $this->fields['state'] ?? self::STATE_ACTIVE
        ]);
        ?>
    </td>
</tr>

<tr class="tab_bg_1 plugin-formaciones-row">
    <td class="plugin-formaciones-label"><?= __('Equipo', 'formaciones') ?></td>
    <td class="plugin-formaciones-value" colspan="3">

        <?= Html::input('computer_filter', [
            'id'          => 'plugin_formaciones_computer_filter',
            'class'       => 'plugin-formaciones-computer-filter',
            'placeholder' => __('Filtrar equipos', 'formaciones')
        ]) ?>

        <input type="hidden" name="computers_id" id="plugin_formaciones_computers_id"
            value="<?= (int) ($this->fields['computers_id'] ?? 0) ?>">

        <select id="plugin_formaciones_computer_picker">
            <option value="0"><?= __('Ninguno', 'formaciones') ?></option>
            <?php foreach ($computers as $computer_id => $computer_name): ?>
                <option value="<?= $computer_id ?>"
                    <?= ((int) ($this->fields['computers_id'] ?? 0) === $computer_id) ? 'selected' : '' ?>>
                    <?= htmlescape($computer_name) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </td>
</tr>

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
    var filter = document.getElementById('plugin_formaciones_computer_filter');
    var hiddenInput = document.getElementById('plugin_formaciones_computers_id');
    var select = document.getElementById('plugin_formaciones_computer_picker');

    if (!filter || !hiddenInput || !select) {
        return;
    }

    var originalOptions = Array.prototype.map.call(select.options, function (option) {
        return {
            value: option.value,
            text: option.text,
            selected: option.selected
        };
    });

    function refreshComputerOptions() {

        var search = filter.value.toLowerCase();
        var selectedValue = hiddenInput.value;
        var hasSelectedOption = false;

        select.innerHTML = '';

        originalOptions.forEach(function (option) {
            var isEmptyOption = option.value === '0';
            var matchesSearch = option.text.toLowerCase().indexOf(search) !== -1;

            if (search !== '' && (isEmptyOption || !matchesSearch)) {
                return;
            }

            var newOption = document.createElement('option');
            newOption.value = option.value;
            newOption.text = option.text;
            newOption.selected = option.value === selectedValue;
            hasSelectedOption = hasSelectedOption || newOption.selected;
            select.appendChild(newOption);
        });

        if (!hasSelectedOption) {
            select.selectedIndex = -1;
        }
    }

    filter.addEventListener('input', refreshComputerOptions);
    select.addEventListener('change', function () {
        hiddenInput.value = select.value;
    });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', pluginFormacionesInitComputerFilter);
} else {
    pluginFormacionesInitComputerFilter();
}
</script>
