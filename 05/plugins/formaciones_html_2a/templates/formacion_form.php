<?php

if (!defined('GLPI_ROOT')) {
    die('Sorry. You cannot access this file directly');
}

?>

<tr class="tab_bg_1">
    <td><?= __('Nombre', 'formaciones') ?></td>
    <td>

        <?= Html::input('name', [
            'value' => $this->fields['name'] ?? '',
            'size'  => 60
        ]) ?>
    </td>

    <td><?= __('Estado', 'formaciones') ?></td>
    <td>
        <?php

        Dropdown::showFromArray('state', self::getStates(), [
            'value' => $this->fields['state'] ?? self::STATE_ACTIVE
        ]);
        ?>
    </td>
</tr>

<tr class="tab_bg_1">
    <td><?= __('Formato', 'formaciones') ?></td>
    <td>
        <?php
        Dropdown::showFromArray('format', self::getFormats(), [
            'value' => $this->fields['format'] ?? self::FORMAT_ONLINE
        ]);
        ?>
    </td>

    <td><?= __('Formador', 'formaciones') ?></td>
    <td>
        <?php
        Dropdown::showFromArray('trainer', self::getTrainers(), [
            'value' => $this->fields['trainer'] ?? 'ana_garcia'
        ]);
        ?>
    </td>
</tr>

<tr class="tab_bg_1">
    <td><?= __('Fecha de inicio', 'formaciones') ?></td>
    <td>
        <?= Html::input('start_date', [
            'value' => $this->fields['start_date'] ?? '',
            'type'  => 'date'
        ]) ?>
    </td>

    <td><?= __('Fecha de fin', 'formaciones') ?></td>
    <td>
        <?= Html::input('end_date', [
            'value' => $this->fields['end_date'] ?? '',
            'type'  => 'date'
        ]) ?>
    </td>
</tr>

<tr class="tab_bg_1">
    <td><?= __('Duracion en horas', 'formaciones') ?></td>
    <td>
        <?= Html::input('duration_hours', [
            'value' => $this->fields['duration_hours'] ?? '8',
            'type'  => 'number',
            'step'  => '0.5',
            'min'   => '0'
        ]) ?>
    </td>

    <td><?= __('Plazas disponibles', 'formaciones') ?></td>
    <td>
        <?= Html::input('capacity', [
            'value' => $this->fields['capacity'] ?? '20',
            'type'  => 'number',
            'min'   => '0'
        ]) ?>
    </td>
</tr>

<tr class="tab_bg_1">
    <td><?= __('Coste', 'formaciones') ?></td>
    <td>

        <?= Html::input('cost', [
            'value' => $this->fields['cost'] ?? '0',
            'type'  => 'number',
            'step'  => '0.01',
            'min'   => '0'
        ]) ?>
        <?php
        Dropdown::showFromArray('currency', self::getCurrencies(), [
            'value' => $this->fields['currency'] ?? 'EUR'
        ]);
        ?>
    </td>

    <td><?= __('Nivel', 'formaciones') ?></td>
    <td>
        <?php
        Dropdown::showFromArray('level', self::getLevels(), [
            'value' => $this->fields['level'] ?? 'inicial'
        ]);
        ?>
    </td>
</tr>

<tr class="tab_bg_1">
    <td><?= __('Ubicacion / aula', 'formaciones') ?></td>
    <td>
        <?= Html::input('location', [
            'value' => $this->fields['location'] ?? '',
            'size'  => 60
        ]) ?>
    </td>

    <td><?= __('Enlace online', 'formaciones') ?></td>
    <td>

        <?= Html::input('meeting_url', [
            'value' => $this->fields['meeting_url'] ?? '',
            'size'  => 60,
            'type'  => 'url'
        ]) ?>
    </td>
</tr>

<tr class="tab_bg_1">
    <td><?= __('Destinatarios', 'formaciones') ?></td>
    <td>
        <?= Html::input('target_audience', [
            'value' => $this->fields['target_audience'] ?? '',
            'size'  => 60
        ]) ?>
    </td>

    <td><?= __('Emite certificado', 'formaciones') ?></td>
    <td>
        <label>

            <input type="checkbox" name="certificate" value="1"
                <?= ((int) ($this->fields['certificate'] ?? 1) === 1) ? 'checked' : '' ?>>
            <?= __('Si') ?>
        </label>
    </td>
</tr>

<tr class="tab_bg_1">
    <td><?= __('Descripcion', 'formaciones') ?></td>

    <td colspan="3">
        <?php Html::textarea([
            'name'  => 'description',
            'value' => $this->fields['description'] ?? '',
            'cols'  => 100,
            'rows'  => 6
        ]) ?>
    </td>
</tr>

<tr class="tab_bg_1">
    <td><?= __('Observaciones internas', 'formaciones') ?></td>

    <td colspan="3">
        <?php Html::textarea([
            'name'  => 'observations',
            'value' => $this->fields['observations'] ?? '',
            'cols'  => 100,
            'rows'  => 4
        ]) ?>
    </td>
</tr>
