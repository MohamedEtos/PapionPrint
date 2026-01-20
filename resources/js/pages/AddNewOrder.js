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
  $('.action-edit').on("click", function (e) {
    e.stopPropagation();
    // Logic for edit can be added here
    $(".add-new-data").addClass("show");
    $(".overlay-bg").addClass("show");
  });

  // On Delete
  $('.action-delete').on("click", function (e) {
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
              type: "success",
              title: 'تم الحذف!',
              text: 'تم حذف الطلب بنجاح!',
              confirmButtonClass: 'btn btn-success',
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
  $('.action-info').on("click", function (e) {
    e.stopPropagation();
    $('#xlarge').modal('show');
  });

  // On Cancel / Close Sidebar
  $('.hide-data-sidebar, .cancel-data-btn, .overlay-bg').on("click", function (e) {
    e.stopPropagation();
    $(".add-new-data").removeClass("show");
    $(".overlay-bg").removeClass("show");
  });

  // Save Data Button Logic
  $('#saveDataBtn').on('click', function (e) {
    e.preventDefault();
    console.log("Add Data Button Clicked");

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



    // if (uploadedImagePaths.length === 0) {
    //   // toastr.error("الرجاء رفع صورة واحدة على الأقل", "خطا");
    //   return;
    // }

    $.ajax({
      url: "/printers/store",
      type: "POST",
      data: {
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
      },
      success: function (response) {
        console.log("Order created:", response);
        toastr.success("Order Added Successfully!", "تمت العملية بنجاح");

        var order = response.order;
        // Construct Image Path
        var imgPath = (order.orders_imgs && order.orders_imgs.length > 0)
          ? '/storage/' + order.orders_imgs[0].path
          : '/core/images/elements/apple-watch.png';

        // Construct Helper Variables
        var customerName = order.customers ? order.customers.name : 'Unknown';
        var machineName = order.machines ? order.machines.name : 'Unknown';
        var pricePerMeter = order.printingprices ? order.printingprices.pricePerMeter : '';

        // Build Table Row HTML
        var newRow = `
            <tr>
                <td></td>
                <td class="product-img"><img src="${imgPath}" alt="Img placeholder"></td>
                <td class="product-name">${customerName}</td>
                <td class="product-category">${machineName} ${order.pass} pass</td>
                <td class="product-category"><b>${order.meters}</b></td>
                <td>
                    <div class="chip chip-sucendry">
                        <div class="chip-body">
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

        // Append to Table
        $('table.data-thumb-view tbody').append(newRow);

        // Close Sidebar
        $(".add-new-data").removeClass("show");
        $(".overlay-bg").removeClass("show");

        // Reset Inputs
        $('#data-customer, #data-customer-view, #data-machine, #data-height, #data-width, #data-copies, #data-pic-copies, #data-pass, #data-meters, #data-price, #data-notes').val('');
        $('#data-status').val('Pending');
        $('#data-pass').val('1'); // Reset pass default

        // Reset Dropzone
        uploadedImagePaths = [];
        if (myDropzone) {
          myDropzone.removeAllFiles();
        }

      },
      error: function (xhr) {
        console.error("Error creating order:", xhr);
        if (xhr.status === 422) {
          var errors = xhr.responseJSON.errors;
          $.each(errors, function (key, val) {
            toastr.error(val[0], "خطا");
          });
        } else {
          toastr.error("Error creating order. Please try again.", "خطا");
        }
      }
    });
  });
});