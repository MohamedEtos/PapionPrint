$(document).ready(function () {

  // culculate meters 

  // Calculate & Update Prices
  $('#data-copies, #data-height, #data-price, #data-pic-copies, #data-machine, #data-pass').on('input change', function () {
    var copies = parseFloat($('#data-copies').val()) || 0;
    var height = parseFloat($('#data-height').val()) || 0;

    // Get Dynamic Price Per Meter based on Machine & Pass
    var machineId = $('#data-machine').val();
    var pass = $('#data-pass').val();
    var pricePerMeter = parseFloat($('#data-price').val()) || 0; // Default to current input



    // Separate Handler for updating Price from Machine/Pass selection
      console.log('Machine/Pass changed');
      var machineId = $('#data-machine').val();
      var pass = $('#data-pass').val();
      console.log('Machine ID:', machineId, 'Pass:', pass);

      if (machineId && window.papionInvData && window.papionInvData.machines) {
        var machine = window.papionInvData.machines.find(m => m.id == machineId);
        console.log('Found Machine:', machine);
        if (machine) {
          var price = 0;
          if (pass == 4) {
            price = parseFloat(machine.price_4_pass);
          } else if (pass == 6) {
            price = parseFloat(machine.price_6_pass);
          } else {
            price = parseFloat(machine.price_1_pass);
          }
          console.log('New Price:', price);

          if (price > 0) {
            $('#data-price').val(price).trigger('change'); // Trigger change to update totals
          }
        }
      } else {
        console.warn('Machine Data or ID missing', window.papionInvData);
      }


    // Calculate Meters
    var meters = copies * height;
    $('#data-meters').val((meters / 100));

    // Calculate Total Price
    var total = meters * price;
    $('#data-total').val(total.toFixed(2));


    // Calculate Picture Price
    var picCopies = parseFloat($('#data-pic-copies').val()) || 0;

    var totalpic = copies * picCopies;

    // Price Per Piece Formula: (Height / 100) / PicCopies * PricePerMeter
    var pricePerPiece = 0;
    if (picCopies > 0) {
      pricePerPiece = (height / 100) / picCopies * price;
    }

    $('#data-price-pic').text(pricePerPiece.toFixed(2));
    $('#data-total-pic').text(totalpic);

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
    $('#data-customer, #data-customer-view, #data-machine, #data-height, #data-width, #data-copies, #data-pic-copies, #data-pass, #data-meters, #data-price, #data-notes, #data-fabric-type').val('');
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
      acceptedFiles: '.jpg,.jpeg,.png,.gif,.tiff,.tif,.webp',
      addRemoveLinks: true,
      resizeHeight: 110,
      resizeMimeType: 'image/webp',
      resizeQuality: 0.9,
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      success: function (file, response) {
        file.serverFileName = response.path; // Store server path on file object
        uploadedImagePaths.push(response.path);
        console.log("Images uploaded:", uploadedImagePaths);
        toastr.success("Image uploaded successfully");
      },
      removedfile: function (file) {
        if (file.previewElement != null && file.previewElement.parentNode != null) {
          file.previewElement.parentNode.removeChild(file.previewElement);
        }

        // Remove from uploadedImagePaths array
        var path = file.serverFileName;
        if (path) {
          var index = uploadedImagePaths.indexOf(path);
          if (index !== -1) {
            uploadedImagePaths.splice(index, 1);
            console.log("Image removed. Remaining:", uploadedImagePaths);
          }
        }

        return _updateMaxFilesReachedClass();
      },
      error: function (file, response) {
        toastr.error("Upload failed:", response.message);
      }
    });
  } catch (e) {
    toastr.error("Dropzone init warning:", e);
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

        // Populate Images in Dropzone
        uploadedImagePaths = []; // Clear current
        if (typeof myDropzone !== 'undefined' && myDropzone) {
          myDropzone.removeAllFiles(true); // true to avoid triggering removedfile events that might mess up logic if not careful, but we reset array anyway

          if (order.orders_imgs && order.orders_imgs.length > 0) {
            order.orders_imgs.forEach(function (img) {
              var mockFile = { name: "Image", size: 12345, serverFileName: img.path };
              myDropzone.emit("addedfile", mockFile);
              myDropzone.emit("thumbnail", mockFile, "/storage/" + img.path);
              myDropzone.emit("complete", mockFile);
              myDropzone.files.push(mockFile); // Add to files array for Dropzone to track count
              uploadedImagePaths.push(img.path);
            });
          }
        }

        // calc pic 
        var copies = parseFloat($('#data-copies').val()) || 0;
        var picCopies = parseFloat($('#data-pic-copies').val()) || 0;
        var totalpic = copies * picCopies;
        $('#data-total-pic').text(totalpic);

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

  //////////////////////// On Delete ////////////////////////////
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

  ///////////////////////////// on action-info /////////////////////////////////
  // $(document).on("click", ".action-info", function (e) {
  //   e.stopPropagation();
  //   var $row = $(this).closest('tr');
  //   var orderId = $row.find('.order_id').val();

  //   if (!orderId) return;

  //   // Show loading state or clear previous measurement
  //   $('#modal-order-id').text('Loading...');

  //   $.ajax({
  //     url: "/printers/" + orderId,
  //     type: "GET",
  //     success: function (order) {
  //       // Basic Info
  //       $('#modal-order-id').text(order.id);
  //       $('#modal-order-number').text(order.orderNumber || '-');
  //       $('#modal-customer-name').text(order.customers ? order.customers.name : '-');
  //       $('#modal-machine-name').text(order.machines ? order.machines.name : '-');

  //       // File Details
  //       $('#modal-file-height').text(order.fileHeight);
  //       $('#modal-file-width').text(order.fileWidth);
  //       $('#modal-file-copies').text(order.fileCopies);
  //       $('#modal-pic-copies').text(order.picInCopies);
  //       $('#modal-meters').text(order.meters);

  //       // Prices
  //       if (order.printingprices) {
  //         $('#modal-price-per-meter').text(order.printingprices.pricePerMeter);
  //         $('#modal-total-price').text(order.printingprices.totalPrice);
  //       } else {
  //         $('#modal-price-per-meter').text('-');
  //         $('#modal-total-price').text('-');
  //       }

  //       // Statuses
  //       $('#modal-status').text(order.status);
  //       $('#modal-payment-status').text(order.paymentStatus);
  //       $('#modal-archive').text(order.archive ? 'Yes' : 'No');
  //       $('#modal-notes').text(order.notes || 'No notes');

  //       // Users
  //       $('#modal-designer').text(order.user ? order.user.name : '-');
  //       $('#modal-operator').text(order.user2 ? order.user2.name : '-');

  //       // Dates
  //       $('#modal-start-date').text(order.created_at ? new Date(order.created_at).toLocaleString() : '-');
  //       $('#modal-end-date').text(order.updated_at ? new Date(order.updated_at).toLocaleString() : '-');
  //       $('#modal-time-end-op').text(order.timeEndOpration ? new Date(order.timeEndOpration).toLocaleString() : '-');

  //       // Image
  //       if (order.orders_imgs && order.orders_imgs.length > 0) {
  //         $('#modal-order-image').attr('src', '/storage/' + order.orders_imgs[0].path);
  //       } else {
  //         $('#modal-order-image').attr('src', '/core/images/elements/apple-watch.png');
  //       }

  //       $('#xlarge').modal('show');
  //     },
  //     error: function (xhr) {
  //       console.error("Error modal details:", xhr);
  //       toastr.error("Could not fetch details", "Error");
  //     }
  //   });
  // });

  // On Cancel / Close Sidebar
  $('.hide-data-sidebar, .cancel-data-btn, .overlay-bg').on("click", function (e) {
    e.stopPropagation();
    $(".add-new-data").removeClass("show");
    $(".overlay-bg").removeClass("show");
    resetForm();
  });



  // //////////////////////////Save Data Button Logic ////////////////////////////////
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
    var fabric_type = $('#data-fabric-type').val();
    var price = $('#data-price').val();
    var fabricType = $('#data-fabric-type').val();
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
      fabric_type: fabric_type,
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
        try {
          console.log("Order saved/updated:", response);
          var message = editingOrderId ? "Order Updated Successfully!" : "Order Added Successfully!";
          toastr.success(message, "تمت العملية بنجاح");

          if (!response || !response.order) {
            console.warn("Response missing 'order' object:", response);
            return;
          }

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
            if ($row.length) {
              $row.find('.product-img img').attr('src', imgPath);
              $row.find('.product-name').text(customerName);

              // Fix: Target Machine column (index 3) explicitly
              var $machineCell = $row.find('td').eq(3);
              $machineCell.text(machineName + ' ' + order.pass + ' pass');

              // Fix: Target Meters column (index 4) explicitly and preserve formatting
              var $metersCell = $row.find('td').eq(4);
              $metersCell.html('<b>' + order.meters + '</b>');

              $row.find('.chip-text').text(order.status);
              $row.find('.chip').removeClass('chip-success chip-warning').addClass(order.status == 'انتهت الطباعة' ? 'chip-success' : 'chip-info');
              $row.find('td:eq(6)').text(pricePerMeter);
            }
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
                                <div class="chip chip-${order.status == 'انتهت الطباعة' ? 'success' : 'info'}">
                                    <div class="chip-body status-toggle" style="cursor: pointer">
                                        <div class="chip-text">${order.status}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="product-price" title="Just now">الآن</td>
                            <td class="product-action">
                                <span class=" hover_action action-edit "><i class="feather icon-edit"></i></span>
                                <span class=" hover_action action-delete text-danger " ><i class="feather icon-trash"></i></span>
                            </td>
                        </tr>
                    `;
            // <span class=" hover_action action-info " data-toggle="modal" data-target="#xlarge"><i class="feather icon-file"></i></span>

            $('table.data-thumb-view tbody').append(newRow);
          }
        } catch (err) {
          console.error("Error updating UI:", err);
          toastr.warning("Order saved, but UI update encountered an issue. Please refresh.", "Warning");
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
          $('#data-fabric-type').val(order.fabric_type);
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




  // Check if global data exists
  if (!window.papionInvData) {
    console.warn('PapionInvData not found');
  } else {

    // Initialize Ink Chart
    const inkChartEl = document.getElementById('inkChart');
    if (inkChartEl && typeof Chart !== 'undefined') {
      const ctxInk = inkChartEl.getContext('2d');
      const inkStocks = window.papionInvData.inkStocks || [];

      // Initialize Paper Chart
      const ctxPaper = document.getElementById('paperChart').getContext('2d');
      const paperStocks = window.papionInvData.paperStocks || [];

      function getQty(stocks, type, color = null) {
        const stock = stocks.find(s => s.machine_type === type && (color ? s.color === color : true));
        return stock ? stock.quantity : 0;
      }

      function createGradient(ctx, color) {
        const gradient = ctx.createLinearGradient(100, 75, 100, 300);
        gradient.addColorStop(0, color); // Solid color at top
        gradient.addColorStop(1, color + '80'); // Fade to transparent/lighter at bottom (approx 40% opacity)
        return gradient;
      }

      // Softer Colors
      const colors = {
        cyan: '#00CFE8',
        magenta: '#EA5455',
        yellow: '#FF9F43',
        black: '#4B4B4B',
        white: '#E5E7EB',
        green: '#28C76F',
        orange: '#FF9F43'
      };

      // --- Ink Chart ---
      const inkData = {
        labels: ['Sub-C', 'Sub-M', 'Sub-Y', 'Sub-K', 'DTF-C', 'DTF-M', 'DTF-Y', 'DTF-K', 'DTF-W'],
        datasets: [{
          label: 'Ink (L)',
          data: [
            getQty(inkStocks, 'sublimation', 'Cyan'), getQty(inkStocks, 'sublimation', 'Magenta'), getQty(inkStocks, 'sublimation', 'Yellow'), getQty(inkStocks, 'sublimation', 'Black'),
            getQty(inkStocks, 'dtf', 'Cyan'), getQty(inkStocks, 'dtf', 'Magenta'), getQty(inkStocks, 'dtf', 'Yellow'), getQty(inkStocks, 'dtf', 'Black'), getQty(inkStocks, 'dtf', 'White')
          ],
          backgroundColor: [
            createGradient(ctxInk, colors.cyan),
            createGradient(ctxInk, colors.magenta),
            createGradient(ctxInk, colors.yellow),
            createGradient(ctxInk, colors.black),
            createGradient(ctxInk, colors.cyan),
            createGradient(ctxInk, colors.magenta),
            createGradient(ctxInk, colors.yellow),
            createGradient(ctxInk, colors.black),
            colors.white // White usually doesn't need much gradient if it's bg color
          ],
          borderColor: 'transparent',
          borderWidth: 0,
          borderRadius: 4,
          elements: {
            bar: {
              borderRadius: 4
            }
          }
        }]
      };

      const inkChart = new Chart(ctxInk, {
        type: 'bar',
        data: inkData,
        options: {
          responsive: true,
          maintainAspectRatio: false,
          scales: {
            y: {
              beginAtZero: true,
              grid: {
                color: '#e7e7e7',
                drawBorder: false
              },
              ticks: { stepSize: 1, color: '#b9c3cd' }
            },
            x: {
              grid: { display: false },
              ticks: { color: '#b9c3cd' }
            }
          },
          plugins: {
            legend: { display: false }
          },
          animation: {
            duration: 2000,
            easing: 'easeInOutQuart'
          }
        }
      });

      // --- Paper Chart ---
      const paperData = {
        labels: ['Sublimation', 'DTF'],
        datasets: [{
          label: 'Paper (Meters)',
          data: [
            getQty(paperStocks, 'sublimation'),
            getQty(paperStocks, 'dtf')
          ],

          backgroundColor: [
            createGradient(ctxPaper, colors.green),
            createGradient(ctxPaper, colors.orange)
          ],
          borderColor: 'transparent',
          borderWidth: 0,
          borderRadius: 4,
          barThickness: 50,
        }]
      };

      const paperChart = new Chart(ctxPaper, {
        type: 'bar', // or 'doughnut'
        data: paperData,
        options: {
          responsive: true,
          maintainAspectRatio: false,
          scales: {
            y: {
              beginAtZero: true,
              grid: {
                color: '#e7e7e7',
                drawBorder: false
              },
              ticks: { color: '#b9c3cd' }
            },
            x: {
              grid: { display: false },
              ticks: { color: '#b9c3cd' }
            }
          },
          plugins: {
            legend: { display: false }
          },
          animation: {
            duration: 2000,
            easing: 'easeInOutQuart'
          }
        }
      });

      // Handle Click for Consumption
      // Note: This needs to be available even if charts fail, but we put it here assuming Charts is key feature
      // Better to move event listener out or keep it here if related.
      // We bind it effectively here.

      $(document).on('click', '.consume-ink-btn', function () {
        let type = $(this).data('type');
        let color = $(this).data('color');

        Swal.fire({
          title: 'تأكيد الاستهلاك',
          text: `هل أنت متأكد من خصم 1 لتر من ${color} (${type})؟`,
          type: 'warning',
          showCancelButton: true,
          confirmButtonText: 'نعم، اخصم',
          cancelButtonText: 'إلغاء'
        }).then((result) => {
          if (result.value) {
            $.ajax({
              url: window.papionInvData.consumeInkRoute,
              method: 'POST',
              data: {
                _token: window.papionInvData.csrfToken,
                machine_type: type,
                color: color
              },
              success: function (response) {
                Swal.fire('تم!', response.success, 'success');

                // Update Chart Locally
                // Map Color to Index: Cyan=0, Magenta=1, Yellow=2, Black=3, White=4
                const colorMap = { 'Cyan': 0, 'Magenta': 1, 'Yellow': 2, 'Black': 3, 'White': 4 };
                let colorIndex = colorMap[color];

                // Since we only have ONE dataset (index 0) with all data:
                // Sublimation indices: 0, 1, 2, 3
                // DTF indices: 4, 5, 6, 7, 8

                let dataIndex;
                if (type === 'sublimation') {
                  dataIndex = colorIndex; // 0..3
                } else {
                  // DTF starts after Sublimation (4 colors)
                  // careful: DTF colors map matches Sublimation order for CMYK, but we need to verify order in chart labels
                  // Chart Labels: 'Sub-C', 'Sub-M', 'Sub-Y', 'Sub-K', 'DTF-C', 'DTF-M', 'DTF-Y', 'DTF-K', 'DTF-W'
                  // Color Map above: C=0, M=1, Y=2, K=3

                  if (color === 'White') {
                    dataIndex = 8;
                  } else {
                    dataIndex = 4 + colorIndex;
                  }
                }

                if (dataIndex !== undefined && inkChart.data.datasets[0]) {
                  inkChart.data.datasets[0].data[dataIndex] = response.new_quantity;
                  inkChart.update();
                }

              },
              error: function (xhr) {
                let msg = xhr.responseJSON ? xhr.responseJSON.error : 'حدث خطأ أثناء الخصم';
                Swal.fire('خطأ!', msg, 'error');
              }
            });
          }
        });
      });
    } else {
      console.log("Chart elements not found or Chart.js not loaded");
    }
  }
});