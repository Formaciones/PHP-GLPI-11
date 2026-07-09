<?php

if (!defined('GLPI_ROOT')) {
    die('Sorry. You cannot access this file directly');
}

?>

<!--
    Esta plantilla contiene las filas del formulario.
    GLPI ya abre y cierra la tabla desde showFormHeader() y showFormButtons().
-->
<tr class="tab_bg_1">
    <td><?= __('Nombre', 'formaciones') ?></td>
    <td>
        <!--
            Html::input genera el <input> con el estilo y convenciones de GLPI.
            $this->fields trae el valor guardado cuando editamos un registro.
        -->
        <?= Html::input('name', [
            'value' => $this->fields['name'] ?? '',
            'size'  => 60
        ]) ?>
    </td>

    <td><?= __('Estado', 'formaciones') ?></td>
    <td>
        <?php
        // Desplegable con los estados definidos en PluginFormacionesFormacion::getStates().
        Dropdown::showFromArray('state', self::getStates(), [
            'value' => $this->fields['state'] ?? self::STATE_ACTIVE
        ]);
        ?>
    </td>
</tr>

<tr class="tab_bg_1">
    <td><?= __('Descripcion', 'formaciones') ?></td>
    <!-- colspan="3" hace que el textarea ocupe las tres columnas restantes. -->
    <td colspan="3">
        <?= Html::textarea([
            'name'  => 'description',
            'value' => $this->fields['description'] ?? '',
            'cols'  => 100,
            'rows'  => 6
        ]) ?>
    </td>
</tr>
