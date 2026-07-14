import Ajax from "../../core/Ajax.js";
import Renderer from "../../core/Renderer.js";
import ProductTemplate from "../../templates/ProductTemplate.js";

class MyProducts {

    constructor() {

        this.template = new ProductTemplate();

        this.tbody = document.querySelector(
            "#productsTable tbody"
        );

        this.filters = {
            page: 1,
            per_page: 10,
            search: "",
            sort_by: "created_at",
            sort_direction: "DESC",
            status: "",
            category_id: ""
        };
    }

    async init() {

        await this.loadProducts();

        this.bindEvents();
    }

    async loadProducts() {

        try {

            Renderer.showLoading(this.tbody);

            const params =
                new URLSearchParams(this.filters);

            const response =
                await Ajax.get(
                    "/admin/api/vendor/?" +
                    params.toString()
                );

            Renderer.replace(
                this.tbody,
                this.template.table(
                    response.data.products
                )
            );

            feather.replace();

        } catch (error) {

            Renderer.showError(
                this.tbody,
                error.message
            );

            console.error(error);
        }
    }

    bindEvents() {

        this.bindSearch();

        this.bindPagination();

        this.bindActions();
    }

    bindSearch() {

        const search =
            document.querySelector("#search");

        if (!search) {
            return;
        }

        search.addEventListener(
            "input",
            async e => {

                this.filters.search =
                    e.target.value;

                this.filters.page = 1;

                await this.loadProducts();

            }
        );
    }

    bindPagination() {

        document.addEventListener(
            "click",
            async e => {

                const button =
                    e.target.closest("[data-page]");

                if (!button) {
                    return;
                }

                this.filters.page =
                    Number(button.dataset.page);

                await this.loadProducts();

            }
        );
    }

    bindActions() {

        document.addEventListener(
            "click",
            e => {

                const button =
                    e.target.closest("[data-action]");

                if (!button) {
                    return;
                }

                const id =
                    Number(button.dataset.id);

                switch (button.dataset.action) {

                    case "edit":

                        location.href =
                            `/admin/products/edit-product.php?id=${id}`;

                        break;

                    case "delete":

                        console.log(id);

                        break;

                }

            }
        );
    }

}

new MyProducts().init();