$(document).ready(function () {
    // DataTable init matching Strass config
    var table = $('.data-thumb-view').DataTable({
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
                text: "<i class='feather icon-plus'></i> إضافة طلب جديد",
                action: function () {
                    $('#addOrderModal').modal('show');
                },
                className: "btn-outline-primary"
            }
        ],
        initComplete: function (settings, json) {
            $(".dt-buttons .btn").removeClass("btn-secondary")
        }
    });

    var actionDropdown = $(".actions-dropodown")
    actionDropdown.insertBefore($(".top .actions .dt-buttons"))

    // Handle Selection and Sum
    table.on('select deselect', function () {
        var selectedRows = table.rows({ selected: true }).nodes();
        var totalCost = 0;
        var totalMeters = 0;
        var totalSections = 0;
        var totalPieces = 0;

        selectedRows.each(function (row) {
            let cost = parseFloat($(row).data('cost')) || 0;
            let height = parseFloat($(row).data('height')) || 0;
            let sectionCount = parseFloat($(row).data('section-count')) || 0;
            let requiredPieces = parseFloat($(row).data('required-pieces')) || 0;

            totalCost += cost;
            totalMeters += (height * sectionCount);
            totalSections += sectionCount;
            totalPieces += requiredPieces;
        });

        if (totalCost > 0) {
            let html = '<span class="badge badge-primary" style="font-size: 1em; margin-left:10px;"><i class="feather icon-dollar-sign"></i> الإجمالي: ' + totalCost.toFixed(2) + ' جنيه</span>';

            // Average piece price
            if (totalPieces > 0) {
                let avgPiecePrice = totalCost / totalPieces;
                html += '<span class="badge badge-warning" style="font-size: 1em; margin-left:10px;"><i class="feather icon-tag"></i> سعر القطعة: ' + avgPiecePrice.toFixed(2) + ' جنيه</span>';
            }

            // Average section price
            if (totalSections > 0) {
                let avgSectionCost = totalCost / totalSections;
                html += '<span class="badge badge-info" style="font-size: 1em; margin-left:10px;"><i class="feather icon-layers"></i> سعر المقطع: ' + avgSectionCost.toFixed(2) + ' جنيه</span>';
            }

            html += '<span class="badge badge-success" style="font-size: 1em; margin-left:10px;"><i class="feather icon-maximize-2"></i> إجمالي الأمتار: ' + (totalMeters / 100).toFixed(2) + ' م</span>';
            $('#selected-total').html(html);
            $('#laser-calculator-results').slideDown();
        } else {
            $('#laser-calculator-results').slideUp();
        }


    });

    // Bulk Delete
    $('#bulk-delete-btn').on('click', function () {
        var selectedIds = [];
        var selectedRows = table.rows({ selected: true }).nodes();

        $(selectedRows).each(function () {
            var id = $(this).data('id');
            if (id) selectedIds.push(id);
        });

        if (selectedIds.length === 0) {
            Swal.fire('تنبيه', 'يرجى تحديد طلبات للحذف', 'warning');
            return;
        }

        Swal.fire({
            title: 'حذف ' + selectedIds.length + ' طلب؟',
            text: 'سيتم حذف جميع الطلبات المحددة نهائياً',
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'نعم، احذف الكل',
            cancelButtonText: 'إلغاء'
        }).then((result) => {
            if (result.value) {
                $.ajax({
                    url: '/laser/bulk-delete',
                    type: 'POST',
                    data: {
                        _token: window.laserConfig.csrfToken,
                        ids: selectedIds
                    },
                    success: function () {
                        Swal.fire('تم الحذف!', 'تم حذف الطلبات المحددة بنجاح', 'success');
                        table.rows({ selected: true }).remove().draw();
                    },
                    error: function () {
                        Swal.fire('خطأ!', 'حدث خطأ أثناء الحذف', 'error');
                    }
                });
            }
        });
    });

    // Bulk Recalculate
    $('#bulk-recalc-btn').on('click', function () {
        var selectedIds = [];
        var selectedRows = table.rows({ selected: true }).nodes();

        $(selectedRows).each(function () {
            var id = $(this).data('id');
            if (id) selectedIds.push(id);
        });

        if (selectedIds.length === 0) {
            Swal.fire('تنبيه', 'يرجى تحديد طلبات للتحديث', 'warning');
            return;
        }

        Swal.fire({
            title: 'تحديث أسعار ' + selectedIds.length + ' طلب؟',
            text: 'سيتم إعادة حساب التكلفة بناءً على أسعار الخامات الحالية',
            type: 'question',
            showCancelButton: true,
            confirmButtonColor: '#7367F0',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'نعم، تحديث',
            cancelButtonText: 'إلغاء'
        }).then((result) => {
            if (result.value) {
                $.ajax({
                    url: '/laser/bulk-recalculate',
                    type: 'POST',
                    data: {
                        _token: window.laserConfig.csrfToken,
                        ids: selectedIds
                    },
                    success: function (response) {
                        Swal.fire('تم!', response.success, 'success');
                        setTimeout(function () { location.reload(); }, 1500);
                    },
                    error: function () {
                        Swal.fire('خطأ!', 'حدث خطأ أثناء التحديث', 'error');
                    }
                });
            }
        });
    });


    // Customer Autocomplete Logic
    $('#customer-name-input').on('input change', function () {
        var customerName = $(this).val();
        var option = $('#customers-list option[value="' + customerName + '"]');
        if (option.length > 0) {
            $('#customer-id-input').val(option.data('id'));
        } else {
            $('#customer-id-input').val(''); // New customer
        }
    });

    // Image Paste Logic
    $(document).on('paste', function (e) {
        var items = (e.clipboardData || e.originalEvent.clipboardData).items;
        for (var index in items) {
            var item = items[index];
            if (item.kind === 'file' && item.type.indexOf('image/') !== -1) {
                var blob = item.getAsFile();
                var file = new File([blob], "pasted-image.png", { type: blob.type });

                // Set to file input
                let container = new DataTransfer();
                container.items.add(file);
                $('#image-upload')[0].files = container.files;

                // Preview
                var reader = new FileReader();
                reader.onload = function (event) {
                    $('#pasted-image-preview img').attr('src', event.target.result);
                    $('#pasted-image-preview').show();
                };
                reader.readAsDataURL(blob);

                toastr.success("تم لصق الصورة بنجاح!", "نجاح");
            }
        }
    });

    // Handle normal file input change for preview
    $('#image-upload').on('change', function () {
        if (this.files && this.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#pasted-image-preview img').attr('src', e.target.result);
                $('#pasted-image-preview').show();
            }
            reader.readAsDataURL(this.files[0]);
        }
    });

    // Drag and Drop Logic
    var dropZone = $('#drop-zone');

    // Prevent default drag behaviors
    $(document).on('drag dragstart dragend dragover dragenter dragleave drop', function (e) {
        e.preventDefault();
        e.stopPropagation();
    });

    // Highlight drop zone when dragging over
    dropZone.on('dragover dragenter', function () {
        $(this).css({
            'background': '#e3f2fd',
            'border-color': '#7367F0'
        });
    });

    dropZone.on('dragleave dragend drop', function () {
        $(this).css({
            'background': '#f9f9f9',
            'border-color': '#ccc'
        });
    });

    // Handle dropped files
    dropZone.on('drop', function (e) {
        var files = e.originalEvent.dataTransfer.files;
        if (files.length > 0) {
            var file = files[0];
            if (file.type.indexOf('image/') !== -1) {
                // Set to file input
                let container = new DataTransfer();
                container.items.add(file);
                $('#image-upload')[0].files = container.files;

                // Preview
                var reader = new FileReader();
                reader.onload = function (event) {
                    $('#pasted-image-preview img').attr('src', event.target.result);
                    $('#pasted-image-preview').show();
                };
                reader.readAsDataURL(file);

                toastr.success("تم إضافة الصورة بنجاح!", "نجاح");
            } else {
                toastr.error("يرجى اختيار صورة فقط", "خطأ");
            }
        }
    });

    // Click on drop zone to open file picker
    dropZone.on('click', function (e) {
        if (e.target.tagName !== 'INPUT') {
            $('#image-upload').click();
        }
    });


    // Global Vars from Backend
    var operatingCostPerPiece = window.laserConfig.costs.operating;
    var ceylonCost = window.laserConfig.costs.ceylon;

    // Auto-calculate section count with animation
    $(document).on('input', '#required-pieces-input, #pps-input', function () {
        var required = parseFloat($('#required-pieces-input').val());
        var perSection = parseFloat($('#pps-input').val());

        if (required > 0 && perSection > 0) {
            var sections = Math.ceil(required / perSection);
            var $input = $('#sc-input');

            if ($input.val() != sections) {
                $input.val(sections);
                $input.removeClass('flash-input');
                void $input.get(0).offsetWidth;
                $input.addClass('flash-input');
                setTimeout(function () { $input.removeClass('flash-input'); }, 1000);
            }
        }
        calculateCost();
    });

    // Calculate Function
    function calculateCost() {
        var height = parseFloat($('#height-input').val()) || 0;
        var source = $('#source-select').val();
        var materialPrice = parseFloat($('#material-select').find(':selected').data('price')) || 0;
        var hasCeylon = $('#ceylonSwitch').is(':checked');
        var piecesPerSection = parseInt($('#pps-input').val()) || 1;
        var requiredPieces = parseInt($('#required-pieces-input').val()) || 0;

        // Custom Operating Cost logic
        var customOpCost = $('#custom-operating-cost-input').val();
        var opCost = (customOpCost !== '' && customOpCost !== undefined) ? parseFloat(customOpCost) : operatingCostPerPiece;

        if (piecesPerSection < 1) piecesPerSection = 1;
        var sectionCount = Math.ceil(requiredPieces / piecesPerSection);

        var lengthMeters = height / 100;

        // Calculate material cost per piece
        var materialCostPerPiece = 0;
        var sectionMaterialCost = 0;
        if (source === 'ap_group') {
            sectionMaterialCost = lengthMeters * materialPrice;
            if (hasCeylon) {
                sectionMaterialCost += (lengthMeters * ceylonCost);
            }
            materialCostPerPiece = sectionMaterialCost / piecesPerSection;
        }

        // Operating cost is flat per piece
        var pieceCost = materialCostPerPiece + opCost;

        // Section cost = Material + (Operating × pieces)
        var sectionTotalCost = sectionMaterialCost + (opCost * piecesPerSection);
        var totalCost = sectionTotalCost * sectionCount;

        $('#piece-cost').text(pieceCost.toFixed(2));
        $('#section-cost').text(sectionTotalCost.toFixed(2));
        $('#section-count-display').text(sectionCount);
        $('#approx-cost').text(totalCost.toFixed(2));
    }

    $('.calc-input, #source-select, #material-select, #ceylonSwitch').on('change keyup', calculateCost);

    // Save Order
    $('#saveOrderBtn').on('click', function () {
        var formData = new FormData($('#laserOrderForm')[0]);

        $.ajax({
            url: window.laserConfig.routes.store,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                $('#addOrderModal').modal('hide');
                toastr.success('تمت الإضافة بنجاح');
                location.reload();
            },
            error: function (errors) {
                toastr.error('يرجى التأكد من البيانات');
            }
        });
    });

    // Delete
    window.deleteLaserOrder = function (id) {
        Swal.fire({
            title: 'هل أنت متأكد؟',
            text: 'سيتم حذف هذا الطلب نهائياً',
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'نعم، احذف',
            cancelButtonText: 'إلغاء'
        }).then((result) => {
            if (result.value) {
                $.ajax({
                    url: window.laserConfig.routes.delete + id,
                    type: 'DELETE',
                    data: { _token: window.laserConfig.csrfToken },
                    success: function () {
                        Swal.fire('تم الحذف!', 'تم حذف الطلب بنجاح', 'success');
                        table.row($('tr[data-id="' + id + '"]')).remove().draw();
                    },
                    error: function () {
                        Swal.fire('خطأ!', 'حدث خطأ أثناء الحذف', 'error');
                    }
                });
            }
        });
    }

    // Edit
    window.editLaserOrder = function (id) {
        $.get(window.laserConfig.routes.show + id, function (data) {
            // Populate form fields
            $('#customer-name-input').val(data.customer ? data.customer.name : '');
            $('#customer-id-input').val(data.customer_id || '');
            $('#source-select').val(data.source);
            $('#material-select').val(data.material_id || '');
            $('#ceylonSwitch').prop('checked', data.add_ceylon);
            $('#height-input').val(data.height);
            $('#width-input').val(data.width);
            $('#required-pieces-input').val(data.required_pieces);
            $('#pps-input').val(data.pieces_per_section);
            $('#sc-input').val(data.section_count);
            $('#custom-operating-cost-input').val(data.custom_operating_cost); // Populate new field
            $('#laserOrderForm textarea[name="notes"]').val(data.notes);

            // Change form to update mode
            $('#addOrderModal .modal-title').text('تعديل الطلب');
            $('#saveOrderBtn').text('تحديث').off('click').on('click', function () {
                var formData = new FormData($('#laserOrderForm')[0]);

                $.ajax({
                    url: window.laserConfig.routes.update + id,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': window.laserConfig.csrfToken,
                        'X-HTTP-Method-Override': 'PUT'
                    },
                    success: function (response) {
                        $('#addOrderModal').modal('hide');
                        toastr.success('تم التحديث بنجاح');
                        setTimeout(function () { location.reload(); }, 1000);
                    },
                    error: function (errors) {
                        toastr.error('يرجى التأكد من البيانات');
                    }
                });
            });

            // Show modal
            $('#addOrderModal').modal('show');
            calculateCost();
        }).fail(function () {
            toastr.error('حدث خطأ أثناء تحميل البيانات');
        });
    }

    // Restart
    window.restartLaserOrder = function (id) {
        Swal.fire({
            title: 'إعادة تشغيل الطلب؟',
            text: 'سيتم إنشاء نسخة جديدة من هذا الطلب',
            type: 'question',
            showCancelButton: true,
            confirmButtonColor: '#7367F0',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'نعم، أعد التشغيل',
            cancelButtonText: 'إلغاء'
        }).then((result) => {
            if (result.value) {
                $.ajax({
                    url: window.laserConfig.routes.restart + id,
                    type: 'POST',
                    data: { _token: window.laserConfig.csrfToken },
                    success: function () {
                        Swal.fire('تم!', 'تم إعادة تشغيل الطلب بنجاح', 'success');
                        setTimeout(function () { location.reload(); }, 1500);
                    },
                    error: function () {
                        Swal.fire('خطأ!', 'حدث خطأ أثناء إعادة التشغيل', 'error');
                    }
                });
            }
        });
    }

    // Reset form when modal closes
    $('#addOrderModal').on('hidden.bs.modal', function () {
        $('#laserOrderForm')[0].reset();
        $('#customer-id-input').val('');
        $('#custom-operating-cost-input').val(''); // Reset
        $('#addOrderModal .modal-title').text('طلب ليزر جديد');
        // Reset save button state
        $('#saveOrderBtn').text('حفظ');
        $('#saveOrderBtn').off('click').on('click', saveOrderHandler);
    });

    // Manual Close Handlers
    $('.modal .close, .modal .btn-secondary').on('click', function () {
        $('#addOrderModal').modal('hide');
    });

    // Define Save Order Handler separately to avoid stacking
    function saveOrderHandler() {
        var formData = new FormData($('#laserOrderForm')[0]);

        $.ajax({
            url: window.laserConfig.routes.store,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                $('#addOrderModal').modal('hide');
                toastr.success('تمت الإضافة بنجاح');
                setTimeout(function () { location.reload(); }, 1000);
            },
            error: function (errors) {
                toastr.error('يرجى التأكد من البيانات');
            }
        });
    }

    // Initial bind
    $('#saveOrderBtn').on('click', saveOrderHandler);

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