$(function() {

    $('.delete').click(function(e) {
        $('#deleteRedirect form').attr('action', $(this).attr('data-deleteurl'));
    });

});