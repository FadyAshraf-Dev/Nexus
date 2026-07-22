export default class RelatedProductsTemplate {
  render(products) {
    return products
      .map(
        (product) => `

            <div class="col-lg-3 col-md-6 col-sm-6">

                <div class="product__item">

<div
    class="product__item__pic set-bg"
    data-setbg="${product.image_path}">

    <a
        href="/shop-details.php?slug=${product.slug}"
        class="product__image-link">
    </a>

</div>
                    <div class="product__item__text">

                        <h6>${product.product_name}</h6>

                        <h5>

                            $${product.display_price}

                            ${product.old_price !== null
                                ? `<span style="text-decoration:line-through;color:#999;font-size:14px;margin-left:8px;">
                                    $${product.old_price}
                                   </span>`
                                : ""
                            }

                        </h5>

                        </div>

                </div>

            </div>

        `,
      )
      .join("");
  }
}
