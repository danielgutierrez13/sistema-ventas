//== Class definition
let Notify = function () {
    toastr.options = {
        "closeButton": true,
        "debug": false,
        "newestOnTop": false,
        "progressBar": true,
        // "positionClass": "toastr-top-right",
        "preventDuplicates": true,
        "onclick": null,
        "showDuration": "300",
        "hideDuration": "1000",
        "timeOut": "10000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut",
        'body-output-type': 'trustedHtml'
    };
    return {
        info: function(message, title = null) {
            toastr.info(message, title);
        },
        success: function(message, title = null) {
            toastr.success(message, title);
        },
        warning: function(message, title = null) {
            toastr.warning(message, title);
        },
        danger: function(message, title = null) {
            toastr.error(message, title);
        },
    };
}();