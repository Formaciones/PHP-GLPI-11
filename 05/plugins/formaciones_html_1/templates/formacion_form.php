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
    <td><?= __('Descripcion', 'formaciones') ?></td>

    <td colspan="3">
        <?= Html::textarea([
            'name'  => 'description',
            'value' => $this->fields['description'] ?? '',
            'cols'  => 100,
            'rows'  => 6
        ]) ?>
    </td>
</tr>
