export default class RelatedProductsTemplate {

    render(products) {

        return products.map(product => `

            <div class="col-lg-3 col-md-6 col-sm-6">

                <div class="product__item">

                    <div
                        class="product__item__pic set-bg"
                        data-setbg="${product.image_path}">

                        <ul class="product__hover">

                            <li>

                                <a href="#">

                                    <img src="/img/icon/heart.png" alt="">

                                </a>

                            </li>

                            <li>

                                <a href="#">

                                    <img src="/img/icon/compare.png" alt="">

                                    <span>Compare</span>

                                </a>

                            </li>

                            <li>

                                <a href="/shop-details.php?slug=${product.slug}">

                                    <img src="/img/icon/search.png" alt="">

                                </a>

                            </li>

                        </ul>

                    </div>

                    <div class="product__item__text">

                        <h6>${product.product_name}</h6>

                        <a href="#" class="add-cart">

                            + Add To Cart

                        </a>

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

        `).join("");

    }

}