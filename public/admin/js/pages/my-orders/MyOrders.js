import Dom from "../../core/Dom.js";
import Money from "../../core/Money.js";
import Order from "../../order/Order.js";
import CheckoutTemplate from "../checkout/CheckoutTemplate.js";

const ELEMENTS = {
  loading: "my-orders-loading",
  error: "my-orders-error",
  empty: "my-orders-empty",
  list: "my-orders-list",
  countShipping: "my-orders-count-shipping",
  countArrived: "my-orders-count-arrived",
  countCanceled: "my-orders-count-canceled",
};

// Maps the real order.status values to the 3 UI tab groups.
const STATUS_GROUPS = {
  pending: "shipping",
  confirmed: "shipping",
  processing: "shipping",
  shipped: "shipping",
  delivered: "arrived",
  cancelled: "canceled",
};

const TAB_LABELS = {
  shipping: "On Deliver",
  arrived: "Delivered",
  canceled: "Canceled",
};

export default class MyOrders {

  constructor() {

    this.dom = new Dom(ELEMENTS);
    this.order = new Order();
    this.orders = [];
    this.activeGroup = "shipping";

  }

  async initialize() {

    this.bindTabs();

    try {

      const response = await this.order.list();

      if (!response.success) {
        this.showError(response.message ?? "Failed to load your orders.");
        return;
      }

      this.orders = Array.isArray(response.data) ? response.data : [];

      this.dom.loading.hidden = true;

      this.renderCounts();
      this.renderList();

    } catch (error) {
      console.error(error);
      this.showError("Failed to load your orders.");
    }

  }

  bindTabs() {

    document.querySelectorAll("[data-status-group]").forEach((tab) => {

      tab.addEventListener("click", () => {

        document.querySelectorAll("[data-status-group]").forEach((el) => {
          el.classList.remove("is-active");
        });

        tab.classList.add("is-active");

        this.activeGroup = tab.dataset.statusGroup;

        this.renderList();

      });

    });

  }

  groupFor(status) {

    return STATUS_GROUPS[status] ?? "shipping";

  }

  renderCounts() {

    const counts = { shipping: 0, arrived: 0, canceled: 0 };

    for (const order of this.orders) {
      const group = this.groupFor(order.status);
      counts[group] = (counts[group] ?? 0) + 1;
    }

    this.dom.countShipping.textContent = counts.shipping || "";
    this.dom.countArrived.textContent = counts.arrived || "";
    this.dom.countCanceled.textContent = counts.canceled || "";

  }

  renderList() {

    const filtered = this.orders.filter(
      (order) => this.groupFor(order.status) === this.activeGroup,
    );

    if (filtered.length === 0) {
      this.dom.list.innerHTML = "";
      this.dom.empty.hidden = false;
      return;
    }

    this.dom.empty.hidden = true;

    this.dom.list.innerHTML = filtered
      .map((order) => this.orderCard(order))
      .join("");

  }

  orderCard(order) {

    const items = Array.isArray(order.items) ? order.items : [];

    const displayItems = items.map((item) => ({
      ...item,
      product_name: item.product_name ?? "Product no longer available",
      image_path: item.image_path
        ?? "data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBTAA7",
    }));

    const group = this.groupFor(order.status);

    return `
      <div class="my-orders__card">

        <div class="my-orders__card-header">
          <div>
            <div class="my-orders__card-id">
              <i class="icon_bag_alt"></i>
              #${order.id}
            </div>
            <div class="my-orders__card-date">
              Placed on ${this.formatDate(order.created_at)}
            </div>
          </div>
          <span class="my-orders__card-status is-${group}">
            ${TAB_LABELS[group]}
          </span>
        </div>

        <div class="checkout__order__products">
          ${CheckoutTemplate.orderItems(displayItems)}
        </div>

        <div class="my-orders__card-footer">
          <div class="my-orders__card-total">
            Total: <span>${Money.format(order.total_price)}</span>
          </div>
          <a href="/order-success.php?id=${order.id}" class="site-btn">Details</a>
        </div>

      </div>
    `;

  }

  formatDate(rawDate) {

    const date = new Date(rawDate);

    if (Number.isNaN(date.getTime())) {
      return rawDate;
    }

    return date.toLocaleDateString(undefined, {
      year: "numeric",
      month: "long",
      day: "numeric",
    });

  }

  showError(message) {

    this.dom.loading.hidden = true;
    this.dom.error.hidden = false;
    this.dom.error.textContent = message;

  }

}

document.addEventListener(
  "DOMContentLoaded",
  () => {
    const page = new MyOrders();
    page.initialize();
  },
);
