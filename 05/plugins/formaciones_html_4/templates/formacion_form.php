<?php

if (!defined('GLPI_ROOT')) {
    die('Sorry. You cannot access this file directly');
}

$external_types = PluginFormacionesFormacion::getExternalTypes();

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
    <td><?= __('Tipo', 'formaciones') ?></td>
    <td colspan="3">
        <?php

        if (count($external_types) > 0) {
            ?>
            <select name="_external_type" class="form-select">
                <option value=""><?= __('Seleccione un tipo', 'formaciones') ?></option>
                <?php foreach ($external_types as $code => $description): ?>

                    <option value="<?= htmlescape($code) ?>">
                        <?= htmlescape($description) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <?php
        } else {
            ?>
            <input
                type="text"
                name="_external_type"
                class="form-control"
                value="<?= htmlescape(__('No se pudieron cargar los tipos externos', 'formaciones')) ?>"
                disabled>
            <?php
        }
        ?>
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
