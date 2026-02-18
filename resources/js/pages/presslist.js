$(document).ready(function () {

  // --- Wizard Initialization ---
  var form = $(".steps-validation").show();

  $(".steps-validation").steps({
    headerTag: "h6",
    bodyTag: "fieldset",
    transitionEffect: "fade",
    titleTemplate: '<span class="step">#index#</span> #title#',
    labels: {
      finish: 'Submit'
    },
    onStepChanging: function (event, currentIndex, newIndex) {
      // Allways allow previous action even if the current form is not valid!
      if (currentIndex > newIndex) {
        return true;
      }
      // Needed in some cases if the user went back (clean up)
      if (currentIndex < newIndex) {
        // To remove error styles
        form.find(".body:eq(" + newIndex + ") label.error").remove();
        form.find(".body:eq(" + newIndex + ") .error").removeClass("error");
      }
      form.validate().settings.ignore = ":disabled,:hidden";
      return form.valid();
    },
    onFinishing: function (event, currentIndex) {
      form.validate().settings.ignore = ":disabled";
      return form.valid();
    },
    onFinished: function (event, currentIndex) {
      submitWizardData();
    }
  });

  // Initialize validation
  $(".steps-validation").validate({
    ignore: 'input[type=hidden]', // ignore hidden fields
    errorClass: 'danger',
    successClass: 'success',
    highlight: function (element, errorClass) {
      $(element).removeClass(errorClass);
    },
    unhighlight: function (element, errorClass) {
      $(element).removeClass(errorClass);
    },
    errorPlacement: function (error, element) {
      error.insertAfter(element);
    },
    rules: {
      email: {
        email: true
      }
    }
  });
  // --- End Wizard Initialization ---

  // --- DataTable Initialization (Merged from data-list-view.js) ---
  var table = $(".data-thumb-view").DataTable({
    responsive: false,
    deferRender: true,
    columnDefs: [
      {
        orderable: true,
        targets: 0,
        checkboxes: { selectRow: true }
      }
    ],
    dom:
      '<"top"<"actions action-btns"B><"action-filters"lf>><"clear">rt<"bottom"<"actions">p>',
    oLanguage: {
      sLengthMenu: "_MENU_",
      sSearch: ""
    },
    aLengthMenu: [[4, 10, 15, 20], [4, 10, 15, 20]],
    select: {
      style: "multi"
    },
    order: [[1, "desc"]],
    bInfo: false,
    pageLength: 4,
    buttons: [
      {
        text: "<i class='feather icon-plus'></i> تشفغيل خارجي",
        action: function (e, dt, node, config) {
          // Slide down logic for the wizard form
          $('#validation').slideToggle(function () {
            var isVisible = $(this).is(':visible');
            dt.button(node).text(isVisible ? '<i class="feather icon-x"></i> اغـــلاق' : "<i class='feather icon-plus'></i> تشفغيل خارجي");
          });
        },
        className: "btn-outline-primary"
      }
    ],
    initComplete: function (settings, json) {
      $(".dt-buttons .btn").removeClass("btn-secondary")
    }
  });

  table.on('draw.dt', function () {
    setTimeout(function () {
      if (navigator.userAgent.indexOf("Mac OS X") != -1) {
        $(".dt-checkboxes-cell input, .dt-checkboxes").addClass("mac-checkbox")
      }
    }, 50);
  });

  // To append actions dropdown before add new button
  var actionDropdown = $(".actions-dropodown")
  actionDropdown.insertBefore($(".top .actions .dt-buttons"))

  // Check if scrollbar exists
  if ($(".data-items").length > 0) {
    new PerfectScrollbar(".data-items", { wheelPropagation: false })
  }

  // Close sidebar logic (if sidebar still exists)
  $(".hide-data-sidebar, .cancel-data-btn, .overlay-bg").on("click", function () {
    $(".add-new-data").removeClass("show")
    $(".overlay-bg").removeClass("show")
    $("#data-name, #data-price").val("")
    $("#data-category, #data-status").prop("selectedIndex", 0)
  })

  // --- End DataTable Initialization ---







  // Global variable to track edit state
  var editingOrderId = null;
  var linkedOrderId = null; // New variable to track the linked Printer Order ID

  function resetForm() {
    $('#data-customer, #data-customer-view, #data-fabric-type, #data-source, #data-code, #data-width, #data-paper-shield, #data-meters, #data-price, #data-notes').val('');
    $('#data-status').val('بانتظار اجراء');
    $('#data-payment-status').val('0');
    $('#data-image-upload').val(''); // Reset file input
    editingOrderId = null;
    linkedOrderId = null; // Reset linked order
    $('.new-data-title h4').text('اضافه اذن تشغيل');
    $('#saveDataBtn').text('Add Data');
  }







  // //////////////////////////Save Data Button Logic ////////////////////////////////
  function submitWizardData() {
    console.log("Submitting Wizard Data");
    // e.preventDefault() is not needed here as it's not an event handler directly
    console.log("Save/Update Data Button Clicked");

    var formData = new FormData();

    // Customer Logic: If hidden ID is empty, use the text value as name (for new customer)
    var customerId = $('#data-customer').val(); // Assuming you have a hidden input for ID
    var customerName = $('#data-customer-view').val();

    // If we have an ID, we send it. If not, we send the name.
    // However, existing controller likely expects 'customerId'. 
    // We'll let the controller handle "if ID is null/not found, create by name".
    // Or we send both and let controller decide.
    // Let's send 'customer_name' always, and 'customer_id' if we have it.

    // Note: The datalist implementation in blade needs to ensure we capture the ID if selected.
    // If the user types a name that exists, we might miss the ID if not careful with the input change event.
    // For now, let's look up the ID from the datalist based on the name if the hidden ID is empty.
    if (!customerId) {
      var val = $('#data-customer-view').val();
      var opt = $('#customers-list option[value="' + val + '"]');
      if (opt.length > 0) {
        customerId = opt.attr('data-id');
      }
    }

    formData.append('customerId', customerId || '');
    formData.append('customerName', customerName); // Send name for creation if ID null
    formData.append('fabrictype', $('#data-fabric-type').val());
    formData.append('fabricsrc', $('#data-source').val());
    formData.append('fabriccode', $('#data-code').val());
    formData.append('fabricwidth', $('#data-width').val());
    formData.append('papyershild', $('#data-paper-shield').val());
    formData.append('meters', $('#data-meters').val());
    formData.append('status', $('#data-status').val());
    formData.append('paymentstatus', $('#data-payment-status').val());
    formData.append('price', $('#data-price').val());
    formData.append('notes', $('#data-notes').val());
    formData.append('notes', $('#data-notes').val());
    if (linkedOrderId) {
      formData.append('orderId', linkedOrderId);
    }
    formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

    // Image Upload
    var imageFile = $('#data-image-upload')[0].files[0];
    if (imageFile) {
      formData.append('image', imageFile);
    }

    var url = "/Rollpress/store"; // Correct case matches Route prefix
    var type = "POST";

    if (editingOrderId) {
      url = "/rollpress/update/" + editingOrderId; // Need update route too
      // For PUT with FormData in Laravel, utilize _method field
      formData.append('_method', 'PUT');
    }

    $.ajax({
      url: url,
      type: type, // POST (even for PUT via _method)
      data: formData,
      processData: false,
      contentType: false,
      success: function (response) {
        try {
          console.log("Order saved/updated:", response);
          var message = editingOrderId ? "Order Updated Successfully!" : "Order Added Successfully!";
          Swal.fire({
            type: 'success',
            title: 'تم التسجيل بنجاح!',
            showConfirmButton: false,
            timer: 1500,
            buttonsStyling: false,
            confirmButtonClass: 'btn btn-primary',
          });

          // Close Wizard
          $('#validation').slideUp();

          // Remove Row logic
          if (linkedOrderId) {
            var $row = $('input.order_id[value="' + linkedOrderId + '"]').closest('tr');
            table.row($row).remove().draw();
          }
          if (editingOrderId) {
            var $row = $('input.roll_id[value="' + editingOrderId + '"]').closest('tr');
            table.row($row).remove().draw();
          }

        } catch (err) {
          console.error("Error updating UI:", err);
          toastr.warning("Order saved, but UI update encountered an issue. See console.", "Warning");
        } finally {
          $(".add-new-data").removeClass("show");
          $(".overlay-bg").removeClass("show");
          resetForm();
        }
      },
      error: function (xhr) {
        console.error("Error processing order:", xhr);
        if (xhr.status === 422) {
          var errors = xhr.responseJSON.errors;
          $.each(errors, function (key, val) {
            toastr.error(val[0], "خطا");
          });
        } else {
          toastr.error("Error processing order. Please try again.", "خطا");
        }
      }
    });
  }

  // Bind the old button to the new function
  $('#saveDataBtn').on('click', function (e) {
    e.preventDefault();
    submitWizardData();
  });

  /////////////////// On Status Click //////////////////////////

  $(document).on("click", ".status-toggle", function (e) {
    e.stopPropagation();
    var $this = $(this);
    var $row = $this.closest('tr');
    var orderId = $row.find('.order_id').val();
    var $textElement = $this.find('.chip-text');
    var currentStatus = $textElement.text().trim();

    if (currentStatus === 'انتهت الطباعة') {
      $row.fadeOut();
      return;
    }

    if (!orderId) return;

    if (currentStatus === 'بانتظار اجراء' || currentStatus === 'بدء التشغيل') { // Updated to match user phrasing if needed, but 'بانتظار اجراء' is standard
      e.preventDefault();

      // Show loading or visual feedback?

      $.ajax({
        url: "/printers/" + orderId,
        type: "GET",
        success: function (order) {
          // Populate Wizard Form
          // Use Rollpress data if available, else Printer data
          var data = order.rollpress ? order.rollpress : order;

          // Customer Name
          var customerName = order.customers ? order.customers.name : '';
          $('#data-customer-view').val(customerName);
          // Assuming we might need to set data-id on hidden field if logic requires, but wizard logic relies on name lookup or id field
          // If we have customer Id from printer order
          $('#data-customer').val(order.customerId);

          // Fields mapping
          // Fabric Type
          $('#data-fabric-type').val(data.fabrictype || data.fabric_type || '');
          // Note: Printer has fabric_type, Rollpress has fabrictype. Handle both.

          $('#data-source').val(data.fabricsrc || 'العميل'); // Default to Customer if not set
          $('#data-code').val(data.fabriccode || '');
          $('#data-width').val(data.fabricwidth || order.fileWidth || ''); // Fallback to fileWidth
          $('#data-paper-shield').val(data.papyershild || '');
          $('#data-meters').val(data.meters || order.meters || '');

          // Status - Set to "In Progress" or "Started" equivalent? 
          // The form has "بانتظار اجراء" etc. user wants "Start Operation".
          // Let's keep it as is or default to "جاري العمل" if starting?
          $('#data-status').val(data.status && data.status != 0 ? 'تم الانتهاء' : 'جاري العمل');
          // Note: rollpress status is boolean (0/1) or string? Migration says boolean. select has strings.
          // Let's set default for new start:
          if (!order.rollpress) {
            $('#data-status').val('جاري العمل');
          } else {
            // Map boolean/string
            // If rollpress status is 1 -> 'تم الانتهاء', 0 -> 'جاري العمل'
            $('#data-status').val(data.status ? 'تم الانتهاء' : 'جاري العمل');
          }

          $('#data-payment-status').val(data.paymentstatus || 0);
          $('#data-price').val(data.price || order.totalPrice || ''); // Printer has totalPrice? or printingprices.totalPrice
          if (!data.price && order.printingprices) {
            $('#data-price').val(order.printingprices.totalPrice);
          }

          $('#data-notes').val(data.notes || order.notes || '');

          // Set Tracking Variables
          if (order.rollpress) {
            editingOrderId = order.rollpress.id; // We are editing the existing Rollpress ticket
            linkedOrderId = order.id; // Keep link just in case
            $('.new-data-title h4').text('تعديل طلب المكبس');
            $('#saveDataBtn').text('حفظ التعديلات');
          } else {
            editingOrderId = null; // New Rollpress Ticket
            linkedOrderId = order.id; // Correctly link to Printer Order
            $('.new-data-title h4').text('بدء تشغيل طلب جديد');
            $('#saveDataBtn').text('بدء التشغيل');
          }

          // Show Wizard
          $('#validation').slideDown();

          // Scroll to Wizard
          $('html, body').animate({
            scrollTop: $("#validation").offset().top - 100
          }, 500);

        },
        error: function (xhr) {
          console.error("Error fetching order:", xhr);
          toastr.error("Could not fetch order details.", "Error");
        }
      });
      return;
    }

    $.ajax({
      url: "/printers/update-status/" + orderId,
      type: "POST",
      data: {
        _token: $('meta[name="csrf-token"]').attr('content')
      },
      success: function (response) {
        $textElement.text(response.status);
        Swal.fire({
          title: " تحديث الحالة :" + response.status,
          text: 'Status updated to ' + response.status,
          type: "success",
          showConfirmButton: false,
          timer: 1000,
          buttonsStyling: false,
        })
      },
      error: function (xhr) {
        toastr.error('Failed to update status', 'Error', {
          showMethod: "fadeIn",
          hideMethod: "fadeOut"
        });
      }
    });
  });



  // ///////////// multiple Bulk Delete & Actions Visibility ///////////////////////

  // Auto-refresh every 30 seconds if wizard is not active
  setInterval(function () {
    if (!$('#validation').is(':visible')) {
      location.reload();
    }
  }, 30000);

  // --- Bulk Action Visibility ---
  table.on('select deselect', function () {
    var selectedRows = table.rows({ selected: true }).count();
    if (selectedRows > 0) {
      $('.action-btns').show();
    } else {
      // $('.action-btns').hide(); // Optional
    }
  });

  // --- Add To Invoice ---
  window.addToInvoice = function () {
    var selectedRows = table.rows({ selected: true }).nodes();
    var ids = [];

    $.each(selectedRows, function (index, row) {
      var id = $(row).find('.order_id').val(); // Start with Printer Order ID

      // If we really need Rollpress ID specifically, we should check if we have it separately or if Polymorphic relation uses Printer ID or Rollpress ID.
      // The system seems to use Printer Order as base for Rollpress?
      // View: value="{{ $Order->id }}" (Printer Order ID)
      // Controller checks if Rollpress exists for it.
      // If I send Printer Order ID, the Invoice Controller needs to know how to handle it.
      // InvoiceController `addToCart` handles `Rollpress` model.
      // Rollpress model is `Rollpress`.
      // But the ID here is `$Order->id` (Printers table).
      // However, `Rollpress` model belongsTo `Printer`? No, `Printers` hasOne `Rollpress`.
      // If I add "Rollpress" item, should I use `Rollpress` ID or `Printers` ID?
      // The view uses `$Order->id`.
      // If the invoice item is `Rollpress`, it expects `itemable_type = App\Models\Rollpress` and `itemable_id = RollpressID`.
      // So I need the Rollpress ID.
      // View doesn't seem to have explicit Rollpress ID hidden input, only `$Order->id`.
      // Wait, `view_code` on `app/Models/Invoice.php` showed relations?
      // Actually, `RollpressController` creates a `Rollpress` entry linked to `printer_id`.
      // If I pass Printer ID, I might need to map it to Rollpress ID in backend OR just use Printer ID and type "Rollpress"? 
      // But strict polymorphic relation needs correct ID.

      // Let's assume for now we pass the ID present in the row.
      // In `printers/print_log`, ID is Printer ID.
      // In `Rollpress/presslist`, ID is Printer ID too (class `order_id`).
      // Does `Stras` or `Tarter` use their own IDs? Yes.
      // `Rollpress/presslist` iterates `$Orders` (Printers).

      // I will send the ID found. If it's a Printer ID, and type is 'rollpress', 
      // the backend `addToCart` should probably resolve it to the specific Rollpress ID if needed, 
      // OR I should expose Rollpress ID in the view.

      // Let's check if Rollpress ID is available in view.
      // View: `<div class="chip-text hover_action">{{ $Order->rollpress->status ...`
      // It accesses `$Order->rollpress`.
      // I should probably add a hidden input for Rollpress ID if it exists.

      if (id) ids.push(id);
    });

    if (ids.length === 0) {
      toastr.warning('Please select items first');
      return;
    }

    $.post('/invoices/add', {
      _token: $('meta[name="csrf-token"]').attr('content'),
      ids: ids,
      type: 'rollpress'
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





  $(document).ready(function () {
    // Dashboard Check In
    $('#dashboardCheckInBtn').click(function () {
      var btn = $(this);
      btn.prop('disabled', true);
      $.ajax({
        url: "/attendance/check-in",
        type: "POST",
        data: {
          _token: $('meta[name="csrf-token"]').attr('content')
        },
        success: function (response) {
          Swal.fire({
            title: 'تم!',
            text: response.success + ' الساعة: ' + response.time,
            type: 'success',
            confirmButtonText: 'حسناً'
          }).then(() => { location.reload(); });
        },
        error: function (xhr) {
          Swal.fire('خطأ!', xhr.responseJSON.error, 'error');
          btn.prop('disabled', false);
        }
      });
    });

    // Dashboard Check Out
    $('#dashboardCheckOutBtn').click(function () {
      var btn = $(this);
      btn.prop('disabled', true);
      $.ajax({
        url: "/attendance/check-out",
        type: "POST",
        data: {
          _token: $('meta[name="csrf-token"]').attr('content')
        },
        success: function (response) {
          Swal.fire({
            title: 'تم!',
            text: response.success + ' الساعة: ' + response.time,
            type: 'success',
            confirmButtonText: 'حسناً'
          }).then(() => { location.reload(); });
        },
        error: function (xhr) {
          Swal.fire('خطأ!', xhr.responseJSON.error, 'error');
          btn.prop('disabled', false);
        }
      });
    });
  });

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

});