<?php

if (!defined('GLPI_ROOT')) {
    die('Sorry. You cannot access this file directly');
}

$label_cell = 'style="width: 15%; white-space: normal; vertical-align: top;"';
$value_cell = 'style="width: 35%; min-width: 0; vertical-align: top;"';
$wide_cell = 'style="width: 85%; min-width: 0; vertical-align: top;"';
$input_style = 'width: 100%; max-width: 100%; box-sizing: border-box;';
$textarea_style = 'width: 100%; max-width: 100%; box-sizing: border-box;';
$dropdown_options = [
    'width' => '100%'
];

?>

<!--
    Formulario completo de ejemplo.
    Cada <tr> agrupa dos campos para aprovechar las cuatro columnas de GLPI:
    etiqueta, valor, etiqueta, valor.
-->
<tr class="tab_bg_1">
    <td <?= $label_cell ?>><?= __('Nombre', 'formaciones') ?></td>
    <td <?= $value_cell ?>>
        <!-- Valor principal de la formacion. En edicion se rellena desde $this->fields. -->
        <?= Html::input('name', [
            'value' => $this->fields['name'] ?? '',
            'style' => $input_style
        ]) ?>
    </td>

    <td <?= $label_cell ?>><?= __('Estado', 'formaciones') ?></td>
    <td <?= $value_cell ?>>
        <?php
        // Desplegable definido desde la clase para centralizar los estados posibles.
        Dropdown::showFromArray('state', self::getStates(), $dropdown_options + [
            'value' => $this->fields['state'] ?? self::STATE_ACTIVE
        ]);
        ?>
    </td>
</tr>

<!-- Catalogos cerrados: se pintan como desplegables para evitar valores libres. -->
<tr class="tab_bg_1">
    <td <?= $label_cell ?>><?= __('Formato', 'formaciones') ?></td>
    <td <?= $value_cell ?>>
        <?php
        Dropdown::showFromArray('format', self::getFormats(), $dropdown_options + [
            'value' => $this->fields['format'] ?? self::FORMAT_ONLINE
        ]);
        ?>
    </td>

    <td <?= $label_cell ?>><?= __('Formador', 'formaciones') ?></td>
    <td <?= $value_cell ?>>
        <?php
        Dropdown::showFromArray('trainer', self::getTrainers(), $dropdown_options + [
            'value' => $this->fields['trainer'] ?? 'ana_garcia'
        ]);
        ?>
    </td>
</tr>

<!-- Los input type="date" dejan que el navegador muestre un selector de fecha. -->
<tr class="tab_bg_1">
    <td <?= $label_cell ?>><?= __('Fecha de inicio', 'formaciones') ?></td>
    <td <?= $value_cell ?>>
        <?= Html::input('start_date', [
            'value' => $this->fields['start_date'] ?? '',
            'type'  => 'date',
            'style' => $input_style
        ]) ?>
    </td>

    <td <?= $label_cell ?>><?= __('Fecha de fin', 'formaciones') ?></td>
    <td <?= $value_cell ?>>
        <?= Html::input('end_date', [
            'value' => $this->fields['end_date'] ?? '',
            'type'  => 'date',
            'style' => $input_style
        ]) ?>
    </td>
</tr>

<!-- Campos numericos: min, step y type ayudan a validar desde el navegador. -->
<tr class="tab_bg_1">
    <td <?= $label_cell ?>><?= __('Duracion en horas', 'formaciones') ?></td>
    <td <?= $value_cell ?>>
        <?= Html::input('duration_hours', [
            'value' => $this->fields['duration_hours'] ?? '8',
            'type'  => 'number',
            'step'  => '0.5',
            'min'   => '0',
            'style' => $input_style
        ]) ?>
    </td>

    <td <?= $label_cell ?>><?= __('Plazas disponibles', 'formaciones') ?></td>
    <td <?= $value_cell ?>>
        <?= Html::input('capacity', [
            'value' => $this->fields['capacity'] ?? '20',
            'type'  => 'number',
            'min'   => '0',
            'style' => $input_style
        ]) ?>
    </td>
