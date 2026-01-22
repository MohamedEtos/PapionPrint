$(document).ready(function () {
  // Handle price input changes
  $('.price-input').on('keypress', function (e) {
    if (e.which === 13) { // Enter key
      e.preventDefault();
      updatePrice($(this));
    }
  });

  $('.price-input').on('blur', function () {
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
      success: function (response) {
        if (response.success) {
          // Show success notification
          showNotification('تم تحديث السعر بنجاح!', 'success');

          // Update the input value with formatted number
          $input.val(value.toFixed(2));
        } else {
          showNotification('حدث خطأ في تحديث السعر', 'error');
        }
      },
      error: function (xhr) {
        let message = 'حدث خطأ في تحديث السعر';
        if (xhr.responseJSON && xhr.responseJSON.error) {
          message = xhr.responseJSON.error;
        }
        showNotification(message, 'error');
      },
      complete: function () {
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
    setTimeout(function () {
      notification.fadeOut(function () {
        notification.remove();
      });
    }, 5000);

    // Remove on close button click
    notification.find('.close').on('click', function () {
      notification.fadeOut(function () {
        notification.remove();
      });
    });
  }




  /////////////// multiple Bulk Delete & Actions Visibility ///////////////////////

  // Custom filtering function which will search data in column four between two values
  $.fn.dataTable.ext.search.push(
    function (settings, data, dataIndex) {
      var min = $('#min-date').val();
      var max = $('#max-date').val();
      // Assuming the date is in the 'data-date' attribute of the column with index 14 (Created At)
      // We need to fetch the node to get the attribute, or use the render data if configured.
      // Since we didn't configure columns data, we can try to look at the DOM or pass it in invisible column.
      // A better way with existing setup:
      var dateCell = settings.aoData[dataIndex].anCells[14]; // Adjust index if needed (14 seems to be created_at based on visual count)
      // Let's verify index:
      // 0: empty, 1: actions, 2: img, 3: order#, 4: cust, 5: machine, 6: H, 7: W, 8: Copies, 9: PicCopies, 10: Meters, 11: User, 12: User2, 13: Notes, 14: CreatedAt, 15: End At

      var createdAt = $(dateCell).data('date') || ""; // "YYYY-MM-DD"

      if (
        (min === "" && max === "") ||
        (min === "" && createdAt <= max) ||
        (min <= createdAt && max === "") ||
        (min <= createdAt && createdAt <= max)
      ) {
        return true;
      }
      return false;
    }
  );

  var table = $('.data-thumb-view').DataTable();

  // Event listener to the two range filtering inputs to redraw on input
  $('#min-date, #max-date').on('change', function () {
    table.draw();
  });

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

  // Handle Duplicate Order
  $(document).on('click', '.duplicate-order-btn', function (e) {
    e.preventDefault();
    var $btn = $(this);
    var $row = $btn.closest('tr');
    var orderId = $row.find('.order_id').val();

    Swal.fire({
      title: 'تأكيد إعادة التشغيل',
      text: "سيتم إنشاء نسخة جديدة من هذا الطلب",
      type: 'question',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'نعم، أعد التشغيل',
      cancelButtonText: 'إلغاء',
      confirmButtonClass: 'btn btn-primary',
      cancelButtonClass: 'btn btn-danger ml-1',
      buttonsStyling: false,
    }).then(function (result) {
      if (result.value) {
        // Show loading state on button
        var originalHtml = $btn.html();
        $btn.prop('disabled', true).html('<i class="spinner-border spinner-border-sm"></i>');

        $.ajax({
          url: '/printers/duplicate/' + orderId,
          type: 'POST',
          data: {
            _token: $('meta[name="csrf-token"]').attr('content')
          },
          success: function (response) {
            Swal.fire({
              type: 'success',
              title: 'تمت العملية بنجاح!',
              text: 'تم إعادة تشغيل الطلب بنجاح.',
              showConfirmButton: false,
              timer: 1500
            }).then(function () {
              location.reload();
            });
          },
          error: function (xhr) {
            $btn.prop('disabled', false).html(originalHtml);
            var errorMsg = 'حدث خطأ أثناء العملية';
            if (xhr.responseJSON && xhr.responseJSON.error) {
              errorMsg = xhr.responseJSON.error;
            }
            Swal.fire({
              title: 'خطأ!',
              text: errorMsg,
              type: 'error',
              confirmButtonClass: 'btn btn-primary',
              buttonsStyling: false,
            });
          }
        });
      }
    });
  });


















  // Calculate Total Meters
  function updateTotalMeters() {
    var total = 0;
    var rows = table.rows({ selected: true });

    // If no rows are selected, use current filtered rows
    if (rows.count() === 0) {
      rows = table.rows({ search: 'applied' });
    }

    rows.data().each(function (data) {
      // data is an array of column values. Index 10 is Meters.
      // The data might be HTML string "<b>25 متر</b>". We need to parse it.
      // Or if using objects, access property. DataTables defaults to array of cell content usually.
      // Let's inspect the data logic. Since it's DOM sourced:
      var metersHtml = data[10];
      // Extract number. Regex for float
      var match = metersHtml.match(/([\d\.]+)/);
      if (match) {
        total += parseFloat(match[1]);
      }
    });

    $('#total-meters').text(total.toFixed(2) + ' متر');
  }

  // Update total on draw (filtering) and select/deselect
  table.on('draw', function () {
    updateTotalMeters();
  });

  table.on('select deselect', function () {
    updateTotalMeters();
  });

  // Initial calculation
  updateTotalMeters();

});