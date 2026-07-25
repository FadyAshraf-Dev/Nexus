export default class ShoppingCartTemplate {
  static money(value) {
    const num = Number(value);
    return Number.isFinite(num) ? num.toFixed(2) : "0.00";
  }

  render(items) {
    if (!Array.isArray(items) || items.length === 0) {
      return "";
    }

    return items
      .map((item) => {
        const productId = Number(item.product_id);
        const quantity = Number(item.quantity) || 0;
        const displayPrice = Number(item.display_price) || 0;
        const unitPrice = Number(item.unit_price);
        const apiLineTotal = Number(item.line_total);
        const computedLineTotal = displayPrice * quantity;

        const lineTotal =
          Number.isFinite(apiLineTotal) && apiLineTotal > 0
            ? apiLineTotal
            : computedLineTotal;

        return `
                    <tr data-product-id="${productId}">

                        <td class="product__cart__item">
                            <div class="product__cart__item__pic">
                                <img src="${item.image_path}" alt="">
                            </div>

                            <div class="product__cart__item__text">
                                <h6>${item.product_name}</h6>
                                <h5>$${ShoppingCartTemplate.money(unitPrice)}</h5>
                            </div>
                        </td>

                        <td class="quantity__item">
                            <div class="quantity">
                                <div class="pro-qty-2">
                                    <span class="fa fa-angle-left dec qtybtn" role="button" data-product-id="${productId}"></span>
                                    <input
                                        type="text"
                                        value="${quantity}"
                                        class="cart-quantity"
                                        data-product-id="${productId}"
                                        readonly
                                    >
                                    <span class="fa fa-angle-right inc qtybtn" role="button" data-product-id="${productId}"></span>
                                </div>
                            </div>
                        </td>

                        <td class="cart__price">
                            $ ${ShoppingCartTemplate.money(lineTotal)}
                        </td>

                        <td class="cart__close">
                            <i
                                class="fa fa-close remove-cart-item"
                                role="button"
                                data-product-id="${productId}"
                            ></i>
                        </td>

                    </tr>
                `;
      })
      .join("");
  }
}
