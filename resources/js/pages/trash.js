$(document).ready(function() {

$('.dt-buttons ,.btn-group').hide();


    // Restore Action
    $(document).on('click', '.action-restore', function() {
        var url = $(this).data('url');
        var tr = $(this).closest('tr');
        
        $.ajax({
            url: url,
            type: 'POST', // or PUT, but we set route to POST
            data: {
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                tr.fadeOut(300, function() {
                    $(this).remove();
                });
                    // Optional: Toast message
            },
            error: function(xhr) {
                alert('حدث خطأ أثناء الاسترجاع');
            }
        });
    });

    // Force Delete Action
    $(document).on('click', '.action-delete-forever', function() {
        var url = $(this).data('url');
        var tr = $(this).closest('tr');
        if(confirm('هل أنت متأكد من الحذف النهائي للطلب؟ لا يمكن التراجع عن هذا الإجراء.')) {
            $.ajax({
                url: url,
                type: 'DELETE',
                data: {
                _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    tr.fadeOut(300, function() {
                        $(this).remove();
                    });
                    // Optional: Show toast or alert
                },
                error: function(xhr) {
                    alert('حدث خطأ أثناء الحذف');
                }
            });
        }
    });
});