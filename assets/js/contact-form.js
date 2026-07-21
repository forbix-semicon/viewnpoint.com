(function () {
    "use strict";

    var form = document.getElementById("contact-form");
    if (!form) {
        return;
    }

    var statusEl = document.getElementById("contact-status");
    var submitBtn = document.getElementById("contact-submit");

    function showStatus(ok, text) {
        if (!statusEl) {
            return;
        }
        statusEl.hidden = false;
        statusEl.textContent = text;
        statusEl.classList.toggle("is-ok", ok);
        statusEl.classList.toggle("is-err", !ok);
    }

    form.addEventListener("submit", function (event) {
        event.preventDefault();
        if (submitBtn) {
            submitBtn.disabled = true;
        }

        var body = new FormData(form);

        fetch(form.action, {
            method: "POST",
            body: body,
            headers: { Accept: "application/json" },
        })
            .then(function (res) {
                return res.json().then(function (data) {
                    return { ok: res.ok && data.success, data: data };
                });
            })
            .then(function (result) {
                showStatus(!!result.ok, (result.data && result.data.message) || "Something went wrong.");
                if (result.ok) {
                    form.reset();
                }
            })
            .catch(function () {
                showStatus(false, "Network error. Please try again later.");
            })
            .finally(function () {
                if (submitBtn) {
                    submitBtn.disabled = false;
                }
            });
    });
})();
