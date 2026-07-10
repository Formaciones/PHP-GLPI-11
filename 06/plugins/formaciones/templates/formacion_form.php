<?php

if (!defined('GLPI_ROOT')) {
    die('Sorry. You cannot access this file directly');
}

?>

<!--
    Esta plantilla contiene las filas del formulario.
    GLPI ya abre y cierra la tabla desde showFormHeader() y showFormButtons().
-->
<tr class="tab_bg_1 plugin-formaciones">
    <td class="plugin-formaciones__label"><?= __('Nombre', 'formaciones') ?></td>
    <td class="plugin-formaciones__field">
        <!--
            Html::input genera el <input> con el estilo y convenciones de GLPI.
            $this->fields trae el valor guardado cuando editamos un registro.
        -->
        <?= Html::input('name', [
            'value' => $this->fields['name'] ?? '',
            'size'  => 60
        ]) ?>
    </td>

    <td class="plugin-formaciones__label"><?= __('Estado', 'formaciones') ?></td>
    <td class="plugin-formaciones__field">
        <?php
        // Desplegable con los estados definidos en PluginFormacionesFormacion::getStates().
        Dropdown::showFromArray('state', self::getStates(), [
            'value' => $this->fields['state'] ?? self::STATE_ACTIVE
        ]);
        ?>
    </td>
</tr>

<!-- Segunda fila: fechas y capacidad maxima de la formacion. -->
<tr class="tab_bg_1 plugin-formaciones">
    <td class="plugin-formaciones__label"><?= __('Fecha de inicio', 'formaciones') ?></td>
    <td class="plugin-formaciones__field">
        <!-- type=date activa el selector de fechas nativo del navegador. -->
        <input type="date" name="begin_date" id="plugin_formaciones_begin_date"
            value="<?= htmlescape((string) ($this->fields['begin_date'] ?? '')) ?>">
    </td>

    <td class="plugin-formaciones__label"><?= __('Fecha de fin', 'formaciones') ?></td>
    <td class="plugin-formaciones__field">
        <!-- El ID permite que el JavaScript encuentre el campo sin depender de GLPI. -->
        <input type="date" name="end_date" id="plugin_formaciones_end_date"
            value="<?= htmlescape((string) ($this->fields['end_date'] ?? '')) ?>">
    </td>
</tr>

<!-- Tercera fila: numero de plazas y zona donde JS muestra la advertencia. -->
<tr class="tab_bg_1 plugin-formaciones">
    <td class="plugin-formaciones__label"><?= __('Numero de plazas', 'formaciones') ?></td>
    <td class="plugin-formaciones__field">
        <!-- min=0 evita numeros negativos mediante la validacion HTML nativa. -->
        <input type="number" name="number_places" min="0" step="1"
            value="<?= (int) ($this->fields['number_places'] ?? 0) ?>">
    </td>

    <!-- La celda se reserva para un mensaje accesible anunciado por lectores de pantalla. -->
    <td class="plugin-formaciones__warning-cell" colspan="2">
        <div id="plugin_formaciones_date_warning"
            class="plugin-formaciones__warning"
            role="alert"
            aria-live="polite"
            hidden>
            <?= __('La fecha de inicio no puede ser posterior a la fecha de fin.', 'formaciones') ?>
        </div>
    </td>
</tr>

<!-- Cuarta fila: descripcion larga de la formacion. -->
<tr class="tab_bg_1 plugin-formaciones">
    <td class="plugin-formaciones__label"><?= __('Descripcion', 'formaciones') ?></td>
    <!-- colspan="3" hace que el textarea ocupe las tres columnas restantes. -->
    <td class="plugin-formaciones__field" colspan="3">
        <?php Html::textarea([
            'name'  => 'description',
            'value' => $this->fields['description'] ?? '',
            'cols'  => 100,
            'rows'  => 6
        ]) ?>
    </td>
</tr>
