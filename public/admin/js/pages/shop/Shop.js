import Ajax from "../../core/Ajax.js";
import Renderer from "../../core/Renderer.js";
import ShopTemplate from "../../templates/ShopTemplate.js";

export default class Shop {
  constructor() {
    this.template = new ShopTemplate();

    this.grid = document.getElementById("shop-products");
    this.pagination = document.getElementById("shop-pagination");
    this.summary = document.getElementById("shop-summary");
    this.filters = {
      page: 1,
      per_page: 10,
      search: "",
      category_id: "",
      sort_by: "created_at",
      sort_direction: "DESC",
    };
  }

  async init() {
    this.registerEvents();

    await this.loadProducts();
  }

  async loadProducts() {
    try {
      this.showLoading();

      const query = new URLSearchParams(this.filters);

      const response = await Ajax.get(`/api/product/shop.php?${query}`);

      const { products, pagination } = response.data;

      this.renderProducts(products);

      this.renderPagination(pagination);

      this.renderSummary(pagination);
    } catch (error) {
      console.error(error);

      this.showError(error.message);
    }
  }

  renderProducts(products) {
    if (products.length === 0) {
      this.grid.innerHTML = `
                <div class="col-12 text-center py-5">
                    No products found.
                </div>
            `;

      return;
    }

    Renderer.replace(this.grid, this.template.products(products));
  }

  renderPagination(pagination) {
    Renderer.replace(this.pagination, this.template.pagination(pagination));
  }

  renderSummary(pagination) {
    const from = pagination.offset + 1;

    const to = Math.min(
      pagination.offset + pagination.per_page,
      pagination.total_products,
    );

    this.summary.textContent = `Showing ${from}-${to} of ${pagination.total_products} results`;
  }

  registerEvents() {
    this.pagination.addEventListener("click", async (event) => {
      const page = event.target.dataset.page;

      if (!page) {
        return;
      }

      event.preventDefault();

      this.filters.page = Number(page);

      await this.loadProducts();

      window.scrollTo({
        top: 0,
        behavior: "smooth",
      });
    });
  }

  showLoading() {
    this.grid.innerHTML = `
            <div class="col-12 text-center py-5">
                Loading products...
            </div>
        `;
  }

  showError(message) {
    this.grid.innerHTML = `
            <div class="col-12 text-center py-5 text-danger">
                ${message}
            </div>
        `;
  }
}
