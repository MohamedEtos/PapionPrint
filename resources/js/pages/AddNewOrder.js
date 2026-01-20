$(document).ready(function () {
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
    $(this).closest('td').parent('tr').fadeOut();
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

    var customerId = $('#data-customer').val();
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

    if (!customerId || !machineId) {
      alert("Please select Customer and Machine.");
      return;
    }

    if (uploadedImagePaths.length === 0) {
      alert("Please upload at least one image.");
      return;
    }

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
        alert("Order Added Successfully!");

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
        $('#data-customer, #data-machine, #data-height, #data-width, #data-copies, #data-pic-copies, #data-pass, #data-meters, #data-price, #data-notes').val('');
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
        alert("Error adding order. Check console.");
      }
    });
  });
});