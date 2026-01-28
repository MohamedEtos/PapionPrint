
$(document).ready(function () {
    // --- Wizard Initialization ---
    var form = $(".steps-validation").show();

    // --- Calculation Helper ---
    $(document).on('input', '#data-required-pieces, #data-pieces-per-card', function () {
        var required = parseFloat($('#data-required-pieces').val());
        var perCard = parseFloat($('#data-pieces-per-card').val());

        if (required > 0 && perCard > 0) {
            var cards = Math.ceil(required / perCard);
            var $input = $('#data-cards-count');

            // Only animate if value changed
            if ($input.val() != cards) {
                $input.val(cards);
                $input.removeClass('flash-input'); // Reset to allow re-triggering
                void $input.closest('.form-control').get(0).offsetWidth; // Trigger reflow
                $input.addClass('flash-input');

                // Remove class after animation to clean up
                setTimeout(function () {
                    $input.removeClass('flash-input');
                }, 1000);
            }
        }
    });

    $(".steps-validation").steps({
        headerTag: "h6",
        bodyTag: "fieldset",
        transitionEffect: "fade",
        titleTemplate: '<span class="step">#index#</span> #title#',
        labels: {
            finish: 'Submit'
        },
        onStepChanging: function (event, currentIndex, newIndex) {
            if (currentIndex > newIndex) {
                return true;
            }
            if (currentIndex < newIndex) {
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

    $(".steps-validation").validate({
        ignore: 'input[type=hidden]',
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
        }
    });

    // --- DataTable Initialization ---
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
                text: "<i class='feather icon-plus'></i> إضافة طلب جديد",
                action: function (e, dt, node, config) {
                    $('#validation').slideToggle(function () {
                        var isVisible = $(this).is(':visible');
                        dt.button(node).text(isVisible ? '<i class="feather icon-x"></i> اغـــلاق' : "<i class='feather icon-plus'></i> إضافة طلب جديد");
                    });
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

    if ($(".data-items").length > 0) {
        new PerfectScrollbar(".data-items", { wheelPropagation: false })
    }

    $(".hide-data-sidebar, .cancel-data-btn, .overlay-bg").on("click", function () {
        $(".add-new-data").removeClass("show")
        $(".overlay-bg").removeClass("show")
        $("#data-name, #data-price").val("")
        $("#data-category, #data-status").prop("selectedIndex", 0)
    })

    // Global variable to track edit state
    var editingOrderId = null;
    var linkedOrderId = null;

    // --- Dynamic Layers Logic ---
    let layerIndex = 1;

    $('#add-layer-btn').on('click', function () {
        var firstRow = $('#layers-container .layer-row').first();
        var newRow = firstRow.clone();

        // Update names to have unique index
        newRow.find('.layer-size').attr('name', 'layers[' + layerIndex + '][size]').val('');
        newRow.find('.layer-count').attr('name', 'layers[' + layerIndex + '][count]').val('');

        $('#layers-container').append(newRow);
        layerIndex++;
    });

    $(document).on('click', '.remove-layer-btn', function () {
        if ($('#layers-container .layer-row').length > 1) {
            $(this).closest('.layer-row').remove();
        } else {
            // If only one row, just clear it
            $(this).closest('.layer-row').find('input, select').val('');
        }
    });

    // --- Image Paste Logic ---
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
                $('#data-image-upload')[0].files = container.files;

                // Preview
                var reader = new FileReader();
                reader.onload = function (event) {
                    $('#pasted-image-preview img').attr('src', event.target.result);
                    $('#pasted-image-preview').show();
                };
                reader.readAsDataURL(blob);

                toastr.success("Image pasted successfully!", "Success");
            }
        }
    });

    // Handle normal file input change for preview
    $('#data-image-upload').on('change', function () {
        if (this.files && this.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#pasted-image-preview img').attr('src', e.target.result);
                $('#pasted-image-preview').show();
            }
            reader.readAsDataURL(this.files[0]);
        }
    });

    function submitWizardData() {
        var formData = new FormData();

        var customerId = $('#data-customer').val();
        // If user typed name but didn't select from datalist, try to find ID
        var customerName = $('#data-customer-view').val();
        if (!customerId) {
            var opt = $('#customers-list option[value="' + customerName + '"]');
            if (opt.length > 0) customerId = opt.attr('data-id');
        }

        formData.append('customerId', customerId || '');
        formData.append('height', $('#data-height').val());
        formData.append('width', $('#data-width').val());
        formData.append('cards_count', $('#data-cards-count').val());
        formData.append('pieces_per_card', $('#data-pieces-per-card').val());
        formData.append('machine_time', $('#data-machine-time').val()); // Added Machine Time
        formData.append('notes', $('#data-notes').val());

        // Layers
        $('#layers-container .layer-row').each(function (index, element) {
            var size = $(element).find('.layer-size').val();
            var count = $(element).find('.layer-count').val();
            if (size && count) {
                formData.append('layers[' + index + '][size]', size);
                formData.append('layers[' + index + '][count]', count);
            }
        });

        if (linkedOrderId) {
            formData.append('orderId', linkedOrderId);
        }
        formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

        var imageFile = $('#data-image-upload')[0].files[0];
        if (imageFile) {
            formData.append('image', imageFile);
        }

        var url = "/tarter/store";
        var type = "POST";

        if (editingOrderId) {
            url = "/tarter/update/" + editingOrderId;
            formData.append('_method', 'PUT');
        }

        $.ajax({
            url: url,
            type: type,
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                console.log("Order saved:", response);
                Swal.fire({
                    type: 'success',
                    title: 'تم التسجيل بنجاح!',
                    showConfirmButton: false,
                    timer: 1500,
                    buttonsStyling: false,
                    confirmButtonClass: 'btn btn-primary',
                });
                $('#validation').slideUp();
                setTimeout(function () { location.reload(); }, 1000);
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

    // --- Actions ---

    window.editTarter = function (id) {
        editingOrderId = id;

        // Fetch data
        $.get('/tarter/show/' + id, function (data) {
            $('.new-data-title h4').text('تعديل طلب');
            $('#saveDataBtn').text('Update');

            // Populate Fields
            $('#data-customer').val(data.customer_id); // Changed to customer_id to match model
            if (data.customer) $('#data-customer-view').val(data.customer.name);

            $('#data-height').val(data.height);
            $('#data-width').val(data.width);
            $('#data-cards-count').val(data.cards_count);
            $('#data-pieces-per-card').val(data.pieces_per_card);
            $('#data-machine-time').val(data.machine_time); // Populate Machine Time
            $('#data-notes').val(data.notes);

            // Required Pieces Helper
            if (data.cards_count && data.pieces_per_card) {
                $('#data-required-pieces').val(data.cards_count * data.pieces_per_card);
            }

            // Populate Layers
            $('#layers-container').empty();
            if (data.layers && data.layers.length > 0) {
                data.layers.forEach(function (layer, index) {
                    var rowHtml = `<div class="layer-row row mb-1">
                                        <div class="col-md-5">
                                            <div class="form-group">
                                                <label>مقاس الإبرة</label>
                                                <select class="form-control layer-size" name="layers[${index}][size]">
                                                     // Options will be set below
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-5">
                                            <div class="form-group">
                                                <label>العدد</label>
                                                <input type="number" class="form-control layer-count" name="layers[${index}][count]" placeholder="عدد الحبات" value="${layer.count}">
                                            </div>
                                        </div>
                                        <div class="col-md-2 d-flex align-items-center">
                                            <button type="button" class="btn btn-danger btn-icon remove-layer-btn"><i class="feather icon-trash"></i></button>
                                        </div>
                                    </div>`;

                    var $row = $(rowHtml);

                    // Clone options from existing select logic check
                    var options = '';
                    var $existingSelect = $('.layer-size').first();
                    if ($existingSelect.length > 0) {
                        options = $existingSelect.html();
                    } else {
                        // Basic fallback if no select found (rare)
                        options = '<option value="9">9</option><option value="11">11</option><option value="14">14</option>';
                    }

                    $row.find('.layer-size').html(options).val(layer.size);
                    $('#layers-container').append($row);
                });
                layerIndex = data.layers.length;
            } else {
                $('#add-layer-btn').trigger('click');
            }

            // Show Form
            $('#validation').slideDown();
            $('html, body').animate({
                scrollTop: $("#validation").offset().top
            }, 500);
        });
    }

    window.deleteTarter = function (id) {
        Swal.fire({
            title: 'هل انت متأكد؟',
            text: "لن تتمكن من استرجاع هذا الطلب!",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'نعم، احذفه!',
            cancelButtonText: 'الغاء'
        }).then((result) => {
            if (result.value) {
                $.ajax({
                    url: '/tarter/delete/' + id,
                    type: 'DELETE',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        Swal.fire(
                            'تم الحذف!',
                            'تم حذف الطلب بنجاح.',
                            'success'
                        ).then(() => {
                            location.reload();
                        });
                    },
                    error: function (xhr) {
                        Swal.fire(
                            'خطأ!',
                            'حدث خطأ اثناء الحذف.',
                            'error'
                        );
                    }
                });
            }
        });
    }

    window.restartTarter = function (id) {
        Swal.fire({
            title: 'إعادة تشغيل؟',
            text: "سيتم إنشاء نسخة جديدة من هذا الطلب.",
            type: 'question',
            showCancelButton: true,
            confirmButtonText: 'نعم، أعد التشغيل',
            cancelButtonText: 'الغاء'
        }).then((result) => {
            if (result.value) {
                $.ajax({
                    url: '/tarter/restart/' + id,
                    type: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        Swal.fire(
                            'تم!',
                            'تم إعادة تشغيل الطلب بنجاح.',
                            'success'
                        ).then(() => {
                            location.reload();
                        });
                    },
                    error: function (xhr) {
                        Swal.fire(
                            'خطأ!',
                            'حدث خطأ اثناء العملية.',
                            'error'
                        );
                    }
                });
            }
        });
    }

    // --- Bulk Action Visibility ---
    table.on('select deselect', function () {
        var selectedRows = table.rows({ selected: true }).count();
        if (selectedRows > 0) {
            $('.action-btns').show();
        } else {
            // $('.action-btns').hide(); // Kept commented as in stras.js to avoid hiding if desired logic differs or handled by CSS
        }
        calculateTotals();
    });

    // --- Bulk Delete Action ---
    $('#bulk-delete-btn').on('click', function () {
        var selectedRows = table.rows({ selected: true }).nodes();
        var ids = [];

        $.each(selectedRows, function (index, row) {
            var id = $(row).find('.tarter_id').val();
            if (id) ids.push(id);
        });

        if (ids.length === 0) return;

        Swal.fire({
            title: 'هل انت متأكد؟',
            text: "سيتم حذف " + ids.length + " طلب/طلبات ولن تستطيع استرجاعهم!",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'نعم، احذف',
            cancelButtonText: 'الغاء'
        }).then((result) => {
            if (result.value) {
                $.ajax({
                    url: '/tarter/bulk-delete',
                    type: 'POST',
                    data: {
                        ids: ids,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        Swal.fire(
                            'تم الحذف!',
                            'تم حذف الطلبات المحددة بنجاح.',
                            'success'
                        ).then(() => {
                            location.reload();
                        });
                    },
                    error: function (xhr) {
                        Swal.fire(
                            'خطأ!',
                            'حدث خطأ اثناء الحذف.',
                            'error'
                        );
                    }
                });
            }
        });
    });

    // --- Tarter Calculator Logic ---
    function calculateTotals() {
        var totals = {};
        var totalHeight = 0;
        var totalPiecesCalc = 0;
        var totalMachineTime = 0;
        var totalCardsCount = 0;
        var grandTotalCost = 0;

        // Prepare Prices Map
        var pricesMap = {
            needle: {},
            paper: {},
            global: {},
            machine: 0
        };

        if (window.tarterPrices) {
            window.tarterPrices.forEach(function (p) {
                if (p.type === 'needle') {
                    pricesMap.needle[p.size] = parseFloat(p.price) || 0;
                } else if (p.type === 'paper') {
                    var num = p.size.replace(/\D/g, '');
                    if (num) pricesMap.paper[num] = parseFloat(p.price) || 0;
                } else if (p.type === 'global' && p.size === 'operating_cost') {
                    pricesMap.global.op_cost = parseFloat(p.price) || 0;
                } else if (p.type === 'machine_time_cost') {
                    pricesMap.machine = parseFloat(p.price) || 0;
                }
            });
        }

        function getPaperPrice(width) {
            var w = Math.round(width);
            if (pricesMap.paper[w]) return pricesMap.paper[w];
            return 0;
        }

        var selectedRows = table.rows({ selected: true }).nodes();
        var anyChecked = selectedRows.length > 0;

        $.each(selectedRows, function (index, row) {
            var $row = $(row);
            var height = parseFloat($row.data('height')) || 0;
            var width = parseFloat($row.data('width')) || 0;
            var cardsCount = parseFloat($row.data('cards-count')) || 0;
            var piecesPerCard = parseFloat($row.data('pieces-per-card')) || 0;
            var machineTime = parseFloat($row.data('machine-time')) || 0;

            if (cardsCount > 0) {
                totalHeight += height * cardsCount / 100;
            }

            if (cardsCount > 0 && piecesPerCard > 0) {
                totalPiecesCalc += cardsCount * piecesPerCard;
            }

            totalMachineTime += machineTime;

            // --- Cost Calculation per Row ---
            var rowCardCost = 0;

            // 1. Paper Cost per Card
            var paperPrice = getPaperPrice(width);
            var cardPaperCost = (height / 100) * paperPrice;
            rowCardCost += cardPaperCost;

            // 2. Operating Cost per Card
            var opCost = pricesMap.global.op_cost || 0;
            rowCardCost += opCost;

            // 3. Machine Time Cost (Total for Item)
            var machineCostTotalForItem = machineTime * pricesMap.machine;

            var layersData = $row.data('layers');
            if (layersData) {
                if (typeof layersData === 'string') {
                    try {
                        layersData = JSON.parse(layersData);
                    } catch (e) { }
                }

                // 4. Sequins Cost
                $.each(layersData, function (index, layer) {
                    var size = layer.size;
                    var count = parseFloat(layer.count) || 0;

                    if (!totals[size]) {
                        totals[size] = 0;
                    }
                    totals[size] += count;

                    var unitPrice = pricesMap.needle[size] || 0;
                    rowCardCost += (count * unitPrice);
                });
            }

            var rowTotal = (rowCardCost * cardsCount) + machineCostTotalForItem;
            grandTotalCost += rowTotal;
            totalCardsCount += cardsCount;
        });

        var resultsContainer = $('#tarter-calculator-results');

        if (anyChecked) {
            var html = '';

            // Total Height
            if (totalHeight > 0) {
                html += '<span class="badge badge-info mb-1" style="font-size: 1em; margin-left:15px;"><i class="feather icon-maximize-2"></i>  طول الورق : ' + totalHeight.toFixed(2) + ' متر</span>';
            }

            // Total Calculated Pieces
            if (totalPiecesCalc > 0) {
                html += '<span class="badge badge-warning mb-1" style="font-size: 1em; margin-left:15px;"><i class="feather icon-package"></i> اجمالي القطع : ' + totalPiecesCalc + ' </span>';
            }

            // Total Machine Time
            if (totalMachineTime > 0) {
                html += '<span class="badge badge-danger mb-1" style="font-size: 1em; margin-left:15px;"><i class="feather icon-clock"></i> وقت الماكينة : ' + totalMachineTime + ' دقيقة</span>';
            }

            // Grand Total Cost
            if (grandTotalCost > 0) {
                html += '<span class="badge badge-primary mb-1" style="font-size: 1em; margin-left:15px;"><i class="feather icon-dollar-sign"></i> الاجمالي : ' + grandTotalCost.toFixed(2) + ' جنيه</span>';

                // Unit Price (Per Card)
                if (totalCardsCount > 0) {
                    var unitCost = grandTotalCost / totalCardsCount;
                    html += '<span class="badge badge-purple mb-1" style="font-size: 1em; margin-left:15px; background-color: #6f42c1; color:white;"><i class="feather icon-tag"></i> تكلفة الكارت : ' + unitCost.toFixed(3) + ' جنيه</span>';

                    if (totalPiecesCalc > 0) {
                        var pieceCost = grandTotalCost / totalPiecesCalc;
                        html += '<span class="badge badge-warning mb-1" style="font-size: 1em; margin-left:15px; background-color: #ff9f43; color:white;"><i class="feather icon-disc"></i> تكلفة القطعة : ' + pieceCost.toFixed(4) + ' جنيه</span>';
                    }
                    html += '<br>';
                } else {
                    html += '<br>';
                }
            } else {
                if (totalHeight > 0 || totalPiecesCalc > 0) html += '<br>';
            }

            html += '<i class="feather icon-bar-chart-2"></i> اجمالي الترتر: &nbsp;&nbsp;';
            var parts = [];

            Object.keys(totals).sort((a, b) => a - b).forEach(function (size) {
                parts.push('<span class="badge badge-success" style="font-size: 1em; margin-left:5px;"> مقاس ' + size + ': ' + totals[size] + ' </span>');
            });

            if (parts.length === 0) {
                html += 'لا توجد طبقات محددة';
            } else {
                html += parts.join(' ');
            }

            resultsContainer.html(html).slideDown();
        } else {
            resultsContainer.slideUp();
        }
    }

    // --- Add To Invoice ---
    window.addToInvoice = function () {
        var selectedRows = table.rows({ selected: true }).nodes();
        var ids = [];

        $.each(selectedRows, function (index, row) {
            var id = $(row).find('.tarter_id').val();
            if (id) ids.push(id);
        });

        if (ids.length === 0) {
            toastr.warning('Please select items first');
            return;
        }

        $.post('/invoices/add', {
            _token: $('meta[name="csrf-token"]').attr('content'),
            ids: ids,
            type: 'tarter'
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

});
