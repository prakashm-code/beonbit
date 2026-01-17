var base_url = $("#base_url").val();
$("#add_user_form").validate({
    // onkeyup: false,
    onfocusout: function (element) {
        $(element).valid();
    },
    rules: {
        first_name: {
            required: true,
        },
        last_name: {
            required: true,
        },
        phone: {
            required: true,
        },
        email: {
            required: true,
            email: true,
            remote: {
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                },
                url: base_url + "/admin/check_user_is_exist",
                type: "POST",
                data: {
                    category_name: function () {
                        return $("input[name='email']").val();
                    },
                    id: function () {
                        return null;
                    },
                },
            },
        },
        password: {
            required: true,
        },
        c_password: {
            required: true,
            equalTo: "#password"
        }
    },
    messages: {
        first_name: {
            required: "Please enter first name",
        },
        last_name: {
            required: "Please enter last name",
        },
        email: {
            required: "Please enter email",
            email: "Please enter a valid email",
            remote: "This email already exists",
        },
        password: {
            required: "Please enter a password",
        },
        c_password: {
            required: "Please confirm password",
            equalTo: "Password and confirm password must match"
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
    },
});

$(document).on('input', '.only-number', function () {
    this.value = this.value.replace(/[^0-9]/g, '');
});

$(document).on('input', '.only-decimal', function () {
    this.value = this.value
        .replace(/[^0-9.]/g, '')   // allow only numbers & dot
        .replace(/(\..*)\./g, '$1'); // prevent more than one dot
});

$.validator.addMethod("withinPlanRange", function (value, element) {
    let plan = $("#plan_id option:selected");
    let min = parseFloat(plan.data("min"));
    let max = parseFloat(plan.data("max"));
    let amount = parseFloat(value);

    if (!plan.val()) {
        return false; // no plan selected
    }

    return amount >= min && amount <= max;
}, "Amount must be between selected plan's minimum and maximum");

$("#add_plan_user_form").validate({
    // onkeyup: false,
    onfocusout: function (element) {
        $(element).valid();
    },
    rules: {
        plan_id: {
            required: true,
        },
        amount: {
            required: true,
            withinPlanRange: true   // ✅ YOUR NEW RULE

        }
    },
    messages: {
        plan_id: {
            required: "Please choose a plan",
        },
        amount: {
            required: "Please enter amount",
            withinPlanRange: "Amount must be within selected plan limit"

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
    },
});

// let typingTimer;
// const doneTypingInterval = 1000;

// $(document).on("keyup", "input[name='name']", function () {
//     clearTimeout(typingTimer);
//     typingTimer = setTimeout(function () {
//         $("input[name='name']").valid();
//     }, doneTypingInterval);
// });

$(document).on("click", ".delete_user", function () {
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
            $("#delete_user_form" + id).submit();
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
