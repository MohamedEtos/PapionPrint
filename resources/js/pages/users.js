$(document).ready(function () {

    // Clear sidebar when opening for new item
    $(".dt-buttons .btn-outline-primary").on("click", function () {
        $('#data-id').val('');
        $('#data-name').val('');
        $('#data-username').val('');
        $('#data-email').val('');
        $('#data-password').val('');
        $('#data-base-salary').val('');
        $('#data-working-hours').val('8');
        $('#data-shift-start').val('');
        $('#data-shift-end').val('');
        $('#data-overtime-rate').val('1.5');
        $('#data-joining-date').val('');
        $('#data-resignation-date').val('');
        $('.role-checkbox').prop('checked', false);
        $(".add-new-data").addClass("show");
        $(".overlay-bg").addClass("show");
    });

    // Close Sidebar
    $('.hide-data-sidebar, .cancel-data-btn, .overlay-bg').on("click", function (e) {
        e.stopPropagation();
        $(".add-new-data").removeClass("show");
        $(".overlay-bg").removeClass("show");
    });

    // On Edit
    $(document).on("click", ".action-edit", function (e) {
        e.stopPropagation();
        var $row = $(this).closest('tr');
        var $btn = $(this);

        // Try getting data from attribute first, then fallback to DOM parsing
        var userId = $btn.data('id') || $row.find('.user_id').val();
        var userName = $btn.data('name') || $row.find('.user-name').text().trim();
        var userUsername = $btn.data('username') || $row.find('.user-username').text().trim();
        var userEmail = $btn.data('email') || $row.find('.user-email').text().trim();
        var roles = [];

        $row.find('.role-item').each(function () {
            roles.push($(this).text().trim());
        });

        var userBaseSalary = $btn.data('base_salary') || '';
        var userWorkingHours = $btn.data('working_hours') || '8';
        var userShiftStart = $btn.data('shift_start') || '';
        var userShiftEnd = $btn.data('shift_end') || '';
        var userOvertimeRate = $btn.data('overtime_rate') || '1.5';
        var userJoiningDate = $btn.data('joining_date') || '';
        var userResignationDate = $btn.data('resignation_date') || '';

        $('#data-id').val(userId);
        $('#data-name').val(userName);
        $('#data-username').val(userUsername);
        $('#data-email').val(userEmail);
        $('#data-password').val('');
        $('#data-base-salary').val(userBaseSalary);
        $('#data-working-hours').val(userWorkingHours);
        $('#data-shift-start').val(userShiftStart);
        $('#data-shift-end').val(userShiftEnd);
        $('#data-overtime-rate').val(userOvertimeRate);
        $('#data-joining-date').val(userJoiningDate);
        $('#data-resignation-date').val(userResignationDate);

        // Reset checkboxes
        $('.role-checkbox').prop('checked', false);

        // Check roles for this user
        roles.forEach(function (role) {
            $('.role-checkbox[value="' + role + '"]').prop('checked', true);
        });

        $(".add-new-data").addClass("show");
        $(".overlay-bg").addClass("show");
    });

    // On Delete
    $(document).on("click", ".action-delete", function (e) {
        e.stopPropagation();
        var $row = $(this).closest('td').parent('tr');
        var userId = $row.find('.user_id').val();

        if (!userId) {
            $row.fadeOut();
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
                    url: "/users/delete/" + userId,
                    type: "POST",
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        $row.fadeOut(function () {
                            $(this).remove();
                        });
                        Swal.fire({
                            type: "success",
                            title: 'تم الحذف!',
                            text: 'تم حذف المستخدم بنجاح!',
                            confirmButtonClass: 'btn btn-success',
                        });
                    },
                    error: function (xhr) {
                        console.error("Error deleting user:", xhr);
                        Swal.fire({
                            title: "خطأ!",
                            text: "حدث خطأ أثناء الحذف.",
                            type: "error",
                            confirmButtonClass: 'btn btn-primary',
                            buttonsStyling: false,
                        });
                    }
                });
            }
        });
    });

    // Save Data
    $('#saveDataBtn').on('click', function (e) {
        e.preventDefault();

        var id = $('#data-id').val();
        var name = $('#data-name').val();
        var username = $('#data-username').val();
        var email = $('#data-email').val();
        var password = $('#data-password').val();
        var base_salary = $('#data-base-salary').val();
        var working_hours = $('#data-working-hours').val();
        var shift_start = $('#data-shift-start').val();
        var shift_end = $('#data-shift-end').val();
        var overtime_rate = $('#data-overtime-rate').val();
        var joining_date = $('#data-joining-date').val();
        var resignation_date = $('#data-resignation-date').val();
        var selectedRoles = [];

        $('.role-checkbox:checked').each(function () {
            selectedRoles.push($(this).val());
        });

        if (!name || !email || !username) {
            Swal.fire({
                title: "خطأ!",
                text: "يرجى ادخال الاسم واسم المستخدم والبريد الالكتروني",
                type: "error",
                confirmButtonClass: 'btn btn-primary',
                buttonsStyling: false,
            });
            return;
        }

        if (!id && !password) {
            Swal.fire({
                title: "خطأ!",
                text: "يرجى ادخال كلمة المرور للمستخدم الجديد",
                type: "error",
                confirmButtonClass: 'btn btn-primary',
                buttonsStyling: false,
            });
            return;
        }

        var url = id ? "/users/update/" + id : "/users/store";

        $.ajax({
            url: url,
            type: "POST",
            data: {
                name: name,
                username: username,
                email: email,
                password: password,
                base_salary: base_salary,
                working_hours: working_hours,
                shift_start: shift_start,
                shift_end: shift_end,
                overtime_rate: overtime_rate,
                joining_date: joining_date,
                resignation_date: resignation_date,
                roles: selectedRoles,
                update_roles: true,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                Swal.fire({
                    type: "success",
                    title: 'تم الحفظ!',
                    text: 'تم حفظ البيانات بنجاح!',
                    confirmButtonClass: 'btn btn-success',
                }).then(() => {
                    location.reload();
                });

                $(".add-new-data").removeClass("show");
                $(".overlay-bg").removeClass("show");
            },
            error: function (xhr) {
                console.error("Error saving user:", xhr);
                var message = "حدث خطأ أثناء الحفظ";
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
                // Check for validation errors
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    message = "";
                    $.each(xhr.responseJSON.errors, function (key, value) {
                        message += value[0] + "\n";
                    });
                }

                Swal.fire({
                    title: "خطأ!",
                    text: message,
                    type: "error",
                    confirmButtonClass: 'btn btn-primary',
                    buttonsStyling: false,
                });
            }
        });
    });
});
