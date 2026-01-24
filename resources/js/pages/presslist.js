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
        text: "<i class='feather icon-plus'></i> Add New",
        action: function () {
          // Slide down logic for the wizard form
          $('#validation').slideToggle();
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

  // Handle Machine Selection
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





  // Global variable to track edit state
  var editingOrderId = null;

  function resetForm() {
    $('#data-customer, #data-customer-view, #data-fabric-type, #data-source, #data-code, #data-width, #data-paper-shield, #data-meters, #data-price, #data-notes').val('');
    $('#data-status').val('بانتظار اجراء');
    $('#data-payment-status').val('0');
    $('#data-image-upload').val(''); // Reset file input
    editingOrderId = null;
    $('.new-data-title h4').text('اضافه اذن تشغيل');
    $('#saveDataBtn').text('Add Data');
  }


  // Dropzone removed in favor of simple file input
  var uploadedImagePaths = []; // Keep for compatibility if needed, though mostly unused now for upload


  // Handle Paste Event
  document.onpaste = function (event) {
    var items = (event.clipboardData || event.originalEvent.clipboardData).items;
    for (var index = 0; index < items.length; index++) {
      var item = items[index];
      if (item.kind === 'file') {
        // add file to dropzone
        myDropzone.addFile(item.getAsFile());
      }
    }
  };

  ////////////////////////////// On Edit /////////////////////////
  $(document).on("click", ".action-edit", function (e) {
    e.stopPropagation();
    var $row = $(this).closest('tr');
    var orderId = $row.find('.order_id').val();

    if (!orderId) return;

    $.ajax({
      url: "/printers/" + orderId,
      type: "GET",
      success: function (order) {
        // Populate Form
        $('#data-customer-view').val(order.customers ? order.customers.name : '');
        $('#data-fabric-type').val(order.fabrictype);
        $('#data-source').val(order.fabricsrc);
        $('#data-code').val(order.fabriccode);
        $('#data-width').val(order.fabricwidth);
        $('#data-paper-shield').val(order.papyershild);
        $('#data-meters').val(order.meters);
        $('#data-status').val(order.status);
        $('#data-payment-status').val(order.paymentstatus);
        $('#data-price').val(order.price);
        $('#data-notes').val(order.notes);

        editingOrderId = order.id;
        $('.new-data-title h4').text('تعديل البيانات');
        $('#saveDataBtn').text('حفظ التعديلات');

        $(".add-new-data").addClass("show");
        $(".overlay-bg").addClass("show");
      },
      error: function (xhr) {
        console.error("Error fetching order:", xhr);
        toastr.error("Could not fetch order details.", "Error");
      }
    });
  });


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
          toastr.success(message, "تمت العملية بنجاح");

          // Refresh page or update table. Since validation logic is simple, let's reload for now to reflect new data from DB properly
          setTimeout(function () {
            location.reload();
          }, 1000);

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

    if (currentStatus === 'بانتظار اجراء' || currentStatus === 'بانتظار اجراء') {
      e.preventDefault();

      $.ajax({
        url: "/printers/" + orderId,
        type: "GET",
        success: function (order) {
          // Populate Form
          $('#data-customer-view').val(order.customers ? order.customers.name : '');
          $('#data-machine').val(order.machineId);
          $('#data-height').val(order.fileHeight);
          $('#data-width').val(order.fileWidth);
          $('#data-copies').val(order.fileCopies);
          $('#data-pic-copies').val(order.picInCopies);
          $('#data-pass').val(order.pass);
          $('#data-meters').val(order.meters);
          $('#data-status').val(order.status);
          if (order.printingprices) {
            $('#data-price').val(order.printingprices.totalPrice);
          }
          $('#data-notes').val(order.notes);

          editingOrderId = order.id;
          $('.new-data-title h4').text('تحديث الطلب وبدات الطباعة');
          $('#saveDataBtn').text('تحديث وبدء');

          $(".add-new-data").addClass("show");
          $(".overlay-bg").addClass("show");
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

  // Use the table variable initialized above.
  // We do NOT re-initialize it here.

  // Initially hide actions dropdown if it exists logic isn't handled by CSS
  // Note: data-list-view.js moves .actions-dropodown to the toolbar.
  // We want to hide it when no rows are selected.
  $('.actions-dropodown').hide();

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