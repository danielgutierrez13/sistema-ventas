var Collection = function () {
    //* BEGIN:CORE HANDLERS *//

    var createChild = function (child,child_class) {
        child = typeof child !== 'undefined' ? child : 'li';
        var custom_child = '<' + child + ' class="' + child_class + ' list-group-item collection_child"></' + child + '>' ;

        return custom_child;
    };

    // Handle Entity
    var handleEntity = function (collection, child) {
        var $addLink = $('<a href="#" class="btn btn-light-success btn-sm tm-btn m-btn--icon m-btn--pill"><span><i class="fa fa-plus"></i><span>Agregar</span></span></a>');
        var $newLink = $(child).append($addLink);
        initDataForm(collection, $addLink, $newLink, child);
    };

    var initDataForm = function(collection, $addLink, $newLink, child){
        var $collectionHolder = $(collection);
        $collectionHolder.find('li').each(function() {
            addBlockFormDeleteLink($(this));
        });
        $collectionHolder.append($newLink);
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
        $newLink.before($newFormLi);
        addBlockFormDeleteLink($newFormLi);

        return $newFormLi;
    };

    var addBlockFormDeleteLink = function($formLi) {
        var $removeFormA = $('<a href="#" class="btn btn-sm btn-light-danger btn-icon btn_cerrar"  style="position: absolute; top: 5px; right: 5px;"><i class="fa fa-times"></i></a>');
        $formLi.append($removeFormA);
        $removeFormA.on('click', function(e) {
            e.preventDefault();
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