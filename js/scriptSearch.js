$(document).ready(function() {
    $('input[type="checkbox"]').change(function() {
        var filters = {};
        $('input[type="checkbox"]:checked').each(function() {
            var name = $(this).attr('name');
            if (name) {
                filters[name] = 'on';
            }
        });
        $.ajax({
            type: 'POST',
            url: 'notes/search_service',
            data: filters,
            dataType: 'json',
            success: function(response) {
                console.log(response);
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
                console.error(status)
                console.error(error)
            }
        });
    });
});
