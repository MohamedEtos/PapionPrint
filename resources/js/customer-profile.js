$(document).ready(function () {
    // View Invoice Details
    $('.view-invoice-btn').click(function () {
        var btn = $(this);
        var url = btn.data('url'); // Get URL directly from data attribute

        $('#invoiceDetailsModal').modal('show');
        $('#invoice-details-content').html('<div class="text-center p-3"><i class="feather icon-loader fa-spin fa-2x"></i> جار التحميل...</div>');

        $.get(url, function (response) {
            $('#invoice-details-content').html(response.html);
        });
    });

    // Add to Invoice
    $('.add-to-invoice').click(function () {
        var btn = $(this);
        var id = btn.data('id');
        var type = btn.data('type');
        var url = $('#orders-table-container').data('add-route'); // Get route from container

        // Loading state
        var originalContent = btn.html();
        btn.html('<i class="feather icon-loader fa-spin"></i>').prop('disabled', true);

        $.ajax({
            url: url,
            method: "POST",
            data: {
                ids: [id],
                type: type,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                toastr.success('تمت الإضافة للفاتورة بنجاح');
                btn.html(originalContent).prop('disabled', false);

                // Update Cart Count
                if (response.cart_count !== undefined) {
                    $('.cart-item-count').text(response.cart_count);
                }

                // Update Cart Dropdown HTML
                if (response.cart_html !== undefined && $('#cart-dropdown-items').length) {
                    $('#cart-dropdown-items').html(response.cart_html);
                }
            },
            error: function (xhr) {
                toastr.error('حدث خطأ أثناء الإضافة');
                btn.html(originalContent).prop('disabled', false);
            }
        });
    });
});