</tr>

<tr class="tab_bg_1">
    <td <?= $label_cell ?>><?= __('Coste', 'formaciones') ?></td>
    <td <?= $value_cell ?>>
        <!-- Coste y moneda se guardan en columnas separadas. -->
        <div style="display: flex; gap: .5rem; align-items: center; width: 100%; min-width: 0;">
            <div style="flex: 1 1 auto; min-width: 0;">
                <?= Html::input('cost', [
                    'value' => $this->fields['cost'] ?? '0',
                    'type'  => 'number',
                    'step'  => '0.01',
                    'min'   => '0',
                    'style' => $input_style
                ]) ?>
            </div>
            <div style="flex: 0 0 6.5rem; min-width: 0;">
                <?php
                Dropdown::showFromArray('currency', self::getCurrencies(), $dropdown_options + [
                    'value' => $this->fields['currency'] ?? 'EUR'
                ]);
                ?>
            </div>
        </div>
    </td>

    <td <?= $label_cell ?>><?= __('Nivel', 'formaciones') ?></td>
    <td <?= $value_cell ?>>
        <?php
        Dropdown::showFromArray('level', self::getLevels(), $dropdown_options + [
            'value' => $this->fields['level'] ?? 'inicial'
        ]);
        ?>
    </td>
</tr>

<!-- Campos de texto libres para completar informacion operativa. -->
<tr class="tab_bg_1">
    <td <?= $label_cell ?>><?= __('Ubicacion / aula', 'formaciones') ?></td>
    <td <?= $value_cell ?>>
        <?= Html::input('location', [
            'value' => $this->fields['location'] ?? '',
            'style' => $input_style
        ]) ?>
    </td>

    <td <?= $label_cell ?>><?= __('Enlace online', 'formaciones') ?></td>
    <td <?= $value_cell ?>>
        <!-- type="url" indica al navegador que el contenido esperado es una URL. -->
        <?= Html::input('meeting_url', [
            'value' => $this->fields['meeting_url'] ?? '',
            'type'  => 'url',
            'style' => $input_style
        ]) ?>
    </td>
</tr>

<tr class="tab_bg_1">
    <td <?= $label_cell ?>><?= __('Destinatarios', 'formaciones') ?></td>
    <td <?= $value_cell ?>>
        <?= Html::input('target_audience', [
            'value' => $this->fields['target_audience'] ?? '',
            'style' => $input_style
        ]) ?>
    </td>

    <td <?= $label_cell ?>><?= __('Emite certificado', 'formaciones') ?></td>
    <td <?= $value_cell ?>>
        <label style="display: inline-flex; gap: .35rem; align-items: center;">
            <!-- Si el checkbox no se envia en POST, la clase lo interpreta como 0. -->
            <input type="checkbox" name="certificate" value="1"
                <?= ((int) ($this->fields['certificate'] ?? 1) === 1) ? 'checked' : '' ?>>
            <?= __('Si') ?>
        </label>
    </td>
</tr>

<tr class="tab_bg_1">
    <td <?= $label_cell ?>><?= __('Descripcion', 'formaciones') ?></td>
    <!-- Los textos largos usan todo el ancho restante del formulario. -->
    <td colspan="3" <?= $wide_cell ?>>
        <?php Html::textarea([
            'name'  => 'description',
            'value' => $this->fields['description'] ?? '',
            'cols'  => 40,
            'rows'  => 6,
            'style' => $textarea_style
        ]) ?>
    </td>
</tr>

<tr class="tab_bg_1">
    <td <?= $label_cell ?>><?= __('Observaciones internas', 'formaciones') ?></td>
    <!-- Campo interno de notas; funciona igual que descripcion pero con menos filas. -->
    <td colspan="3" <?= $wide_cell ?>>
        <?php Html::textarea([
            'name'  => 'observations',
            'value' => $this->fields['observations'] ?? '',
            'cols'  => 40,
            'rows'  => 4,
            'style' => $textarea_style
        ]) ?>
    </td>
</tr>
