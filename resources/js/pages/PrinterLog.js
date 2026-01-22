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




/////////////// multiple Bulk Delete & Actions Visibility ///////////////////////

  var table = $('.data-thumb-view').DataTable();

  // Initially hide actions dropdown if it exists logic isn't handled by CSS
  // Note: data-list-view.js moves .actions-dropodown to the toolbar.
  // We want to hide it when no rows are selected.
      $('.actions-dropodown').hide();
      $('.dt-buttons ,.btn-group').hide();
  table.on('select deselect', function () {
    var selectedCount = table.rows({ selected: true }).count();
    if (selectedCount > 0) {
      $('.actions-dropodown').slideDown();
    } else {
      $('.actions-dropodown').slideUp();
    }
  });

  $(document).on("click", ".bulk-delete-btn", function (e) {
    e.preventDefault();
    var selectedRows = table.rows({ selected: true });
    var selectedIds = [];

    selectedRows.nodes().each(function (row) {
      var id = $(row).find('.order_id').val();
      if (id) selectedIds.push(id);
    });

    if (selectedIds.length === 0) {
      Swal.fire({
        title: "تنبيه",
        text: "الرجاء تحديد طلب واحد على الأقل للحذف.",
        type: "warning",
        confirmButtonClass: 'btn btn-primary',
        buttonsStyling: false,
      });
      return;
    }

    Swal.fire({
      title: 'هل انت متاكد من حذف ' + selectedIds.length + ' طلب؟',
      text: "لن تتمكن من التراجع عن هذا!",
      type: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'نعم، احذفهم!',
      confirmButtonClass: 'btn btn-primary',
      cancelButtonClass: 'btn btn-danger ml-1',
      buttonsStyling: false,
    }).then(function (result) {
      if (result.value) {
        $.ajax({
          url: "/printers/bulk-delete",
          type: "POST",
          data: {
            ids: selectedIds,
            _token: $('meta[name="csrf-token"]').attr('content')
          },
          success: function (response) {
            selectedRows.remove().draw();
            $('.actions-dropodown').hide(); // Hide actions since selection is gone
            Swal.fire({
              type: 'success',
              title: 'تم الحذف!',
              text: 'تم حذف الطلبات المحددة بنجاح.',
              showConfirmButton: false,
              timer: 1500,
              buttonsStyling: false,
            });
          },
          error: function (xhr) {
            console.error("Bulk delete error:", xhr);
            Swal.fire({
              title: "خطأ!",
              text: "حدث خطأ أثناء الحذف. حاول مرة أخرى.",
              type: "error",
              confirmButtonClass: 'btn btn-primary',
              buttonsStyling: false,
            });
          }
        });
      }
    });
  });


















});