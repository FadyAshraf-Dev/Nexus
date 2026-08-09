import Template from "../core/Template.js";

export default class ProductManagementTemplate extends Template {

  table(products) {
    if (!products.length) {
      return this.emptyState();
    }

    return products.map((product) => this.row(product)).join("");
  }

  row(product) {
    return `
        <tr>
            <td>
                ${this.image(
                    product.image_path,
                    product.product_name
                )}
            </td>

            <td>${this.escape(product.product_name)}</td>

            <td>${this.escape(product.category_name)}</td>

            <td>${this.price(product)}</td>

            <td>${product.stock_quantity}</td>

            <td>${this.status(product.status)}</td>

            <td>${this.discount(product)}</td>

            <td class="text-end">${this.actions(product)}</td>
        </tr>
    `;
  }

  actions(product) {
    return `
            <div class="d-inline-flex">

                ${this.button({
                  icon: "trash-2",
                  action: "delete",
                  id: product.id,
                  title: "Delete",
                  className: "btn-datatable btn-icon btn-transparent-dark me-2",
                })}

            </div>
        `;
  }

  price(product) {
    return `
        <strong>
            $${this.currency(product.selling_price)}
        </strong>
    `;
  }

  discount(product) {
    if (!product.discount_value) {
      return "—";
    }

    return `
        <span class="text-success">
            ${this.escape(product.discount_type)}:
            ${product.discount_value}
        </span>
    `;
  }

  status(status) {
    return this.badge(
      this.capitalize(status),
      status === "active" ? "bg-success" : "bg-secondary",
    );
  }

  capitalize(text) {
    if (!text) {
      return "";
    }
    return text.charAt(0).toUpperCase() + text.slice(1);
  }

  emptyState() {
    return `
            <tr>

                <td colspan="8" class="text-center py-5">

                    <i
                        data-feather="package"
                        style="width:48px;height:48px;"
                        class="mb-3"
                    ></i>

                    <h5>No products found.</h5>

                    <p class="text-muted">
                        Products will appear here once vendors start listing them.
                    </p>

                </td>

            </tr>
        `;
  }
}