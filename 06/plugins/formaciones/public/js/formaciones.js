/*
 * JavaScript propio del plugin Formaciones.
 * setup.php registra este fichero mediante el hook add_javascript de GLPI.
 * En GLPI 11 los recursos servidos por el navegador viven dentro de public/.
 */

/* La funcion se encapsula para no crear variables globales que puedan chocar con GLPI. */
(function () {
    'use strict';

    /* Devuelve los elementos actuales; se consultan cada vez por la carga dinamica de GLPI. */
    function getDateElements() {
        // Los ID de la plantilla permiten localizar exactamente los tres elementos.
        var beginDate = document.getElementById('plugin_formaciones_begin_date');
        var endDate = document.getElementById('plugin_formaciones_end_date');
        var warning = document.getElementById('plugin_formaciones_date_warning');

        // En otras paginas o durante una carga parcial todavia pueden no existir.
        if (!beginDate || !endDate || !warning) {
            return null;
        }

        // El objeto agrupa los elementos que necesita la funcion de validacion.
        return {
            beginDate: beginDate,
            endDate: endDate,
            warning: warning
        };
    }

    /* Comprueba el orden y actualiza tanto el mensaje como el estilo visual. */
    function validateDateRange() {
        // Se buscan los campos ahora, no solo cuando termino de cargarse la pagina.
        var elements = getDateElements();
        if (!elements) {
            return true;
        }

        // YYYY-MM-DD se puede comparar como texto porque ordena cronologicamente.
        var invalid = elements.beginDate.value !== ''
            && elements.endDate.value !== ''
            && elements.beginDate.value > elements.endDate.value;

        // hidden controla si el lector y el usuario pueden percibir el aviso.
        elements.warning.hidden = !invalid;

        // La clase CSS resalta ambos campos cuando existe el error.
        elements.beginDate.classList.toggle('plugin-formaciones__date--invalid', invalid);
        elements.endDate.classList.toggle('plugin-formaciones__date--invalid', invalid);

        // aria-invalid comunica el estado a las tecnologias de asistencia.
        elements.beginDate.setAttribute('aria-invalid', invalid ? 'true' : 'false');
        elements.endDate.setAttribute('aria-invalid', invalid ? 'true' : 'false');

        // El resultado permite reutilizar esta funcion durante el envio.
        return !invalid;
    }

    /* La delegacion funciona incluso si GLPI inserta los campos despues por AJAX. */
    document.addEventListener('input', function (event) {
        // Solo se valida cuando cambia uno de los dos campos de fecha del plugin.
        if (
            event.target.id === 'plugin_formaciones_begin_date'
            || event.target.id === 'plugin_formaciones_end_date'
        ) {
            validateDateRange();
        }
    });

    /* change cubre expresamente la seleccion desde el calendario del navegador. */
    document.addEventListener('change', function (event) {
        // Se reutiliza exactamente la misma validacion que para escritura manual.
        if (
            event.target.id === 'plugin_formaciones_begin_date'
            || event.target.id === 'plugin_formaciones_end_date'
        ) {
            validateDateRange();
        }
    });

    /* Capturar submit permite bloquear tambien formularios insertados dinamicamente. */
    document.addEventListener('submit', function (event) {
        // Solo se interviene si el formulario enviado contiene los campos del plugin.
        if (event.target.querySelector('#plugin_formaciones_begin_date')) {
            if (!validateDateRange()) {
                event.preventDefault();
                document.getElementById('plugin_formaciones_begin_date').focus();
            }
        }
    }, true);

    // La primera comprobacion cubre la carga normal de una ficha ya existente.
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', validateDateRange);
    } else {
        validateDateRange();
    }

    // GLPI dispara tabsload cuando reemplaza el contenido central de una pestana.
    if (window.jQuery) {
        window.jQuery(document).on('tabsload', '.glpi_tabs', validateDateRange);
    }
}());
