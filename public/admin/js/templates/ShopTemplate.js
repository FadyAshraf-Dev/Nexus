import Template from "../core/Template.js";

export default class ShopTemplate extends Template {

    product(product) {

        return `
            <div class="col-lg-4 col-md-6 col-sm-6">

                <div class="product__item ${product.is_on_sale ? "sale" : ""}">

<div
                        class="product__item__pic set-bg"
                        data-setbg="${this.escape(product.image_path)}">

                        <a
                            href="shop-details.php?slug=${encodeURIComponent(product.slug)}"
                            class="product__image-link">
                        </a>

                        ${
                            product.old_price !== null
                                ? `<span class="label">Sale</span>`
                                : ""
                        }

                    </div>
                    <div class="product__item__text">

                        <h6>${this.escape(product.product_name)}</h6>

                        <a
                            href="#"
                            class="add-cart"
                            data-product-id="${product.id}"
                        >
                            + Add To Cart
                        </a>

                        <h5>
                            $${this.currency(product.display_price)}
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
        `;
    }

    products(products) {

        return products
            .map(product => this.product(product))
            .join("");
    }

    pagination(pagination) {

        if (pagination.total_pages <= 1) {
            return "";
        }

        let html = "";

        for (
            let page = 1;
            page <= pagination.total_pages;
            page++
        ) {

            html += `
                <a
                    href="#"
                    class="${page === pagination.current_page ? "active" : ""}"
                    data-page="${page}"
                >
                    ${page}
                </a>
            `;
        }

        return html;
    }

}