$(function() {

    $advancedFields = $('[data-advanced-field]').closest('.control-group');
    $advancedFields.hide();
    $('[data-action="toggle-advanced-fields"]').click(function(e) {
        e.preventDefault();
        $advancedFields.slideToggle();
    });

    $('.delete').click(function(e) {
        $('#deleteRedirect form').attr('action', $(this).attr('data-deleteurl'));
    });

});