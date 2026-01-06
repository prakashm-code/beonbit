var base_url = $("#base_url").val();
$.validator.addMethod("greaterThanOrEqual", function (value, element, param) {
    var fromVal = parseFloat($(param).val());
    var toVal = parseFloat(value);

    if (isNaN(fromVal) || isNaN(toVal)) {
        return true; // let required/number handle empty
    }

    return toVal >= fromVal;
}, "Ending level must be greater than or equal to starting level");
$("#add_referral_form").validate({
    onfocusout: function (element) {
        $(element).valid();
    },

    rules: {
        from_level: {
            required: true,
        },
        to_level: {
            required: true,
            greaterThanOrEqual: "#from_level"

        },

        percentage: {
            required: true,
            number: true,
        },
    },

    messages: {
        from_level: {
            required: "Please enter starting referral level",
            // minlength: "Plan name must be at least 2 characters",
            // remote: "Plan with this name already exists"
        },
        to_level: {
            required: "Please enter ending referral level",
            greaterThanOrEqual: "Ending level must be greater than or equal to starting level"

        },
        percentage: {
            required: "Please enter commission percentage",
            // min: "Minimum amount must be at least 1"
        },
    },

    normalizer: function (value) {
        return $.trim(value);
    },

    errorClass: "text-danger",
    errorElement: "span",

    highlight: function (element) {
        $(element).addClass("is-invalid");
    },

    unhighlight: function (element) {
        $(element).removeClass("is-invalid");
    },

    submitHandler: function (form) {
        $(form)
            .find('button[type="submit"]')
            .prop("disabled", true)
            .text("Please wait...");

        form.submit();
    }
});


$("#edit_referral_form").validate({
    onfocusout: function (element) {
        $(element).valid();
    },


    rules: {
        from_level: {
            required: true,
        },
        to_level: {
            required: true,
            greaterThanOrEqual: "#from_level"

        },

        percentage: {
            required: true,
            number: true,
        },
    },

    messages: {
        from_level: {
            required: "Please enter starting referral level",
            // minlength: "Plan name must be at least 2 characters",
            // remote: "Plan with this name already exists"
        },
        to_level: {
            required: "Please enter ending referral level",
            greaterThanOrEqual: "Ending level must be greater than or equal to starting level"

        },

        percentage: {
            required: "Please enter commission percentage",
            // min: "Minimum amount must be at least 1"
        },
    },

    normalizer: function (value) {
        return $.trim(value);
    },

    errorClass: "text-danger",
    errorElement: "span",

    highlight: function (element) {
        $(element).addClass("is-invalid");
    },

    unhighlight: function (element) {
        $(element).removeClass("is-invalid");
    },

    submitHandler: function (form) {
        $(form)
            .find('button[type="submit"]')
            .prop("disabled", true)
            .text("Please wait...");

        form.submit();
    }
});
$(document).on('input', '.only-number', function () {
    this.value = this.value.replace(/[^0-9]/g, '');
});

$(document).on('input', '.only-decimal', function () {
    this.value = this.value
        .replace(/[^0-9.]/g, '')   // allow only numbers & dot
        .replace(/(\..*)\./g, '$1'); // prevent more than one dot
});
$(document).on('change', '.status-toggle', function () {
    var status = $(this).is(':checked') ? 1 : 0;
    var id = $(this).data('id');

    $.ajax({
        url: base_url + '/admin/referral_setting_update_status',
        type: 'POST',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content'),
            id: id,
            status: status
        },
        success: function (response) {
            if (response.success) {
                toastr.success('Status updated successfully');
            } else {
                toastr.error('Failed to update status');

            }
        },
        error: function () {
            toastr.error('Something went wrong!');
        }
    });
});

$(document).on("click", ".delete_referral_setting", function () {
    var id = $(this).data("id");
    Swal.fire({
        title: "Are you sure?",
        text: "You won't be able to revert this!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, delete it!",
    }).then((result) => {
        if (result.isConfirmed) {
            // alert();
            $("#delete_referral_setting_form" + id).submit();
        }
    });
});

function toggleDeleteButton() {
    let anyChecked = $(".row-checkbox:checked").length > 0;

    if (anyChecked) {
        $("#delete-selected").show();
    } else {
        $("#delete-selected").hide();
    }
}

$(document).on("change", ".row-checkbox", function () {
    toggleDeleteButton();
});

$(document).on("change", "#select-all", function () {
    $(".row-checkbox").prop("checked", this.checked);
    toggleDeleteButton();
});

$("#select-all").on("click", function () {
    $(".row-checkbox").prop("checked", this.checked);
});

$("#delete-selected").on("click", function () {
    let ids = [];

    $(".row-checkbox:checked").each(function () {
        ids.push($(this).val());
    });

    if (ids.length === 0) {
        toastr.success("Please select at least one user");
        return;
    }

    Swal.fire({
        title: "Are you sure?",
        text: "You won't be able to revert this!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, delete it!",
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: base_url + "/admin/delete_multiple_user",
                type: "POST",
                data: {
                    ids: ids,
                    _token: $('meta[name="csrf-token"]').attr("content"),
                },
                success: function (response) {
                    $("#select-all").prop("checked", false);
                    $("#delete-selected").css("display", "none");
                    $("#users-table").DataTable().ajax.reload();
                },
            });
        }
    });
});
