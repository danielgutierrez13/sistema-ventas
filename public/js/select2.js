let Select2 = function () {
    // Init Select2 (with .js-select2 class)
    $('.js-select2:not(.js-select2-enabled)').each((index, element) => {
        let el = $(element);

        // Add .js-select2-enabled class to tag it as activated and init it
        el.addClass('js-select2-enabled').select2({
            allowClear: true,
            placeholder: el.data('placeholder') || false,
            dropdownAutoWidth : true,
            width: '100%'
        });
    });
}