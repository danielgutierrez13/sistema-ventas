/**
 Admin script
 **/
var Collection = function () {
    //* BEGIN:CORE HANDLERS *//

    var createChild = function (child,child_class) {
        // creamos el contenedor
        child = typeof child !== 'undefined' ? child : 'li';
        var custom_child = '<' + child + ' class="' + child_class + ' list-group-item"></' + child + '>' ;

        return custom_child;
    };

    // Handle Entity
    var handleEntity = function (collection, child) {
        var $addLink = $('<a href="#" class="btn btn-outline-success btn-sm tm-btn m-btn--icon m-btn--pill"><span><i class="fa flaticon-plus"></i><span>Agregar</span></span></a>');
        var $newLink = $(child).append($addLink);
        initDataForm(collection, $addLink, $newLink, child);
    };

    var initDataForm = function(collection, $addLink, $newLink, child){
        var $collectionHolder = $(collection);
        $collectionHolder.find('li').each(function() {
            addBlockFormDeleteLink($(this));
        });
        $collectionHolder.append($newLink); //$collectionHolder.prepend($newLink);
        //$collectionHolder.data('index', $collectionHolder.find(':input').length);
        $collectionHolder.data('index', $collectionHolder.find('li').length);
        $addLink.on('click', function(e) {
            e.preventDefault();
            addBlockForm($collectionHolder, $newLink, child);
        });
    };

    var addBlockForm = function($collectionHolder, $newLink, child) {
        var prototype = $collectionHolder.data('prototype');
        var index = $collectionHolder.data('index');
        var newForm;
        if($collectionHolder.data('prototype-name')){
            var prototype_name = new RegExp($collectionHolder.data('prototype-name'), 'gi');
            newForm = prototype.replace(prototype_name, index);
        }
        else{
            newForm = prototype.replace(/__name__/g, index);
        }
        $collectionHolder.data('index', index + 1);
        var $newFormLi = $(child).append(newForm);
        $newLink.before($newFormLi); //$newLink.append($newFormLi);
        addBlockFormDeleteLink($newFormLi);

        return $newFormLi;
    };

    var addBlockFormDeleteLink = function($formLi) {
        var $removeFormA = $('<a href="#" class="btn btn-outline-danger m-btn m-btn--icon m-btn--icon-only m-btn--pill"><i class="fa flaticon-delete"></i></a>');
        //var $auxForm = $('<div class="col-sm-1"></div>').append($removeFormA);
        //$tagFormLi.children('.row').append($auxForm);
        $formLi.append($removeFormA);
        $removeFormA.on('click', function(e) {
            // prevent the link from creating a "#" on the URL
            e.preventDefault();
            // remove the li for the tag form
            $formLi.remove();
        });
    };

    //* END:CORE HANDLERS *//

    return {
        init: function (collection,child) {
            var custom_child = createChild(child,'collection_child');
            handleEntity(collection,custom_child);
        },
    };

}();