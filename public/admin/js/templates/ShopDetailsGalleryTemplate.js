export default class ShopDetailsGalleryTemplate {

    render(images) {

        const thumbnails = images.map((image, index) => `

            <li class="nav-item">

                <a
                    class="nav-link ${index === 0 ? "active" : ""}"
                    data-toggle="tab"
                    href="#gallery-${index}"
                    role="tab">

                    <div
                        class="product__thumb__pic set-bg"
                        data-setbg="${image.image_path}">
                    </div>

                </a>

            </li>

        `).join("");

        const preview = images.map((image, index) => `

            <div
                class="tab-pane ${index === 0 ? "active" : ""}"
                id="gallery-${index}"
                role="tabpanel">

                <div class="product__details__pic__item">

                    <img src="${image.image_path}" alt="">

                </div>

            </div>

        `).join("");

        return {

            thumbnails,

            preview,

        };

    }

}