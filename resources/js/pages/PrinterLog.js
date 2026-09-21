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
          let editBtn = '';
          if (window.permissions && window.permissions.canEdit) {
              editBtn = `<button type="button" class="btn btn-icon btn-flat-info action-edit edit-order-btn" title="تعديل" data-id="${full.id}">
                          <i class="feather icon-edit"></i>
                      </button>`;
          }

          let migrateBtn = '';
          if (window.permissions && window.permissions.canMigrate) {
              migrateBtn = `<button type="button" class="btn btn-icon btn-flat-${full.is_migrated ? 'success' : 'secondary'} migrate-btn" title="${full.is_migrated ? 'تم الترحيل' : 'ترحيل'}" data-id="${full.id}" data-url="/printers/toggle-migrate/${full.id}">
                             <i class="feather icon-${full.is_migrated ? 'check-circle' : 'circle'}"></i>
                         </button>`;
          }

          return editBtn + `<button type="button" class="btn btn-icon btn-flat-primary duplicate-order-btn" title="إعادة تشغيل" data-id="${full.id}">
                            <i class="feather icon-copy"></i>
                        </button>` + migrateBtn;

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
        targets: 3, // Model Number (orderNumber)
        render: function (data, type, full, meta) {
          if (!data || data.startsWith('ORD-')) return '-';
          return '<span class="badge badge-light-primary">' + data + '</span>';
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

  // Toggle Migrate
  $(document).on('click', '.migrate-btn', function (e) {
    e.preventDefault();
    var $btn = $(this);
    var orderId = $btn.data('id');
    var url = $btn.data('url');
    var isMigrated = $btn.attr('title') === 'تم الترحيل';

    function doToggle() {
      $.ajax({
        url: url,
        type: 'POST',
        data: {
          _token: $('meta[name="csrf-token"]').attr('content')
        },
        success: function (response) {
          if (response.success) {
            if (response.is_migrated) {
              $btn.removeClass('btn-flat-secondary').addClass('btn-flat-success').attr('title', 'تم الترحيل');
              $btn.find('i').removeClass('icon-circle').addClass('icon-check-circle');
              toastr.success('تم الترحيل بنجاح');
            } else {
              $btn.removeClass('btn-flat-success').addClass('btn-flat-secondary').attr('title', 'ترحيل');
              $btn.find('i').removeClass('icon-check-circle').addClass('icon-circle');
              toastr.info('تم إلغاء الترحيل');
            }
          }
        },
        error: function (xhr) {
          toastr.error('حدث خطأ أثناء تغيير حالة الترحيل');
        }
      });
    }

    if (isMigrated) {
      var confirmCode = Math.floor(1000 + Math.random() * 9000).toString();
      Swal.fire({
        title: 'تأكيد إلغاء الترحيل',
        html: '<p>لإلغاء الترحيل، أدخل الرمز التالي:</p><h2 style="letter-spacing:8px;font-weight:bold;color:#d33;">' + confirmCode + '</h2>',
        input: 'text',
        inputPlaceholder: 'أدخل الرمز هنا',
        showCancelButton: true,
        confirmButtonText: 'تأكيد',
        cancelButtonText: 'إلغاء',
        confirmButtonClass: 'btn btn-danger',
        cancelButtonClass: 'btn btn-secondary ml-1',
        buttonsStyling: false,
        inputValidator: function (value) {
          if (value !== confirmCode) {
            return 'الرمز غير صحيح! حاول مرة أخرى';
          }
        }
      }).then(function (result) {
        if (result.value) {
          doToggle();
        }
      });
    } else {
      doToggle();
    }
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
    var grandTotalCost = 0;
    var pieceCostDisplay = '';
    var isSingleRow = false;

    var selectedRows = table.rows({ selected: true }).data();
    var count = selectedRows.count();
    var anyChecked = count > 0;

    if (anyChecked) {
      isSingleRow = (count === 1);

      selectedRows.each(function (row) {
        var meters = parseFloat(row.meters) || 0;
        var height = parseFloat(row.fileHeight) || 0;
        var picCopies = parseFloat(row.picInCopies) || 0;

        // Price Calculation Logic
        // User Requested: Total Cost = Meters * Machine Price (based on pass)
        // Ignoring printingprices.totalPrice for the Total Cost calculation per user request

        var pricePerMeter = 0;
        if (row.machines) {
          var pass = parseInt(row.pass) || 0;
          if (pass === 4) pricePerMeter = parseFloat(row.machines.price_4_pass);
          else if (pass === 6) pricePerMeter = parseFloat(row.machines.price_6_pass);
          else pricePerMeter = parseFloat(row.machines.price_1_pass);
        }

        totalMeters += meters;
        grandTotalCost += (meters * pricePerMeter);

        // Calculate Price Per Piece for this row
        // Formula: (Height / 100) / PicCopies * PricePerMeter
        if (isSingleRow) {
          if (picCopies > 0 && height > 0) {
            var onePieceCost = (height / 100) / picCopies * pricePerMeter;
            pieceCostDisplay = onePieceCost.toFixed(2);
          } else {
            pieceCostDisplay = '0.00';
          }
        }
      });

      var html = '';
      if (totalMeters > 0) {
        html += '<span class="badge badge-info mb-1" style="font-size: 1em; margin-left:15px;"><i class="feather icon-maximize-2"></i>  طول الورق : ' + totalMeters.toFixed(2) + ' متر</span>';
      }

      if (grandTotalCost > 0) {
        html += '<span class="badge badge-primary mb-1" style="font-size: 1em; margin-left:15px;"><i class="feather icon-dollar-sign"></i> الاجمالي : ' + grandTotalCost.toFixed(2) + ' جنيه</span>';
      }

      if (isSingleRow && pieceCostDisplay !== '') {
        html += '<span class="badge badge-success mb-1" style="font-size: 1em; margin-left:15px;"><i class="feather icon-tag"></i> سعر القطعة : ' + pieceCostDisplay + ' جنيه</span>';
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

      // Update cart count
      if (response.cart_count !== undefined) {
        $('.cart-item-count').text(response.cart_count);
        $('.badge.badge-up.cart-item-count').text(response.cart_count);
      }

      // Update cart dropdown HTML
      if (response.cart_html) {
        $('#cart-dropdown-items').html(response.cart_html);
      }
    }).fail(function () {
      toastr.error('حدث خطأ');
    });
  }

  // Image Zoom Logic
  $(document).on('click', '.product-img img', function () {
    var src = $(this).attr('src');
    if (src) {
      $('#enlarged-image').attr('src', src);
      $('#imageZoomModal').modal('show');
    }
  });

  // Close Zoom Modal
  $('.close-zoom').on('click', function () {
    $('#imageZoomModal').modal('hide');
  });

  // ==========================================
  // --- Edit Order Sidebar Logic & Dropzone ---
  // ==========================================
  var editingOrderId = null;
  var uploadedImagePaths = [];

  // Calculate & Update Prices in edit sidebar
  $('#data-copies, #data-height, #data-price, #data-pic-copies, #data-machine, #data-pass').on('input change', function () {
    var copies = parseFloat($('#data-copies').val()) || 0;
    var height = parseFloat($('#data-height').val()) || 0;

    var machineId = $('#data-machine').val();
    var pass = $('#data-pass').val();
    var price = parseFloat($('#data-price').val()) || 0;

    if (machineId && window.papionInvData && window.papionInvData.machines) {
      var machine = window.papionInvData.machines.find(m => m.id == machineId);
      if (machine) {
        if (pass == 4) {
          price = parseFloat(machine.price_4_pass);
        } else if (pass == 6) {
          price = parseFloat(machine.price_6_pass);
        } else {
          price = parseFloat(machine.price_1_pass);
        }
        if (price > 0) {
          $('#data-price').val(price);
        }
      }
    }

    var meters = copies * height;
    $('#data-meters').val((meters / 100).toFixed(2));

    var picCopies = parseFloat($('#data-pic-copies').val()) || 0;
    var totalpic = copies * picCopies;
    var pricePerPiece = 0;
    if (picCopies > 0 && price > 0) {
      pricePerPiece = (height / 100) / picCopies * price;
    }

    $('#data-price-pic').text(pricePerPiece.toFixed(2));
    $('#data-total-pic').text(totalpic);

    if (copies > 0 && height > 0) {
      $('#data-meters').prop('disabled', true);
    } else {
      $('#data-meters').prop('disabled', false);
    }
  });

  // Handle Machine Selection in Sidebar
  $('#data-machine').on('change', function () {
    var selectedText = $(this).find("option:selected").text().toLowerCase();

    if (selectedText.includes('dtf')) {
      $('#data-width').val(58);
      $('#data-pass').val(4).prop('disabled', false);
    } else if (selectedText.includes('sublimation')) {
      $('#data-width').val(150);
      $('#data-pass').val(1).prop('disabled', true);
    } else {
      $('#data-pass').prop('disabled', false);
    }
  });

  // Customer Datalist Input Handler
  $(document).on('input', '#data-customer-view', function () {
    var val = $(this).val();
    var id = '';
    var opt = $('#customers-list option').filter(function () {
      return $(this).val() === val;
    });
    if (opt.length > 0) id = opt.attr('data-id');
    $('#data-customer').val(id);
  });

  // Dropzone setup
  Dropzone.autoDiscover = false;
  if (Dropzone.instances.length > 0) {
    Dropzone.instances.forEach(dz => dz.destroy());
  }

  try {
    var myDropzone = new Dropzone("#dataListUpload", {
      url: "/printers/upload-image",
      paramName: "file",
      maxFiles: 10,
      acceptedFiles: '.jpg,.jpeg,.png,.gif,.tiff,.tif,.webp',
      addRemoveLinks: true,
      resizeHeight: 110,
      resizeMimeType: 'image/webp',
      resizeQuality: 0.9,
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      success: function (file, response) {
        file.serverFileName = response.path;
        uploadedImagePaths.push(response.path);
        toastr.success("تم رفع الصورة بنجاح");
      },
      removedfile: function (file) {
        if (file.previewElement != null && file.previewElement.parentNode != null) {
          file.previewElement.parentNode.removeChild(file.previewElement);
        }
        var path = file.serverFileName;
        if (path) {
          var index = uploadedImagePaths.indexOf(path);
          if (index !== -1) {
            uploadedImagePaths.splice(index, 1);
          }
        }
      },
      error: function (file, response) {
        toastr.error("فشل رفع الصورة: " + (response ? response.message : ''));
      }
    });
  } catch (e) {
    console.warn("Dropzone init warning:", e);
  }

  function resetEditForm() {
    $('#data-customer, #data-customer-view, #data-machine, #data-height, #data-width, #data-copies, #data-pic-copies, #data-pass, #data-meters, #data-price, #data-notes, #data-fabric-type, #data-model-number, #edit-order-id').val('');
    $('#data-status').val('بانتظار اجراء');
    $('#data-pass').val('1');
    $('#data-price-pic').text('0');
    $('#data-total-pic').text('0');
    uploadedImagePaths = [];
    if (typeof myDropzone !== 'undefined' && myDropzone) {
      myDropzone.removeAllFiles(true);
    }
    editingOrderId = null;
  }

  // Open Edit Sidebar
  $(document).on("click", ".edit-order-btn, .action-edit", function (e) {
    e.stopPropagation();
    var orderId = $(this).data('id');
    if (!orderId) {
      var $row = $(this).closest('tr');
      orderId = $row.find('.order_id').val();
    }

    if (!orderId) return;

    $.ajax({
      url: "/printers/" + orderId,
      type: "GET",
      success: function (order) {
        $('#edit-order-id').val(order.id);
        $('#data-customer-view').val(order.customers ? order.customers.name : '');
        $('#data-machine').val(order.machineId);
        $('#data-height').val(order.fileHeight);
        $('#data-width').val(order.fileWidth);
        $('#data-copies').val(order.fileCopies);
        $('#data-pic-copies').val(order.picInCopies);
        $('#data-fabric-type').val(order.fabric_type);
        $('#data-pass').val(order.pass);
        $('#data-meters').val(order.meters);
        $('#data-status').val(order.status);
        if (order.printingprices) {
          $('#data-price').val(order.printingprices.totalPrice);
        }
        $('#data-notes').val(order.notes);
        $('#data-price-pic').text(order.manufacturing_cost || 0);

        var orderNum = order.orderNumber || '';
        $('#data-model-number').val(orderNum.startsWith('ORD-') ? '' : orderNum);

        // Populate Images in Dropzone
        uploadedImagePaths = [];
        if (typeof myDropzone !== 'undefined' && myDropzone) {
          myDropzone.removeAllFiles(true);
          if (order.orders_imgs && order.orders_imgs.length > 0) {
            order.orders_imgs.forEach(function (img) {
              var mockFile = { name: "Image", size: 12345, serverFileName: img.path };
              myDropzone.emit("addedfile", mockFile);
              myDropzone.emit("thumbnail", mockFile, "/storage/" + img.path);
              myDropzone.emit("complete", mockFile);
              myDropzone.files.push(mockFile);
              uploadedImagePaths.push(img.path);
            });
          }
        }

        var copies = parseFloat($('#data-copies').val()) || 0;
        var picCopies = parseFloat($('#data-pic-copies').val()) || 0;
        $('#data-total-pic').text(copies * picCopies);

        editingOrderId = order.id;

        $(".add-new-data").addClass("show");
        $(".overlay-bg").addClass("show");
      },
      error: function (xhr) {
        console.error("Error fetching order:", xhr);
        toastr.error("تعذر جلب تفاصيل الطلب.", "خطأ");
      }
    });
  });

  // Close Edit Sidebar
  $(document).on("click", ".hide-data-sidebar, .cancel-data-btn, .overlay-bg", function (e) {
    e.stopPropagation();
    $(".add-new-data").removeClass("show");
    $(".overlay-bg").removeClass("show");
    resetEditForm();
  });

  // Save Edit Data Button Handler
  $('#saveDataBtn').on('click', function (e) {
    e.preventDefault();
    if (!editingOrderId) return;

    var customerId = $('#data-customer-view').val();
    var machineId = $('#data-machine').val();
    var height = $('#data-height').val();
    var width = $('#data-width').val();
    var copies = $('#data-copies').val();
    var picInCopies = $('#data-pic-copies').val();
    var pass = $('#data-pass').val();
    var meters = $('#data-meters').val();
    var status = $('#data-status').val();
    var fabric_type = $('#data-fabric-type').val();
    var price = $('#data-price').val();
    var notes = $('#data-notes').val();
    var manufacturing_cost = parseFloat($('#data-price-pic').text()) || 0;

    var postData = {
      _method: 'PUT',
      customerId: customerId,
      machineId: machineId,
      fileHeight: height,
      fileWidth: width,
      fileCopies: copies,
      picInCopies: picInCopies,
      pass: pass,
      meters: meters,
      status: status,
      price: price,
      fabric_type: fabric_type,
      notes: notes,
      manufacturing_cost: manufacturing_cost,
      image_paths: uploadedImagePaths,
      orderNumber: $('#data-model-number').val() || null,
      _token: $('meta[name="csrf-token"]').attr('content')
    };

    $.ajax({
      url: "/printers/" + editingOrderId,
      type: "POST",
      data: postData,
      success: function (response) {
        toastr.success("تم تحديث أمر الطباعة بنجاح", "تمت العملية بنجاح");
        $(".add-new-data").removeClass("show");
        $(".overlay-bg").removeClass("show");
        resetEditForm();
        table.draw(false);
      },
      error: function (xhr) {
        console.error("Error updating order:", xhr);
        var message = "حدث خطأ أثناء حفظ التعديلات.";
        if (xhr.responseJSON && xhr.responseJSON.message) {
          message = xhr.responseJSON.message;
        }
        toastr.error(message, "خطأ");
      }
    });
  });

});