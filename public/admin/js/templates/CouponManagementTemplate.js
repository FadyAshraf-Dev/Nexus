import Template from "../core/Template.js";

export default class CouponManagementTemplate extends Template {

  table(coupons) {
    if (!coupons.length) {
      return this.emptyState();
    }

    return coupons.map((coupon) => this.row(coupon)).join("");
  }

  row(coupon) {
    return `
        <tr>
            <td><code>${this.escape(coupon.code)}</code></td>

            <td>${this.type(coupon)}</td>

            <td>${this.value(coupon)}</td>

            <td>${this.usage(coupon)}</td>

            <td>${this.window(coupon)}</td>

            <td>${this.status(coupon.status)}</td>

            <td class="text-end">${this.actions(coupon)}</td>
        </tr>
    `;
  }

  type(coupon) {
    return coupon.type === "percentage"
      ? this.badge("Percentage", "bg-info")
      : this.badge("Fixed", "bg-primary");
  }

  value(coupon) {
    return coupon.type === "percentage"
      ? `${Number(coupon.value)}%`
      : `$${this.currency(coupon.value)}`;
  }

  usage(coupon) {
    const limit = coupon.usage_limit ?? "∞";
    return `${coupon.used_count} / ${limit}`;
  }

  window(coupon) {
    const starts = coupon.starts_at ? this.date(coupon.starts_at) : "—";
    const expires = coupon.expires_at ? this.date(coupon.expires_at) : "—";
    return `${starts} → ${expires}`;
  }

  date(rawDate) {
    const date = new Date(rawDate);

    if (Number.isNaN(date.getTime())) {
      return "—";
    }

    return date.toLocaleDateString(undefined, {
      year: "numeric",
      month: "short",
      day: "numeric",
    });
  }

  status(status) {
    return status === "active"
      ? this.badge("Active", "bg-success")
      : this.badge("Inactive", "bg-secondary");
  }

  actions(coupon) {
    const isActive = coupon.status === "active";

    return `
            <div class="d-inline-flex">

                ${this.button({
                  icon: isActive ? "toggle-right" : "toggle-left",
                  action: "toggle-status",
                  id: coupon.id,
                  title: isActive ? "Deactivate" : "Activate",
                  className: "btn-datatable btn-icon btn-transparent-dark me-2",
                })}

                ${this.button({
                  icon: "trash-2",
                  action: "delete",
                  id: coupon.id,
                  title: "Delete coupon",
                  className: "btn-datatable btn-icon btn-transparent-dark",
                })}

            </div>
        `;
  }

  emptyState() {
    return `
            <tr>

                <td colspan="7" class="text-center py-5">

                    <i
                        data-feather="tag"
                        style="width:48px;height:48px;"
                        class="mb-3"
                    ></i>

                    <h5>No coupons found.</h5>

                    <p class="text-muted">
                        Create a coupon to offer discounts to your customers.
                    </p>

                </td>

            </tr>
        `;
  }
}