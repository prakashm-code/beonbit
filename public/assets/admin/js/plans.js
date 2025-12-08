var base_url = $("#base_url").val();

$("#add_plan_form").validate({
    onfocusout: function (element) {
        $(element).valid();
    },

    rules: {
        name: {
            required: true,
            minlength: 2,
            // remote: {
            //     depends: function () {
            //         return $("#name").val().length > 0;
            //     },
            //     url: base_url + "/admin/plans/check_name",
            //     type: "POST",
            //     headers: {
            //         "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
            //     },
            //     data: {
            //         name: function () {
            //             return $("#name").val();
            //         },
            //         id: function () {
            //             return $("#plan_id").val() ?? null; // for edit mode
            //         }
            //     }
            // }
        },

        min_amount: {
            required: true,
            number: true,
            min: 1
        },

        max_amount: {
            required: true,
            number: true,
            min: function () {
                return parseFloat($("#min_amount").val()) || 1;
            }
        },

        roi: {
            required: true,
            number: true,
            min: 0.1,
            max: 100
        },

        duration: {
            required: true,
            digits: true,
            min: 1
        },

        plan_type: {
            required: true
        }
    },

    messages: {
        name: {
            required: "Please enter plan name",
            minlength: "Plan name must be at least 2 characters",
            // remote: "Plan with this name already exists"
        },

        min_amount: {
            required: "Please enter minimum amount",
            min: "Minimum amount must be at least 1"
        },

        max_amount: {
            required: "Please enter maximum amount",
            min: "Maximum must be greater than minimum amount"
        },

        daily_roi: {
            required: "Please enter ROI percentage",
            min: "ROI must be > 0",
            max: "ROI cannot be more than 100%"
        },

        duration_days: {
            required: "Please enter duration in days",
            min: "Duration must be at least 1 day"
        },

        type: {
            required: "Please select a status"
        }
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


$("#edit_plan_form").validate({
    onfocusout: function (element) {
        $(element).valid();
    },

    rules: {
        name: {
            required: true,
            minlength: 2,
            // remote: {
            //     depends: function () {
            //         return $("#name").val().length > 0;
            //     },
            //     url: base_url + "/admin/plans/check_name",
            //     type: "POST",
            //     headers: {
            //         "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
            //     },
            //     data: {
            //         name: function () {
            //             return $("#name").val();
            //         },
            //         id: function () {
            //             return $("#plan_id").val() ?? null; // for edit mode
            //         }
            //     }
            // }
        },

        min_amount: {
            required: true,
            number: true,
            min: 1
        },

        max_amount: {
            required: true,
            number: true,
            min: function () {
                return parseFloat($("#min_amount").val()) || 1;
            }
        },

        roi: {
            required: true,
            number: true,
            min: 0.1,
            max: 100
        },

        duration: {
            required: true,
            digits: true,
            min: 1
        },

        plan_type: {
            required: true
        }
    },

    messages: {
        name: {
            required: "Please enter plan name",
            minlength: "Plan name must be at least 2 characters",
            // remote: "Plan with this name already exists"
        },

        min_amount: {
            required: "Please enter minimum amount",
            min: "Minimum amount must be at least 1"
        },

        max_amount: {
            required: "Please enter maximum amount",
            min: "Maximum must be greater than minimum amount"
        },

        daily_roi: {
            required: "Please enter ROI percentage",
            min: "ROI must be > 0",
            max: "ROI cannot be more than 100%"
        },

        duration_days: {
            required: "Please enter duration in days",
            min: "Duration must be at least 1 day"
        },

        type: {
            required: "Please select a status"
        }
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
        url: base_url + '/admin/plans_update_status',
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
