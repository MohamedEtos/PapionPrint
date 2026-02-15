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

    // --- Customer Datalist Input Handler ---
    $(document).on('input', '#data-customer-view', function () {
        var val = $(this).val();
        var id = '';
        var opt = $('#customers-list option').filter(function () {
            return $(this).val() === val;
        });
        if (opt.length > 0) id = opt.attr('data-id');
        $('#data-customer').val(id);
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
    // Listed to paste on the document (or focus area)
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

    function resetForm() {
        $('#data-customer, #data-customer-view, #data-fabric-type, #data-source, #data-code, #data-width, #data-paper-shield, #data-meters, #data-price, #data-notes, #data-height, #data-cards-count, #data-pieces-per-card, #data-required-pieces').val('');
        $('#data-status').val('بانتظار اجراء');
        $('#data-payment-status').val('0');
        $('#data-image-upload').val('');
        editingOrderId = null;
        linkedOrderId = null;
        $('.new-data-title h4').text('اضافه اذن تشغيل');
        $('#saveDataBtn').text('Add Data');
    }

    function submitWizardData() {
        var formData = new FormData();

        var customerId = $('#data-customer').val();
        // If user typed name but didn't select from datalist, try to find ID
        var customerName = $('#data-customer-view').val();
        if (!customerId && customerName) {
            // More robust search than Attribute Selector which breaks on quotes
            var opt = $('#customers-list option').filter(function () {
                return $(this).val() === customerName;
            });
            if (opt.length > 0) customerId = opt.attr('data-id');
        }

        formData.append('customerId', customerId || '');
        formData.append('customer_name', customerName); // Send name for new customers

        formData.append('height', $('#data-height').val());
        formData.append('width', $('#data-width').val());
        formData.append('cards_count', $('#data-cards-count').val());
        formData.append('pieces_per_card', $('#data-pieces-per-card').val());
        formData.append('notes', $('#data-notes').val());
        // --- Calculate Manufacturing Cost ---
        var height = parseFloat($('#data-height').val()) || 0;
        var width = parseFloat($('#data-width').val()) || 0;
        var pieces_per_card = parseFloat($('#data-pieces-per-card').val()) || 0;

        var pricesMap = { stras: {}, paper: {}, global: {} };
        if (window.strasPrices) {
            window.strasPrices.forEach(function (p) {
                if (p.type === 'stras') pricesMap.stras[p.size] = parseFloat(p.price) || 0;
                else if (p.type === 'paper') {
                    var num = p.size.replace(/\D/g, '');
                    if (num) pricesMap.paper[num] = parseFloat(p.price) || 0;
                } else if (p.type === 'global' && p.size === 'operating_cost') {
                    pricesMap.global.op_cost = parseFloat(p.price) || 0;
                }
            });
        }

        var w = Math.round(width);
        var paperPrice = pricesMap.paper[w] || 0;
        var cardPaperCost = (height / 100) * paperPrice;
        var opCost = pricesMap.global.op_cost || 0;
        var strasCost = 0;

        $('#layers-container .layer-row').each(function (index, element) {
            var size = $(element).find('.layer-size').val();
            var count = parseFloat($(element).find('.layer-count').val()) || 0;
            if (size && count) {
                var unitPrice = pricesMap.stras[size] || 0;
                strasCost += (count * unitPrice);
            }
        });

        var rowCardCost = cardPaperCost + opCost + strasCost;
        var manufacturing_cost = pieces_per_card > 0 ? (rowCardCost / pieces_per_card) : 0;

        formData.append('manufacturing_cost', manufacturing_cost.toFixed(4));

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

        var url = "/stras/store";
        var type = "POST";

        if (editingOrderId) {
            url = "/stras/update/" + editingOrderId;
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
                setTimeout(function () { location.reload(); }, 1000); // Reload to simplistic refetch
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

    $(document).on("click", ".status-toggle", function (e) {
        e.stopPropagation();
        var $this = $(this);
        var $row = $this.closest('tr');
        // For Stras, we use 'data-id' attribute or similar if row structure changes.
        // But assuming row structure similar to presslist.
        // We need to fetch 'data-id' from the row.
        // In presslist, it was .order_id input val.
        var strasId = $row.find('.stras_id').val(); // New class for Stras IDs
        var printerOrderId = $row.find('.order_id').val();

        // If we clicked an existing Stras order
        if (strasId) {
            // Fetch and Edit
            // ... (Similar edit fetch logic if needed, but for now just toggle status?)
            // Wait, User wants to EDIT or Toggle?
            // The original code has logic to fetch for Edit if "Waiting".
            // Let's implement full Edit fetch.

            // Fetch existing Stras
            // TODO: Add route for showing/fetching single Stras
        }

        // For now, let's assume if it exists, we edit.
        // Since I didn't add the 'show' route in controller yet, maybe skipping complex edit for now.
        // But I can implement it in the controller easily.
    });

    // --- Actions ---

    window.editStras = function (id) {
        editingOrderId = id;

        // Fetch data
        $.get('/stras/show/' + id, function (data) {
            $('.new-data-title h4').text('تعديل طلب');
            $('#saveDataBtn').text('Update');

            // Populate Fields
            // Use customer ID. If name input, set name and hidden ID.
            $('#data-customer').val(data.customerId);
            if (data.customer) $('#data-customer-view').val(data.customer.name);

            $('#data-height').val(data.height);
            $('#data-width').val(Math.round(data.width));
            $('#data-cards-count').val(data.cards_count);
            $('#data-pieces-per-card').val(data.pieces_per_card);
            $('#data-notes').val(data.notes);

            // Required Pieces Helper - Estimate? 
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
                                                <label>المقاس</label>
                                                <select class="form-control layer-size" name="layers[${index}][size]">
                                                     // Need to have options. Cloning hidden template or similar is better, 
                                                     // but here we might loose options if we just string build.
                                                     // Better: Clone a template if possible, or fetch options earlier?
                                                     // Actually, let's use the first row logic if exists?
                                                     // Or just build options manually (Standard sizes).
                                                     <option value="6">6</option>
                                                     <option value="8">8</option>
                                                     <option value="10">10</option>
                                                     <option value="12">12</option>
                                                     // We should ideally use the sizes from blade...
                                                     // Let's rely on the fact that existing options are standard.
                                                     // Or better, grab options from a hidden select or just hardcode standard ones for now 
                                                     // as we don't have easy access to blade variable here in JS without passing it.
                                                     // Let's iterate options from existing select in DOM if available.
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
                    // Set selected size
                    // To populate options correctly, let's grab them from the hidden/first select in the DOM (assuming one exists or we kept a template)
                    // But wait, the form is cleared or hidden. The wizard steps exist.

                    // Helper: Generate options from window.strasPrices
                    var options = '';
                    if (window.strasPrices) {
                        window.strasPrices.forEach(function (p) {
                            // Assuming we only want 'stras' type sizes?
                            // Based on calculateTotals, we have 'stras' and 'paper'. 
                            // If blade is sending all, we might want to filter or just use all if that is intended.
                            // However, usually 'stras' sizes are what we want here.
                            if (p.type === 'stras') {
                                options += `<option value="${p.size}">${p.size}</option>`;
                            }
                        });
                    }

                    if (options === '') {
                        // Fallback in case filtered list is empty but we have something
                        options = '<option value="6">6</option><option value="8">8</option><option value="10">10</option><option value="12">12</option>';
                    }

                    $row.find('.layer-size').html(options).val(layer.size);
                    $('#layers-container').append($row);
                });
                layerIndex = data.layers.length;
            } else {
                // Add one empty
                $('#add-layer-btn').trigger('click');
            }

            // Show Form
            $('#validation').slideDown();
            // Scroll to form
            $('html, body').animate({
                scrollTop: $("#validation").offset().top
            }, 500);
        });
    }

    window.deleteStras = function (id) {
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
                    url: '/stras/delete/' + id,
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

    window.restartStras = function (id) {
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
                    url: '/stras/restart/' + id,
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

    $('#saveDataBtn').on('click', function (e) {
        e.preventDefault();
        submitWizardData();
    });

    // --- Bulk Action Visibility ---
    table.on('select deselect', function () {
        var selectedRows = table.rows({ selected: true }).count();
        if (selectedRows > 0) {
            $('.action-btns').show();
        } else {
        }
    });

    // --- Bulk Delete Action ---
    $('#bulk-delete-btn').on('click', function () {
        var selectedRows = table.rows({ selected: true }).nodes();
        var ids = [];

        $.each(selectedRows, function (index, row) {
            var id = $(row).find('.stras_id').val(); // Assuming we have .stras_id input in row, or use data attribute?
            // View shows: <input type="hidden" class="stras_id" value="{{ $Record->id }}"> in the second column (image)
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
                    url: '/stras/bulk-delete',
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

    // --- Stras Calculator Logic ---

    // Using DataTables select events since 'checkboxes' plugin is active
    table.on('select deselect', function (e, dt, type, indexes) {
        if (type === 'row') {
            calculateTotals();
        }
    });

    // Also listen for draw event to re-calculate if needed (e.g. page change with preserved selection)
    table.on('draw', function () {
        // calculateTotals(); // Optional if selection persists
    });

    function calculateTotals() {
        var totals = {};
        var totalHeight = 0;
        var totalPiecesCalc = 0;
        var totalCardsCount = 0;
        var grandTotalCost = 0;

        // Prepare Prices Map
        var pricesMap = {
            stras: {}, // Equivalent to needle
            paper: {},
            global: {},
        };

        if (window.strasPrices) {
            window.strasPrices.forEach(function (p) {
                if (p.type === 'stras') {
                    pricesMap.stras[p.size] = parseFloat(p.price) || 0;
                } else if (p.type === 'paper') {
                    var num = p.size.replace(/\D/g, '');
                    if (num) pricesMap.paper[num] = parseFloat(p.price) || 0;
                } else if (p.type === 'global' && p.size === 'operating_cost') {
                    pricesMap.global.op_cost = parseFloat(p.price) || 0;
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

            if (cardsCount > 0) {
                totalHeight += height * cardsCount / 100;
            }

            if (cardsCount > 0 && piecesPerCard > 0) {
                totalPiecesCalc += cardsCount * piecesPerCard;
            }

            // --- Cost Calculation per Row ---
            var rowCardCost = 0;

            // 1. Paper Cost per Card
            var paperPrice = getPaperPrice(width);
            var cardPaperCost = (height / 100) * paperPrice;
            rowCardCost += cardPaperCost;

            // 2. Operating Cost per Card
            var opCost = pricesMap.global.op_cost || 0;
            rowCardCost += opCost;

            var layersData = $row.data('layers');
            if (layersData) {
                if (typeof layersData === 'string') {
                    try {
                        layersData = JSON.parse(layersData);
                    } catch (e) { }
                }

                // 3. Stras Cost
                $.each(layersData, function (index, layer) {
                    var size = layer.size;
                    var count = parseFloat(layer.count) || 0;

                    if (!totals[size]) {
                        totals[size] = 0;
                    }
                    totals[size] += count; // Accumulate global count for badge

                    var unitPrice = pricesMap.stras[size] || 0;
                    rowCardCost += (count * unitPrice);
                });
            }

            var rowTotal = (rowCardCost * cardsCount);
            grandTotalCost += rowTotal;
            totalCardsCount += cardsCount;
        });

        var resultsContainer = $('#stras-calculator-results');

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

            // Grand Total Cost
            if (grandTotalCost > 0) {
                html += '<span class="badge badge-primary mb-1" style="font-size: 1em; margin-left:15px;"><i class="feather icon-dollar-sign"></i> الاجمالي : ' + grandTotalCost.toFixed(2) + ' جنيه</span>';

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


            html += '<i class="feather icon-bar-chart-2"></i> اجمالي الاستراس: &nbsp;&nbsp;';
            var parts = [];

            Object.keys(totals).sort((a, b) => a - b).forEach(function (size) { // Sorting might be weird for strings like 'ss10', 'ss6'.
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
            var id = $(row).find('.stras_id').val();
            if (id) ids.push(id);
        });

        if (ids.length === 0) {
            toastr.warning('Please select items first');
            return;
        }

        $.post('/invoices/add', {
            _token: $('meta[name="csrf-token"]').attr('content'),
            ids: ids,
            type: 'stras'
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
