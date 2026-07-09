<?php

if (!defined('GLPI_ROOT')) {
    die('Sorry. You cannot access this file directly');
}

// Carga los tipos desde el API externo antes de pintar el desplegable.
$external_types = PluginFormacionesFormacion::getExternalTypes();

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
    <td><?= __('Tipo', 'formaciones') ?></td>
    <td colspan="3">
        <?php
        /*
         * Este desplegable muestra datos que vienen de un API externo.
         * En XAMPP suele cargarse desde:
         * http://localhost/labs/05/api/tipos/
         *
         * Como no hemos anadido una columna en base de datos, el campo usa el
         * nombre _external_type y la clase lo elimina en cleanInput().
         * Asi el alumno puede abrir el desplegable, pero GLPI no lo guarda.
         */
        if (count($external_types) > 0) {
            ?>
            <select name="_external_type" class="form-select">
                <option value=""><?= __('Seleccione un tipo', 'formaciones') ?></option>
                <?php foreach ($external_types as $code => $description): ?>
                    <!-- El value usa el codigo tecnico; el texto visible usa la descripcion del API. -->
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
    <!-- colspan="3" hace que el textarea ocupe las tres columnas restantes. -->
    <td colspan="3">
        <?php Html::textarea([
            'name'  => 'description',
            'value' => $this->fields['description'] ?? '',
            'cols'  => 100,
            'rows'  => 6
        ]) ?>
    </td>
</tr>
