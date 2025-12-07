var loadFile = function (event) {
    var output = document.getElementById("preview_image");
    output.src = URL.createObjectURL(event.target.files[0]);
    $(".gift_image_validation").text("");
    $(".banner_image_validation").text("");
};

$(document).ready(function () {
    $("#tags").tagsinput();

    $.validator.addMethod(
        "requireTags",
        function (value, element) {
            return value.trim() !== "";
        },
        "Please enter at least one tag."
    );

    $("#tags").on("itemAdded itemRemoved", function () {
        $(this).valid();
    });

    $("#add_image_form").validate({
        ignore: [],
        rules: {
            category: {
                required: true,
            },
            image_name: {
                required: true,
            },
            image_price: {
                required: true,
            },
            image_description: {
                required: true,
            },
            image: {
                required: true,
                extension: "jpg|jpeg|png",
            },
            // tags: { requireTags: true },
        },
        messages: {
            category: {
                required: "Please select a category",
            },
            image_name: {
                required: "Please enter a image name",
            },
            image_price: {
                required: "Please enter image price",
            },
            image_description: {
                required: "Please enter a image description",
            },
            image: {
                required: "Please select a image",
                extension: "Please upload only png/jpg/jpeg",
            },
            // tags: {
            //     required: "Please enter tags for this image",
            // },
        },

        normalizer: function (value) {
            return $.trim(value);
        },

        errorClass: "text-danger",
        errorElement: "span",
        validClass: "is-valid",

        highlight: function (element) {
            if ($(element).attr("data-role") === "tagsinput") {
                $(element)
                    .closest(".mb-3")
                    .find(".bootstrap-tagsinput")
                    .addClass("form-control is-invalid");
            } else {
                $(element).addClass("is-invalid");
            }
        },
        unhighlight: function (element) {
            if ($(element).attr("data-role") === "tagsinput") {
                $(element)
                    .closest(".mb-3")
                    .find(".bootstrap-tagsinput")
                    .removeClass("form-control is-invalid");
            } else {
                $(element).removeClass("is-invalid");
            }
        },

        errorPlacement: function (error, element) {
            error.addClass("invalid-feedback");
            if (element.attr("data-role") === "tagsinput") {
                error.insertAfter(element.siblings(".bootstrap-tagsinput"));
            } else {
                error.insertAfter(element);
            }
        },
        submitHandler: function (form) {
            $(form)
                .find('button[type="submit"]')
                .prop("disabled", true)
                .text("Please wait...");
            $("#loader").css("display", "flex");
            form.submit();
        },
    });

    $("#edit_image_form").validate({
        ignore: [],
        rules: {
            category: {
                required: true,
            },
            image_name: {
                required: true,
            },
            image_price: {
                required: true,
            },
            image_description: {
                required: true,
            },
            image: {
                extension: "jpg|jpeg|png",
            },
            // tags: { requireTags: true },
        },
        messages: {
            category: {
                required: "Please select a category",
            },
            image_name: {
                required: "Please enter a image name",
            },
            image_price: {
                required: "Please enter image price",
            },
            image_description: {
                required: "Please enter a image description",
            },
            image: {
                extension: "Please upload only png/jpg/jpeg",
            },
            // tags: {
            //     required: "Please enter tags for this image",
            // },
        },

        normalizer: function (value) {
            return $.trim(value);
        },

        errorClass: "text-danger",
        errorElement: "span",
        validClass: "is-valid",

        highlight: function (element) {
            if ($(element).attr("data-role") === "tagsinput") {
                $(element)
                    .closest(".mb-3")
                    .find(".bootstrap-tagsinput")
                    .addClass("form-control is-invalid");
            } else {
                $(element).addClass("is-invalid");
            }
        },
        unhighlight: function (element) {
            if ($(element).attr("data-role") === "tagsinput") {
                $(element)
                    .closest(".mb-3")
                    .find(".bootstrap-tagsinput")
                    .removeClass("form-control is-invalid");
            } else {
                $(element).removeClass("is-invalid");
            }
        },
        submitHandler: function (form) {
            $(form)
                .find('button[type="submit"]')
                .prop("disabled", true)
                .text("Please wait...");

            form.submit();
        },
    });

    $("#tags").on("itemAdded itemRemoved", function () {
        $(this).valid();
    });
});

$(document).on("click", ".delete_image", function () {
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
            $("#delete_image_form" + id).submit();
        }
    });
});

$(document).on("click", ".preview-image", function () {
    const src = $(this).data("src");
    const img = $("#previewImage");

    img.attr("src", src);
    new bootstrap.Modal(document.getElementById("imagePreviewModal")).show();

    $("#imagePreviewModal").on("hidden.bs.modal", function () {
        document.activeElement?.blur();
    });
});

$(document).on("change", ".toggle-display-images", function () {
    let id = $(this).data("id");
    let isChecked = $(this).is(":checked");
    $.ajax({
        url: base_url + "/admin/toggle_image_display",
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

document.addEventListener("DOMContentLoaded", () => {
    const tagsInput = document.getElementById("tags");

    tagsInput.addEventListener("keydown", (e) => {
        if (e.key === "Enter") {
            e.preventDefault(); // Prevent form submission
            const tagValue = tagsInput.value.trim();

            if (tagValue) {
                // Add the tag or handle the input logic here
                console.log("Tag added:", tagValue);
                tagsInput.value = ""; // Clear the input after adding the tag
            }
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
        toastr.success("Please select at least one image");
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
                url: base_url + "/admin/delete_multiple_image",
                type: "POST",
                data: {
                    ids: ids,
                    _token: $('meta[name="csrf-token"]').attr("content"),
                },
                success: function (response) {
                    $("#select-all").prop("checked", false);
                    $("#delete-selected").css("display", "none");
                    $("#images-table").DataTable().ajax.reload();
                },
            });
        }
    });
});
