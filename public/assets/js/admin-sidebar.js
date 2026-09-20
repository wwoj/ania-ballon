/**
 * Admin Sidebar Toggle Module (jQuery)
 * Toggles mini-sidebar state (icons only) and persists layout preferences.
 */
(function ($) {
    "use strict";

    function isMobileView() {
        return window.innerWidth <= 992;
    }

    function initSidebarToggle() {
        const $adminLayout = $(".admin-layout");
        const $toggleBtn = $(".admin-topbar__toggle");
        const $overlay = $("#sidebarOverlay");

        if (!$adminLayout.length || !$toggleBtn.length) {
            return;
        }

        // Restore collapsed preference on desktop
        const isCollapsedSaved =
            localStorage.getItem("admin_sidebar_collapsed") === "true";
        if (isCollapsedSaved && !isMobileView()) {
            $adminLayout.addClass("admin-layout--sidebar-collapsed");
        }

        // Toggle functionality
        function toggleSidebar(e) {
            if (e) {
                e.preventDefault();
            }

            if (isMobileView()) {
                $adminLayout.toggleClass("admin-layout--mobile-open");
            } else {
                $adminLayout.toggleClass("admin-layout--sidebar-collapsed");
                const isCollapsed = $adminLayout.hasClass(
                    "admin-layout--sidebar-collapsed",
                );
                localStorage.setItem("admin_sidebar_collapsed", isCollapsed);
            }
        }

        // Bind click & touch event
        $toggleBtn.on("click touchstart", function (e) {
            if (e.type === "touchstart") {
                $(this).off("click");
            }
            toggleSidebar(e);
        });

        // Close drawer on overlay click (mobile)
        if ($overlay.length) {
            $overlay.on("click touchstart", function (e) {
                e.preventDefault();
                $adminLayout.removeClass("admin-layout--mobile-open");
            });
        }

        // Clean mobile state on viewport resize
        $(window).on("resize", function () {
            if (!isMobileView()) {
                $adminLayout.removeClass("admin-layout--mobile-open");
            }
        });
    }

    $(document).ready(initSidebarToggle);
})(jQuery);
