import Dom from "../../core/Dom.js";
import Money from "../../core/Money.js";
import Renderer from "../../core/Renderer.js";
import Order from "../../order/Order.js";
import CheckoutTemplate from "../checkout/CheckoutTemplate.js";

const ELEMENTS = {
  loading: "order-success-loading",
  error: "order-success-error",
  intro: "order-success-intro",
  content: "order-success-content",
  orderId: "order-success-id",
  orderDate: "order-success-date",
  orderStatus: "order-success-status",
  items: "order-success-items",
  address: "order-success-address",
  phone: "order-success-phone",
  subtotal: "order-success-subtotal",
  shipping: "order-success-shipping",
  discountRow: "order-success-discount-row",
  discount: "order-success-discount",
  total: "order-success-total",
};

export default class OrderSuccess {

  constructor() {

    this.dom = new Dom(ELEMENTS);
    this.order = new Order();

  }

  async initialize() {

    const params = new URLSearchParams(window.location.search);
    const orderId = params.get("id");
    const isFresh = params.get("fresh") === "1";

    if (!orderId) {
      this.showError("No order was specified.");
      return;
    }

    try {

      const response = await this.order.find(orderId);

      if (!response.success) {
        this.showError(response.message ?? "Order not found.");
        return;
      }

      this.render(response.data, isFresh);

    } catch (error) {
      console.error(error);
      this.showError("Failed to load order details.");
    }

  }

  render(order, isFresh) {

    this.dom.loading.hidden = true;

    if (isFresh) {
      this.dom.intro.hidden = false;
    }

    const items = Array.isArray(order.items) ? order.items : [];

    // Item rows may have a null product_name/image_path if the product
    // was later deleted - fall back gracefully rather than showing
    // "null" in the UI.
    const displayItems = items.map((item) => ({
      ...item,
      product_name: item.product_name ?? "Product no longer available",
      // No placeholder image asset exists in this project yet - fall
      // back to a blank 1x1 transparent pixel rather than a broken
      // image icon. Replace with a real placeholder asset if one gets
      // added later (e.g. /img/placeholder.png).
      image_path: item.image_path
        ?? "data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBTAA7",
    }));

    this.dom.orderId.textContent = `#${order.id}`;
    this.dom.orderDate.textContent = this.formatDate(order.created_at);
    this.dom.orderStatus.textContent = this.formatStatus(order.status);

    Renderer.replace(
      this.dom.items,
      CheckoutTemplate.orderItems(displayItems),
    );

    this.dom.address.textContent = order.address;
    this.dom.phone.textContent = order.phone;

    const subtotal = this.resolveSubtotal(order, items);
    const shipping = Money.toNumber(order.shipping_price);
    const discount = Money.toNumber(order.discount_amount);

    this.dom.subtotal.textContent = Money.format(subtotal);
    this.dom.shipping.textContent = Money.format(shipping);
    this.dom.total.textContent = Money.format(order.total_price);

    if (discount > 0) {
      this.dom.discountRow.hidden = false;
      this.dom.discount.textContent = `-${Money.format(discount)}`;
    }

    this.dom.content.hidden = false;

  }

  // orders table doesn't store a subtotal column directly - derive it
  // from the line items so the summary panel stays consistent even if
  // total_price/shipping_price/discount_amount are the only authoritative
  // numbers on the order itself.
  resolveSubtotal(order, items) {

    return items.reduce((sum, item) => {
      return sum + Money.toNumber(item.price) * Money.toNumber(item.quantity);
    }, 0);

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

  formatStatus(status) {

    if (!status) {
      return "";
    }

    return status.charAt(0).toUpperCase() + status.slice(1);

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
    const page = new OrderSuccess();
    page.initialize();
  },
);
