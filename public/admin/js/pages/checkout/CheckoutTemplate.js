export default class CheckoutTemplate {
  static orderItem(product) {
    return `
            <div class="checkout__order__item">

                <div class="checkout__order__image">

                    <img
                        src="${product.image_path}"
                        alt="${product.product_name}"
                    >

                    <span class="checkout__order__quantity">

                        ${product.quantity}

                    </span>

                </div>

                <div class="checkout__order__content">

                    <h6 class="checkout__order__name">

                        ${product.product_name}

                    </h6>

                    <p class="checkout__order__category">

                        ${product.category_name ?? product.brand ?? ""}

                    </p>

                </div>

                <div class="checkout__order__price">

                    $${(
                      Number(product.price ?? product.selling_price) *
                      Number(product.quantity)
                    ).toFixed(2)}

                </div>

            </div>
        `;
  }
  static orderItems(products) {
    return products.map(this.orderItem).join("");
  }
}
