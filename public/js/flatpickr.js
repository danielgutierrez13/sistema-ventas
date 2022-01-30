let Flatpickr = function () {
    let Spanish = {
        weekdays: {
            shorthand: ["Dom", "Lun", "Mar", "Mié", "Jue", "Vie", "Sáb"],
            longhand: [
                "Domingo",
                "Lunes",
                "Martes",
                "Miércoles",
                "Jueves",
                "Viernes",
                "Sábado",
            ],
        },
        months: {
            shorthand: [
                "Ene",
                "Feb",
                "Mar",
                "Abr",
                "May",
                "Jun",
                "Jul",
                "Ago",
                "Sep",
                "Oct",
                "Nov",
                "Dic",
            ],
            longhand: [
                "Enero",
                "Febrero",
                "Marzo",
                "Abril",
                "Mayo",
                "Junio",
                "Julio",
                "Agosto",
                "Septiembre",
                "Octubre",
                "Noviembre",
                "Diciembre",
            ],
        },
        ordinal: function () {
            return "º";
        },
        firstDayOfWeek: 1,
        rangeSeparator: " a ",
        time_24hr: true,
    };

    // Init Flatpickr (with .js-flatpickr class)
    jQuery('.js-flatpickr:not(.js-flatpickr-enabled)').each((index, element) => {
        let el = jQuery(element);

        // Add .js-flatpickr-enabled class to tag it as activated
        el.addClass('js-flatpickr-enabled');

        // Init it
        flatpickr(el, {
            locale: 'es',
            dateFormat: "Y-m-d",
            altInput: true,
            altFormat: 'd/m/Y',
        });
    });
}