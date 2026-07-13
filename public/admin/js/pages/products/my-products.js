class MyProducts {

    static #tableBody = document.getElementById("productsTableBody");

    static async init() {

        try {

            Renderer.showLoading(this.#tableBody);

            const response = await Ajax.get("api/vendor");

            console.log(response);

            if (!response.success) {
                Renderer.showError(
                    this.#tableBody,
                    response.message ?? "Unable to load products."
                );

                return;
            }

            const products = response.data.products ?? [];
            
            

            if (products.length === 0) {
                Renderer.showEmpty(
                    this.#tableBody,
                    "No products found."
                );

                return;
            }

            let html = "";
            console.log(products);
            for (const product of products) {

                html += `
                    <tr>

                        <td>
                            <img
                                src="${product.image_path}"
                                class="rounded"
                                width="60"
                                height="60"
                                alt="${product.product_name}">
                        </td>

                        <td>${product.product_name}</td>

                        <td>${product.category_name}</td>

                        <td>${product.selling_price}</td>

                        <td>${product.stock_quantity}</td>

                        <td>
                            <span class="badge bg-success">
                                ${product.status}
                            </span>
                        </td>

                        <td>${product.discount ?? "-"}</td>

                        <td class="text-end">

                            <button
                                class="btn btn-datatable btn-icon btn-transparent-dark me-2"
                                title="Edit">

                                <i data-feather="edit"></i>

                            </button>

                            <button
                                class="btn btn-datatable btn-icon btn-transparent-dark"
                                title="Delete">

                                <i data-feather="trash-2"></i>

                            </button>

                        </td>

                    </tr>
                `;

            }

            Renderer.replace(
                this.#tableBody,
                html
            );

            feather.replace();

        }
        catch (error) {

            console.error(error);

            Renderer.showError(
                this.#tableBody,
                "An unexpected error occurred."
            );

        }

    }

}

document.addEventListener(
    "DOMContentLoaded",
    () => MyProducts.init()
);