var base_url = $("#base_url").val();

$("#add_category_form").validate({
    onkeyup: false,
    rules: {
        category_name: {
            required: true,
            remote: {
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                },
                url: base_url + "/admin/check_category_is_exist",
                type: "POST",
                data: {
                    category_name: function () {
                        return $("input[name='category_name']").val();
                    },
                    id: function () {
                        return null;
                    },
                },
            },
        },
    },
    messages: {
        category_name: {
            required: "Please enter category name",
            remote: "This category already exists",
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
    },
});

let typingTimer;
const doneTypingInterval = 1000;

$(document).on("keyup", "input[name='category_name']", function () {
    clearTimeout(typingTimer);
    typingTimer = setTimeout(function () {
        $("input[name='category_name']").valid();
    }, doneTypingInterval);
});

$("#edit_category_form").validate({
    onkeyup: false,
    rules: {
        category_name: {
            required: true,
            remote: {
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                },
                url: base_url + "/admin/check_category_is_exist",
                type: "POST",
                data: {
                    category_name: function () {
                        return $("#category_name").val();
                    },
                    id: function () {
                        return $("#category_id").val();
                    },
                },
            },
        },
    },
    messages: {
        category_name: {
            required: "Please enter category name",
            remote: "This category already exists",
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
    },
});

$(document).on("click", ".delete_category", function () {
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
            $("#delete_category_form" + id).submit();
        }
    });
});

$(document).on("change", ".toggle-display-status", function () {
    let id = $(this).data("id");
    let isChecked = $(this).is(":checked");
    $.ajax({
        url: base_url + "/admin/toggle_category_display",
        type: "POST",
        data: {
            id: id,
            _token: $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (response) {
            if (response.success) {
                toastr.success("Display status updated");
            } else {
                toastr.error("Something went wrong!");
            }
        },
        error: function () {
            toastr.error("Server error occurred!");
        },
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
                url: base_url + "/admin/delete_multiple_category",
                type: "POST",
                data: {
                    ids: ids,
                    _token: $('meta[name="csrf-token"]').attr("content"),
                },
                success: function (response) {
                    if (response.success == false) {
                        toastr.error(response.message);
                    }
                    $("#select-all").prop("checked", false);
                    $("#delete-selected").css("display", "none");
                    $("#category-table").DataTable().ajax.reload();
                },
            });
        }
    });
});
