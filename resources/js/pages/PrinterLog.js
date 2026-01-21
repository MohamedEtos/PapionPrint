$(document).ready(function() {
    // Handle price input changes
    $('.price-input').on('keypress', function(e) {
        if (e.which === 13) { // Enter key
            e.preventDefault();
            updatePrice($(this));
        }
    });

    $('.price-input').on('blur', function() {
        updatePrice($(this));
    });

    function updatePrice($input) {
        const orderId = $input.data('order-id');
        const field = $input.data('field');
        const value = parseFloat($input.val()) || 0;

        // Show loading state
        $input.prop('disabled', true);

        $.ajax({
            url: '{{ route("printers.update.price", ":id") }}'.replace(':id', orderId),
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                field: field,
                value: value
            },
            success: function(response) {
                if (response.success) {
                    // Show success notification
                    showNotification('تم تحديث السعر بنجاح!', 'success');

                    // Update the input value with formatted number
                    $input.val(value.toFixed(2));
                } else {
                    showNotification('حدث خطأ في تحديث السعر', 'error');
                }
            },
            error: function(xhr) {
                let message = 'حدث خطأ في تحديث السعر';
                if (xhr.responseJSON && xhr.responseJSON.error) {
                    message = xhr.responseJSON.error;
                }
                showNotification(message, 'error');
            },
            complete: function() {
                $input.prop('disabled', false);
            }
        });
    }

    function showNotification(message, type) {
        // Create notification element
        const notification = $('<div class="alert alert-' + (type === 'success' ? 'success' : 'danger') + ' alert-dismissible fade show position-fixed" style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;">')
            .html('<strong>' + message + '</strong><button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>');

        // Add to body
        $('body').append(notification);

        // Auto remove after 5 seconds
        setTimeout(function() {
            notification.fadeOut(function() {
                notification.remove();
            });
        }, 5000);

        // Remove on close button click
        notification.find('.close').on('click', function() {
            notification.fadeOut(function() {
                notification.remove();
            });
        });
    }
});