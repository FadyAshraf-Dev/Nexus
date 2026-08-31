// Storefront counterpart to admin/js/shared/Notifications.js - same
// data source (the notification endpoints already allow all three
// roles), but the storefront has no Bootstrap dropdown component in
// use anywhere else, so this manages its own open/close state
// instead of relying on bootstrap.Dropdown.

const ELEMENTS = {
    toggle: "notification-bell-toggle",
    dropdown: "notification-dropdown",
    badge: "notification-badge",
    list: "notification-list",
};

class StorefrontNotifications {

    constructor() {

        this.toggle = document.getElementById(ELEMENTS.toggle);
        this.dropdown = document.getElementById(ELEMENTS.dropdown);
        this.badge = document.getElementById(ELEMENTS.badge);
        this.list = document.getElementById(ELEMENTS.list);

        if (!this.toggle || !this.dropdown || !this.badge || !this.list) {
            return;
        }

        this.hasOpenedOnce = false;
        this.isOpen = false;

        this.initialize();

    }

    async initialize() {

        await this.refresh();

        this.toggle.addEventListener("click", (event) => {
            event.stopPropagation();
            this.toggleDropdown();
        });

        document.addEventListener("click", (event) => {
            if (this.isOpen && !this.dropdown.contains(event.target)) {
                this.closeDropdown();
            }
        });

    }

    toggleDropdown() {

        this.isOpen ? this.closeDropdown() : this.openDropdown();

    }

    openDropdown() {

        this.isOpen = true;

        this.dropdown.classList.add("is-open");

        if (!this.hasOpenedOnce) {
            this.hasOpenedOnce = true;
            this.markAllRead();
        }

    }

    closeDropdown() {

        this.isOpen = false;

        this.dropdown.classList.remove("is-open");

    }

    async refresh() {

        try {

            const response = await fetch("/admin/api/notification/index.php", {
                headers: { Accept: "application/json" },
            });

            const data = await response.json();

            if (!data.success) {
                return;
            }

            const { notifications, unread_count } = data.data;

            this.renderList(notifications);
            this.renderBadge(unread_count);

        } catch (error) {
            console.error(error);
        }

    }

    async markAllRead() {

        try {

            const csrfToken = document
                .querySelector('meta[name="csrf-token"]')
                ?.content;

            await fetch("/admin/api/notification/mark-all-read.php", {
                method: "POST",
                headers: { "X-CSRF-TOKEN": csrfToken },
            });

            await this.refresh();

        } catch (error) {
            console.error(error);
        }

    }

    renderBadge(unreadCount) {

        if (unreadCount > 0) {
            this.badge.hidden = false;
            this.badge.textContent = unreadCount > 9 ? "9+" : String(unreadCount);
        } else {
            this.badge.hidden = true;
            this.badge.textContent = "";
        }

    }

    renderList(notifications) {

        if (!Array.isArray(notifications) || notifications.length === 0) {
            this.list.innerHTML = `
                <div class="header__notifications-empty">
                    No notifications yet.
                </div>
            `;
            return;
        }

        this.list.innerHTML = notifications
            .map((notification) => this.notificationItem(notification))
            .join("");

    }

    notificationItem(notification) {

        const href = notification.url ?? "#";
        const unreadClass = notification.is_read ? "" : "is-unread";

        return `
            <a class="header__notifications-item ${unreadClass}" href="${this.escape(href)}">
                <div class="header__notifications-item-text">
                    ${this.escape(notification.content)}
                </div>
                <div class="header__notifications-item-time">
                    ${this.formatRelativeTime(notification.created_at)}
                </div>
            </a>
        `;

    }

    formatRelativeTime(rawDate) {

        const date = new Date(rawDate);

        if (Number.isNaN(date.getTime())) {
            return "";
        }

        const seconds = Math.floor((Date.now() - date.getTime()) / 1000);

        if (seconds < 60) return "Just now";

        const minutes = Math.floor(seconds / 60);
        if (minutes < 60) return `${minutes}m`;

        const hours = Math.floor(minutes / 60);
        if (hours < 24) return `${hours}h`;

        const days = Math.floor(hours / 24);
        if (days < 7) return `${days}d`;

        return date.toLocaleDateString(undefined, {
            month: "short",
            day: "numeric",
        });

    }

    escape(value) {

        const div = document.createElement("div");

        div.textContent = value ?? "";

        return div.innerHTML;

    }

}

document.addEventListener(
    "DOMContentLoaded",
    () => new StorefrontNotifications(),
);
