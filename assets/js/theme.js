(function () {
    var key = "viewnpoint-theme";
    var root = document.documentElement;
    var toggle = document.getElementById("theme-toggle");
    if (!toggle) {
        return;
    }

    function apply(theme) {
        root.setAttribute("data-theme", theme);
        var isDark = theme === "dark";
        toggle.checked = isDark;
        toggle.setAttribute("aria-checked", isDark ? "true" : "false");
        localStorage.setItem(key, theme);
    }

    toggle.addEventListener("change", function () {
        apply(toggle.checked ? "dark" : "light");
    });

    apply(root.getAttribute("data-theme") || "dark");
})();
