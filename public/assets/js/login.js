$(function () {
    $("#loginForm").on("submit", function (event) {
        event.preventDefault();

        const form = $(this);
        const submitButton = form.find('button[type="submit"]');

        submitButton.prop("disabled", true);

        $.ajax({
            url: form.attr("action"),
            method: "POST",
            contentType: "application/json",
            dataType: "json",
            data: JSON.stringify({
                email: form.find('[name="email"]').val(),
                password: form.find('[name="password"]').val(),
            }),
        })
            .done(function (response) {
                window.location.href = response.redirect;
            })
            .fail(function (xhr) {
                const message = xhr.responseJSON?.message || "Login failed.";
                alert(message);
            })
            .always(function () {
                submitButton.prop("disabled", false);
            });
    });
});
