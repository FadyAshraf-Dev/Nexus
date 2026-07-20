import Template from "../core/Template.js";

export default class ShopTemplate extends Template {

    product(product) {

        return `
            <div class="col-lg-4 col-md-6 col-sm-6">

                <div class="product__item ${product.is_on_sale ? "sale" : ""}">

                    <div
                        class="product__item__pic set-bg"
                        style="background-image:url('${this.escape(product.image_path)}')"
                    >

                        ${product.is_on_sale
                            ? `<span class="label">Sale</span>`
                            : ""}

                        <ul class="product__hover">

                            <li>
                                <a href="#">
                                    <img src="img/icon/heart.png" alt="">
                                </a>
                            </li>

                            <li>
                                <a href="#">
                                    <img src="img/icon/compare.png" alt="">
                                    <span>Compare</span>
                                </a>
                            </li>

                            <li>
                                <a href="shop-details.php?slug=${encodeURIComponent(product.slug)}">
                                    <img src="img/icon/search.png" alt="">
                                </a>
                            </li>

                        </ul>

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