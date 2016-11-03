$(function() {

    var $advancedFields = $('[data-advanced-field]').closest('.control-group');
    if ($advancedFields.length && $advancedFields.find(".form-errors").size() == 0) {
        $advancedFields.hide();
    }
    $('[data-action="toggle-advanced-fields"]').click(function(e) {
        e.preventDefault();
        $advancedFields.slideToggle();
    });

    $('.delete').click(function(e) {
        $('#deleteRedirect form').attr('action', $(this).attr('data-deleteurl'));
    });

});