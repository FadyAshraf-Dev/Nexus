import Ajax from "../../core/Ajax.js";
import Renderer from "../../core/Renderer.js";

import ProductTemplate from "../../templates/ProductTemplate.js";
import PaginationTemplate from "../../templates/PaginationTemplate.js";

class MyProducts {
  constructor() {
    this.productTemplate = new ProductTemplate();

    this.paginationTemplate = new PaginationTemplate();

    this.tbody = document.querySelector("#productsTableBody");

    this.pagination = document.querySelector("#paginationContainer");

    this.filters = {
      page: 1,

      per_page: 5,

      search: "",

      sort_by: "created_at",

      sort_direction: "DESC",

      status: "",

      category_id: "",
    };
  }

  async initialize() {
    this.bindEvents();

    await this.refresh();
  }

  async refresh() {
    try {
      Renderer.showLoading(this.tbody);

      const params = new URLSearchParams(this.filters);

      const response = await Ajax.get(
        "/admin/api/vendor/?" + params.toString(),
      );

      const { products, pagination } = response.data;

      Renderer.replace(this.tbody, this.productTemplate.table(products));

      Renderer.replace(
        this.pagination,
        this.paginationTemplate.render(pagination),
      );

      feather.replace();
    } catch (error) {
      Renderer.showError(this.tbody, error.message);

      Renderer.clear(this.pagination);

      console.error(error);
    }
  }

  bindEvents() {
    this.bindSearch();

    this.bindPagination();

    this.bindActions();
  }

  bindSearch() {
    const search = document.querySelector("#search");

    if (!search) {
      return;
    }

    search.addEventListener("input", async (event) => {
      this.filters.search = event.target.value;

      this.filters.page = 1;

      await this.refresh();
    });
  }

  bindPagination() {
    document.addEventListener("click", async (event) => {
      const button = event.target.closest("[data-page]");

      if (!button) {
        return;
      }

      const page = Number(button.dataset.page);

      if (Number.isNaN(page) || page === this.filters.page) {
        return;
      }

      this.filters.page = page;

      await this.refresh();
    });
  }

  bindActions() {
    document.addEventListener("click", (event) => {
      const button = event.target.closest("[data-action]");

      if (!button) {
        return;
      }

      const id = Number(button.dataset.id);

      switch (button.dataset.action) {
        case "edit":
          location.href = `/admin/products/edit-product.php?id=${id}`;

          break;

        case "delete":
          console.log("Delete", id);

          break;
      }
    });
  }
}

document.addEventListener(
  "DOMContentLoaded",

  () => {
    const page = new MyProducts();

    page.initialize();
  },
);
