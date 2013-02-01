$(document).ready(function() {

    $('.delete').click(function(e) {
        $('#myModal .delete').attr('data-deleteurl', $(this).attr('data-deleteurl'));
    });

    // delete redirection
    $('#myModal .delete').click(function(e) {
        e.preventDefault();

        var deleteUrl = $(this).attr('data-deleteurl');
        $.post(deleteUrl, function() {
            // console.log('hurray');
        });
    });

});