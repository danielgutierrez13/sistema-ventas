/** CRUD LIST JS */
let CRUDList = function () {
    let generateRoute = function (route) {
        route = route + "?" + 'n=' + $('#filter_size option:selected').val();
        route = route + "&" + 'b=' + $('#filter_text').val();

        return route
    }

    let execute = function (route) {
        $(document).on('change', '#filter_size', function () {
            window.location = generateRoute(route);
        });

        $(document).on('keyup', '#filter_text', function (e) {
            let code = e.key; // recommended to use e.key, it's normalized across devices and languages
            if(code==="Enter") e.preventDefault();
            if(code===" " || code==="Enter" || code===","|| code===";"){
                window.location = generateRoute(route);
            } // missing closing if brace
        });

        $(document).on('click', '.btn-send', function () {
            window.location = route;
        });

        $(document).on('click', '.btn-clean', function () {
            window.location.href = route;
        });
    };

    return {
        init: function (route) {
            execute(route);
        },
    };
}();