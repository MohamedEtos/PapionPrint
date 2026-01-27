// Cart AJAX Handlers
$(document).ready(function () {
    // Remove individual item from cart
    $(document).on('click', '.remove-cart-item', function (e) {
        e.preventDefault();
        var $btn = $(this);
        var itemId = $btn.data('id');

        $.ajax({
            url: '/invoices/remove/' + itemId,
            type: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function (response) {
                // Update cart count
                if (response.cart_count !== undefined) {
                    $('.cart-item-count').text(response.cart_count);
                    $('.badge.badge-up.cart-item-count').text(response.cart_count);
                }

                // Update cart dropdown HTML
                if (response.cart_html) {
                    $('#cart-dropdown-items').html(response.cart_html);
                }

                // If on invoice page, reload to update table
                if (window.location.pathname.includes('/invoices/create')) {
                    location.reload();
                } else {
                    toastr.success('تم حذف المنتج من السلة');
                }
            },
            error: function () {
                toastr.error('حدث خطأ أثناء الحذف');
            }
        });
    });

    // Clear entire cart
    $(document).on('click', '.clear-cart-btn', function (e) {
        e.preventDefault();

        $.ajax({
            url: '/invoices/clear',
            type: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function (response) {
                // Update cart count
                if (response.cart_count !== undefined) {
                    $('.cart-item-count').text(response.cart_count);
                    $('.badge.badge-up.cart-item-count').text(response.cart_count);
                }

                // Update cart dropdown HTML
                if (response.cart_html) {
                    $('#cart-dropdown-items').html(response.cart_html);
                }

                // If on invoice page, reload to update table
                if (window.location.pathname.includes('/invoices/create')) {
                    location.reload();
                } else {
                    toastr.success('تم تفريغ السلة');
                }
            },
            error: function () {
                toastr.error('حدث خطأ أثناء التفريغ');
            }
        });
    });
});
