var base_url = $("#base_url").val();

$("#add_term_condition_form").validate({
    rules: {
        title: {
            required: true,
        },
        description: {
            required: true,
        },
    },
    messages: {
        title: {
            required: "Please enter title",
        },
        description: {
            required: "Please enter description",
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

$("#add_privacy_policy_form").validate({
    rules: {
        title: {
            required: true,
        },
        description: {
            required: true,
        },
    },
    messages: {
        title: {
            required: "Please enter title",
        },
        description: {
            required: "Please enter description",
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
document.querySelectorAll(".ckeditor").forEach((el) => {
    CKEDITOR.replace(el);
});
