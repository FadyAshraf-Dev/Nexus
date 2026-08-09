import Template from "../core/Template.js";

const STATUSES = [
  "pending",
  "confirmed",
  "processing",
  "shipped",
  "delivered",
  "cancelled",
];

const STATUS_BADGE_CLASS = {
  pending: "bg-secondary",
  confirmed: "bg-info",
  processing: "bg-primary",
  shipped: "bg-warning",
  delivered: "bg-success",
  cancelled: "bg-danger",
};

export default class OrderManagementTemplate extends Template {

  table(orders) {
    if (!orders.length) {
      return this.emptyState();
    }

    return orders.map((order) => this.row(order)).join("");
  }

  row(order) {
    return `
        <tr>
            <td>#${order.id}</td>

            <td>${this.escape(order.customer_name)}</td>

            <td>$${this.currency(order.total_price)}</td>

            <td>${this.date(order.created_at)}</td>

            <td>${this.badge(this.capitalize(order.status), STATUS_BADGE_CLASS[order.status] ?? "bg-secondary")}</td>

            <td class="text-end">${this.statusSelect(order)}</td>
        </tr>
    `;
  }

  statusSelect(order) {
    const options = STATUSES
      .map((status) => `
                <option value="${status}" ${status === order.status ? "selected" : ""}>
                    ${this.capitalize(status)}
                </option>
            `)
      .join("");

    return `
            <select
                class="form-select form-select-sm order-status-select"
                data-action="update-status"
                data-id="${order.id}"
                style="max-width: 160px; display: inline-block;"
            >
                ${options}
            </select>
        `;
  }

  capitalize(text) {
    if (!text) {
      return "";
    }
    return text.charAt(0).toUpperCase() + text.slice(1);
  }

  date(rawDate) {
    const date = new Date(rawDate);

    if (Number.isNaN(date.getTime())) {
      return "";
    }

    return date.toLocaleDateString(undefined, {
      year: "numeric",
      month: "short",
      day: "numeric",
    });
  }

  emptyState() {
    return `
            <tr>

                <td colspan="6" class="text-center py-5">

                    <i
                        data-feather="shopping-bag"
                        style="width:48px;height:48px;"
                        class="mb-3"
                    ></i>

                    <h5>No orders found.</h5>

                    <p class="text-muted">
                        Orders will appear here once customers start checking out.
                    </p>

                </td>

            </tr>
        `;
  }
}