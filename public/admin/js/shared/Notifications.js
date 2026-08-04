import Ajax from "../core/Ajax.js";
import Template from "../core/Template.js";

const ELEMENTS = {
  bellToggle: "navbarDropdownAlerts",
  badge: "notification-badge",
  list: "notification-list",
  empty: "notification-empty",
};

export default class Notifications extends Template {

  constructor() {

    super();

    this.bellToggle = document.getElementById(ELEMENTS.bellToggle);
    this.badge = document.getElementById(ELEMENTS.badge);
    this.list = document.getElementById(ELEMENTS.list);

    // Only wire up on pages that actually have the dropdown - some
    // admin pages/roles might not render the shared navbar.
    if (!this.bellToggle || !this.badge || !this.list) {
      return;
    }

    this.hasOpenedOnce = false;

    this.initialize();

  }

  async initialize() {

    await this.refresh();

    this.bellToggle.addEventListener("click", () => this.onBellOpen());

  }

  async refresh() {

    try {

      const response = await Ajax.get("/admin/api/notification/index.php");

      if (!response.success) {
        return;
      }

      const { notifications, unread_count } = response.data;

      this.renderList(notifications);
      this.renderBadge(unread_count);

    } catch (error) {
      console.error(error);
    }

  }

  async onBellOpen() {

    // Bootstrap's dropdown toggle fires this click before the menu is
    // actually shown, and toggles on every click (open AND close) - only
    // mark-as-read the first time it's opened per page load, so closing
    // the dropdown doesn't re-fire a mark-all-read call for nothing.
    if (this.hasOpenedOnce) {
      return;
    }

    this.hasOpenedOnce = true;

    try {

      await Ajax.post("/admin/api/notification/mark-all-read.php");

      // Re-fetch so the list reflects the now-read state (read styling)
      // and the badge clears.
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
        <div class="dropdown-item text-center text-muted py-3">
          No notifications yet.
        </div>
      `;
      return;
    }

    const items = notifications
      .map((notification) => this.notificationItem(notification))
      .join("");

    this.list.innerHTML = items;

    // data-feather icons inside the HTML just inserted won't render
    // until feather re-scans the DOM - matches the pattern used in
    // ProductImageGallery.js / MyProducts.js elsewhere in this app.
    if (window.feather) {
      window.feather.replace();
    }

  }

  notificationItem(notification) {

    const href = notification.url ?? "#!";
    const unreadClass = notification.is_read ? "" : "fw-bold";

    return `
      <a class="dropdown-item dropdown-notifications-item" href="${this.escape(href)}">
        <div class="dropdown-notifications-item-icon bg-primary">
          <i data-feather="bell"></i>
        </div>
        <div class="dropdown-notifications-item-content">
          <div class="dropdown-notifications-item-content-details">
            ${this.formatRelativeTime(notification.created_at)}
          </div>
          <div class="dropdown-notifications-item-content-text ${unreadClass}">
            ${this.escape(notification.content)}
          </div>
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

}

document.addEventListener(
  "DOMContentLoaded",
  () => new Notifications(),
);