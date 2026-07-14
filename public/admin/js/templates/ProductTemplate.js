import Template from "./Template.js";

export default class ProductTemplate extends Template {
  table(products) {
    if (!products.length) {
      return this.emptyState();
    }

    return products.map((product) => this.row(product)).join("");
  }

  // row(product) {

  //     return `
  //         <tr>

  //             <td>
  //                 ${this.image(
  //                     product.image_path,
  //                     product.product_name
  //                 )}
  //             </td>

  //             <td>
  //                 ${this.escape(product.product_name)}
  //             </td>

  //             <td>
  //                 ${this.badge(
  //                     product.category_name,
  //                     "bg-light text-dark"
  //                 )}
  //             </td>

  //             <td>
  //                 ${this.price(product)}
  //             </td>

  //             <td>
  //                 ${product.stock_quantity}
  //             </td>

  //             <td>
  //                 ${this.status(product.status)}
  //             </td>

  //             <td>
  //                 ${this.actions(product)}
  //             </td>

  //         </tr>
  //     `;
  // }
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

            <td>${this.category(product)}</td>

            <td>${this.price(product)}</td>

            <td>${product.stock_quantity}</td>

            <td>${this.status(product.status)}</td>

            <td>${this.discount(product)}</td>

            <td class="text-end">${this.actions(product)}</td>
        </tr>
    `;
  }
  price(product) {
    return `
        <strong>
            ${product.selling_price}
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
  category(product) {
    return this.escape(product.category_name);
}
  status(status) {
    return this.badge(
      status,
      status === "active" ? "bg-success" : "bg-secondary",
    );
  }

  actions(product) {
    return `
            <div class="d-inline-flex">

                ${this.button({
                  icon: "edit",
                  action: "edit",
                  id: product.id,
                  title: "Edit",
                  className: "btn-datatable btn-icon btn-transparent-dark me-2",
                })}

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
                        Create your first product to get started.
                    </p>

                </td>

            </tr>
        `;
  }
}
