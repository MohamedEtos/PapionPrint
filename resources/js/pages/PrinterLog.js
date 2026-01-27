$(document).ready(function () {
  "use strict";

  // Init Data Table
  var table = $('.data-thumb-view').DataTable({
    processing: true,
    serverSide: true,
    responsive: false,
    ajax: {
      url: window.location.href, // Reuse current URL which hits printLog
      data: function (d) {
        d.min = $('#min-date').val();
        d.max = $('#max-date').val();
      }
    },
    columns: [
      { data: 'id' }, // 0: Checkbox
      { data: 'action' }, // 1: Actions
      { data: 'image' }, // 2: Image
      { data: 'orderNumber' }, // 3: Order #
      { data: 'customers.name', defaultContent: 'غير محدد' }, // 4: Customer
      { data: 'machines.name', defaultContent: 'غير محدد' }, // 5: Machine
      { data: 'fileHeight' }, // 6: H
      { data: 'fileWidth' }, // 7: W
      { data: 'fileCopies', defaultContent: '0' }, // 8: Copies
      { data: 'picInCopies', defaultContent: '0' }, // 9: Pic/Copy
      { data: 'meters' }, // 10: Meters
      { data: 'user.name', defaultContent: 'غير محدد' }, // 11: Designer
      { data: 'user2.name', defaultContent: 'غير محدد' }, // 12: Operator
      { data: 'notes', defaultContent: '-' }, // 13: Notes
      { data: 'created_at' }, // 14: Created At
      { data: 'timeEndOpration' } // 15: End At
    ],
    columnDefs: [
      {
        targets: 0,

        orderable: false,
        className: 'dt-checkboxes-cell',
        render: function (data, type, full, meta) {
          return '<div class="dt-checkboxes"><input type="checkbox" class="dt-checkboxes key_checkbox" value="' + full.id + '"><label></label></div>';
        },
        checkboxes: { selectRow: true }
      },
      {
        targets: 1, // Actions
        orderable: false,
        render: function (data, type, full, meta) {
          return `<button type="button" class="btn btn-icon btn-flat-primary duplicate-order-btn" title="إعادة تشغيل" data-id="${full.id}">
                            <i class="feather icon-copy"></i>
                        </button>`;
        }
      },
      {
        targets: 2, // Image
        orderable: false,
        className: 'product-img',
        render: function (data, type, full, meta) {
          // Determine image path
          let imgPath = assetPath + 'core/images/elements/apple-watch.png'; // Fallback

          if (full.orders_imgs && full.orders_imgs.length > 0) {
            imgPath = '/storage/' + full.orders_imgs[0].path;
          }

          return `<input type="hidden" class="order_id" value="${full.id}">
                        <img style="height: 50px;" src="${imgPath}" alt="Img">`;
        }
      },
      {
        targets: 5, // Machine
        render: function (data, type, full, meta) {
          return (full.machines ? full.machines.name : 'غير محدد') + ' ' + (full.pass ? full.pass + ' Pass' : '');
        }
      },
      {
        targets: 10, // Meters
        render: function (data, type, full, meta) {
          return '<b>' + parseFloat(data).toFixed(2) + ' </b>';
        }
      },
      {
        targets: 14, // Created At
        render: function (data, type, full, meta) {
          if (!data) return '';
          // Simple parsing or use a library if available. Assuming standard YYYY-MM-DD HH:MM:SS
          return new Date(data).toLocaleString('ar-EG');
        }
      },
      {
        targets: 15, // End At
        render: function (data, type, full, meta) {
          return data ? new Date(data).toLocaleString('ar-EG') : '-';
        }
      }
    ],
    dom: '<"top"<"actions action-btns"B><"action-filters"lf>><"clear">rt<"bottom"<"actions">p>',
    oLanguage: {
      sLengthMenu: "_MENU_",
      sSearch: ""

    },
    aLengthMenu: [[10, 20, 50, 100, 200], [10, 20, 50, 100, 200]],
    select: {
      style: "multi"
    },
    // Server-side ordering mapping if needed, else DataTables sends columns[i][data]
    order: [[14, "desc"]], // Sort by created_at by default
    bInfo: false,
    pageLength: 10,
    buttons: [
      // Reuse existing buttons logic or Keep empty if specific buttons are added via DOM manipulation in previous script
      // The previous script had "Add New" but this is "Print Log" so maybe not needed/visible?
      // View file shows "Actions" dropdown in HTML, hidden by default.
    ],
    initComplete: function (settings, json) {
      $(".dt-buttons .btn").removeClass("btn-secondary");

      // Move actions dropdown
      var actionDropdown = $(".actions-dropodown");
      actionDropdown.insertBefore($(".top .actions .dt-buttons"));

      // Check for actions visibility
      if (table.rows({ selected: true }).count() > 0) {
        actionDropdown.slideDown();
      } else {
        actionDropdown.hide();
      }
    },
    drawCallback: function () {

      // Mac fix
      if (navigator.userAgent.indexOf("Mac OS X") != -1) {
        $(".dt-checkboxes-cell input, .dt-checkboxes").addClass("mac-checkbox");
      }
    }
  });

  // Need to define assetPath if not global
  var assetPath = window.location.origin + '/';
  if (document.querySelector('base')) {
    assetPath = document.querySelector('base').href;
  }

  // Handle Event Listeners for Filters
  $('#min-date, #max-date').on('change', function () {
    table.draw();
  });

  // Actions Dropdown Visibility & Calculator
  table.on('select deselect', function () {
    var selectedCount = table.rows({ selected: true }).count();
    if (selectedCount > 0) {
      $('.actions-dropodown').slideDown();
    } else {
      $('.actions-dropodown').slideUp();
    }
    calculateTotals();
  });

  // Bulk Delete
  $(document).on("click", ".bulk-delete-btn", function (e) {
    e.preventDefault();
    var selectedRows = table.rows({ selected: true });
    // Note: data() gives objects now in server-side
    var selectedIds = [];

    // Iterate over selected data
    var data = selectedRows.data();
    for (var i = 0; i < data.length; i++) {
      selectedIds.push(data[i].id);
    }

    // Fallback if selection doesn't work as expected with serverside in some versions
    if (selectedIds.length === 0) {
      $('.dt-checkboxes:checked').each(function () {
        selectedIds.push($(this).val());
      });
      // De-dupe
      selectedIds = [...new Set(selectedIds)];
    }

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
            table.draw(); // Redraw table
            $('.actions-dropodown').hide();
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

  // Duplicate Order
  $(document).on('click', '.duplicate-order-btn', function (e) {
    e.preventDefault();
    var $btn = $(this);
    // Logic to get ID. Since we render data-id on button now:
    var orderId = $btn.data('id');
    if (!orderId) {
      // Fallback for old way if button didn't populate correctly
      var $row = $btn.closest('tr');
      orderId = $row.find('.order_id').val();
    }

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
              toastr.success('تم إعادة تشغيل الطلب بنجاح', "نجاح");
              // Refresh table
              table.draw();
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

  function calculateTotals() {
    var totalMeters = 0;
    var totalCost = 0;

    var selectedRows = table.rows({ selected: true }).data();
    var anyChecked = selectedRows.count() > 0;

    if (anyChecked) {
      selectedRows.each(function (row) {
        var meters = parseFloat(row.meters) || 0;
        var pass = parseInt(row.pass) || 0;
        var machine = row.machines; // Assuming 'machines' object is available

        totalMeters += meters;

        var price = 0;
        if (machine) {
          if (pass === 1) {
            price = parseFloat(machine.price_1_pass) || 0;
          } else if (pass === 4) {
            price = parseFloat(machine.price_4_pass) || 0;
          } else if (pass === 6) {
            price = parseFloat(machine.price_6_pass) || 0;
          }
        }

        totalCost += meters * price;
      });

      var html = '';
      if (totalMeters > 0) {
        html += '<span class="badge badge-info mb-1" style="font-size: 1em; margin-left:15px;"><i class="feather icon-maximize-2"></i>  طول الورق : ' + totalMeters.toFixed(2) + ' متر</span>';
      }

      if (totalCost > 0) {
        html += '<span class="badge badge-primary mb-1" style="font-size: 1em; margin-left:15px;"><i class="feather icon-dollar-sign"></i> الاجمالي : ' + totalCost.toFixed(2) + ' جنيه</span>';
      }

      $('#printer-log-calculator-results').html(html).slideDown();
    } else {
      $('#printer-log-calculator-results').slideUp();
    }
  }

  // Price Input Logic (if used anywhere else or kept just in case)
  // ... (previous logic seemed unused in view but safe to keep or remove. I'll remove as it seems irrelevant to pagination task and likely dead code)

  // --- Add To Invoice ---
  window.addToInvoice = function () {
    var selectedRows = table.rows({ selected: true }).data();
    var ids = [];

    selectedRows.each(function (row) {
      ids.push(row.id);
    });

    if (ids.length === 0) {
      toastr.warning('Please select items first');
      return;
    }

    $.post('/invoices/add', {
      _token: $('meta[name="csrf-token"]').attr('content'),
      ids: ids,
      type: 'printer'
    }, function (response) {
      toastr.success('تمت الاضافة للفاتورة');
    }).fail(function () {
      toastr.error('حدث خطأ');
    });
  }

});