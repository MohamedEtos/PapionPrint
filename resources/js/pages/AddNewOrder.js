$(document).ready(function () {

  // culculate meters 

  $('#data-copies, #data-height,  #data-price').on('input', function () {
    var copies = parseFloat($('#data-copies').val()) || 0;
    var height = parseFloat($('#data-height').val()) || 0;
    var price = parseFloat($('#data-price').val()) || 0;

    var meters = copies * height;
    $('#data-meters').val((meters / 100));

    var total = meters * price;
    $('#data-total').val(total);

    // Disable meters input if valid calculation exists
    if (copies > 0 && height > 0) {
      $('#data-meters').prop('disabled', true);
    } else {
      $('#data-meters').prop('disabled', false);
    }
  });

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
    $('#data-customer, #data-customer-view, #data-machine, #data-height, #data-width, #data-copies, #data-pic-copies, #data-pass, #data-meters, #data-price, #data-notes').val('');
    $('#data-status').val('waiting');
    $('#data-pass').val('1');
    uploadedImagePaths = [];
    if (typeof myDropzone !== 'undefined' && myDropzone) {
      myDropzone.removeAllFiles();
    }
    editingOrderId = null;
    $('.new-data-title h4').text('اضافه اذن تشغيل');
    $('#saveDataBtn').text('Add Data');
  }


  // Configure Dropzone
  Dropzone.autoDiscover = false;

  var uploadedImagePaths = [];

  // Initialize Dropzone safely
  if (Dropzone.instances.length > 0) {
    Dropzone.instances.forEach(dz => dz.destroy());
  }

  try {
    var myDropzone = new Dropzone("#dataListUpload", {
      url: "/printers/upload-image",
      paramName: "file",
      maxFiles: 10,
      acceptedFiles: 'image/*',
      addRemoveLinks: true,
      resizeHeight: 110,
      resizeMimeType: 'image/webp',
      resizeQuality: 0.9,
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      success: function (file, response) {
        uploadedImagePaths.push(response.path);
        console.log("Images uploaded:", uploadedImagePaths);
      },
      removedfile: function (file) {
        if (file.previewElement != null && file.previewElement.parentNode != null) {
          file.previewElement.parentNode.removeChild(file.previewElement);
        }
        // Ideally we should also remove from uploadedImagePaths array here
        return _updateMaxFilesReachedClass();
      },
      error: function (file, response) {
        console.error("Upload failed:", response);
      }
    });
  } catch (e) {
    console.error("Dropzone init warning:", e);
  }

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

  // On Edit
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

  // On Delete
  $(document).on("click", ".action-delete", function (e) {
    e.stopPropagation();
    var $row = $(this).closest('td').parent('tr');
    var orderId = $row.find('.order_id').val();

    console.log(orderId);
    if (!orderId) {
      $row.fadeOut(); // Just remove from text if no ID (newly added row before refresh? though usually we reload)
      return;
    }

    Swal.fire({
      title: 'هل انت متاكد?',
      text: "لن تتمكن من التراجع عن هذا!",
      type: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'نعم، احذفه!',
      confirmButtonClass: 'btn btn-primary',
      cancelButtonClass: 'btn btn-danger ml-1',
      buttonsStyling: false,
    }).then(function (result) {
      if (result.value) {
        $.ajax({
          url: "/printers/delete/" + orderId,
          type: "POST",
          data: {
            _token: $('meta[name="csrf-token"]').attr('content')
          },
          success: function (response) {
            console.log("Order deleted:", response);
            $row.fadeOut(function () {
              $(this).remove();
            });
            Swal.fire({
              type: 'success',
              title: 'تم الحذف!',
              showConfirmButton: false,
              timer: 1500,
              buttonsStyling: false,
              confirmButtonClass: 'btn btn-primary',
            });
          },
          error: function (xhr) {
            console.error("Error deleting order:", xhr);
            Swal.fire({
              title: "Error!",
              text: "Error deleting order. Please try again.",
              type: "error",
              confirmButtonClass: 'btn btn-primary',
              buttonsStyling: false,
            });
          }
        });
      }
    });
  });

  // on action-info
  $(document).on("click", ".action-info", function (e) {
    e.stopPropagation();
    $('#xlarge').modal('show');
  });

  // On Cancel / Close Sidebar
  $('.hide-data-sidebar, .cancel-data-btn, .overlay-bg').on("click", function (e) {
    e.stopPropagation();
    $(".add-new-data").removeClass("show");
    $(".overlay-bg").removeClass("show");
    resetForm();
  });

  // Save Data Button Logic
  $('#saveDataBtn').on('click', function (e) {
    e.preventDefault();
    console.log("Save/Update Data Button Clicked");

    var customerId = $('#data-customer-view').val();
    var machineId = $('#data-machine').val();
    var height = $('#data-height').val();
    var width = $('#data-width').val();
    var copies = $('#data-copies').val();
    var picInCopies = $('#data-pic-copies').val();
    var pass = $('#data-pass').val();
    var meters = $('#data-meters').val();
    var status = $('#data-status').val();
    var price = $('#data-price').val();
    var notes = $('#data-notes').val();

    var url = "/printers/store";
    var type = "POST";
    var data = {
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
      notes: notes,
      image_paths: uploadedImagePaths,
      _token: $('meta[name="csrf-token"]').attr('content')
    };

    if (editingOrderId) {
      url = "/printers/" + editingOrderId;
      data._method = 'PUT';
      data.auto_advance_status = true;
    }

    $.ajax({
      url: url,
      type: type,
      data: data,
      success: function (response) {
        console.log("Order saved/updated:", response);
        var message = editingOrderId ? "Order Updated Successfully!" : "Order Added Successfully!";
        toastr.success(message, "تمت العملية بنجاح");

        var order = response.order;

        // Helper Variables
        var customerName = order.customers ? order.customers.name : 'Unknown';
        var machineName = order.machines ? order.machines.name : 'Unknown';
        var pricePerMeter = order.printingprices ? order.printingprices.pricePerMeter : '';
        var imgPath = (order.orders_imgs && order.orders_imgs.length > 0)
          ? '/storage/' + order.orders_imgs[0].path
          : '/core/images/elements/apple-watch.png';

        if (editingOrderId) {
          var $row = $('input.order_id[value="' + editingOrderId + '"]').closest('tr');
          $row.find('.product-img img').attr('src', imgPath);
          $row.find('.product-name').text(customerName);

          // Fix: Target Machine column (index 3) explicitly
          var $machineCell = $row.find('td').eq(3);
          $machineCell.text(machineName + ' ' + order.pass + ' pass');

          // Fix: Target Meters column (index 4) explicitly and preserve formatting
          var $metersCell = $row.find('td').eq(4);
          $metersCell.html('<b>' + order.meters + '</b>');

          $row.find('.chip-text').text(order.status);
          $row.find('.chip').removeClass('chip-success chip-warning').addClass(order.status == 'تم الانتهاء' ? 'chip-success' : 'chip-warning');
          $row.find('td:eq(6)').text(pricePerMeter);
        } else {
          var newRow = `
                <tr>
                    <td></td>
                    <td class="product-img">
                         <input type="hidden" class="order_id" value="${order.id}">
                        <img src="${imgPath}" alt="Img placeholder">
                    </td>
                    <td class="product-name">${customerName}</td>
                    <td class="product-category">${machineName} ${order.pass} pass</td>
                    <td class="product-category"><b>${order.meters}</b></td>
                    <td>
                        <div class="chip chip-${order.status == 'تم الانتهاء' ? 'success' : 'warning'}">
                            <div class="chip-body status-toggle" style="cursor: pointer">
                                <div class="chip-text">${order.status}</div>
                            </div>
                        </div>
                    </td>
                    <td class="product-price">${pricePerMeter}</td>
                    <td class="product-price" title="Just now">الآن</td>
                    <td class="product-action">
                        <span class=" hover_action action-info " data-toggle="modal" data-target="#xlarge"><i class="feather icon-file"></i></span>
                        <span class=" hover_action action-edit "><i class="feather icon-edit"></i></span>
                        <span class=" hover_action action-delete text-danger " ><i class="feather icon-trash"></i></span>
                    </td>
                </tr>
            `;
          $('table.data-thumb-view tbody').append(newRow);
        }

        $(".add-new-data").removeClass("show");
        $(".overlay-bg").removeClass("show");
        resetForm();
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
  });

  // On Status Click
  $(document).on("click", ".status-toggle", function (e) {
    e.stopPropagation();
    var $this = $(this);
    var $row = $this.closest('tr');
    var orderId = $row.find('.order_id').val();
    var $textElement = $this.find('.chip-text');
    var currentStatus = $textElement.text().trim();

    if (!orderId) return;

    if (currentStatus === 'waiting' || currentStatus === 'بانتظار اجراء') {
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

  // Bulk Delete & Actions Visibility
  var table = $('.data-thumb-view').DataTable();

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