import Ajax from "../../core/Ajax.js";
import Renderer from "../../core/Renderer.js";

import CategoryManagementTemplate from "../../templates/CategoryManagementTemplate.js";
import PaginationTemplate from "../../templates/PaginationTemplate.js";

class CategoryManagement {
  constructor() {
    this.categoryTemplate = new CategoryManagementTemplate();

    this.paginationTemplate = new PaginationTemplate();

    this.tbody = document.querySelector("#categoriesTableBody");

    this.pagination = document.querySelector("#paginationContainer");

    this.filters = {
      page: 1,

      per_page: 10,
    };
    this.previousRequest = "";
  }

  async initialize() {
    this.bindEvents();

    await this.refresh();
  }

  async refresh(force = false) {
    try {
      const query = new URLSearchParams(this.filters).toString();
      if (query === this.previousRequest && !force) {
        return;
      }
      Renderer.showLoading(this.tbody);

      this.previousRequest = query;
      const response = await Ajax.get("/admin/api/category/?" + query);

      const { categories, pagination } = response.data;

      Renderer.replace(this.tbody, this.categoryTemplate.table(categories));

      Renderer.replace(
        this.pagination,
        this.paginationTemplate.render(pagination, "categories"),
      );

      feather.replace();
    } catch (error) {
      Renderer.showError(this.tbody, error.message);

      Renderer.clear(this.pagination);

      console.error(error);
    }
  }

  bindEvents() {
    this.bindPagination();

    this.bindActions();
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
    document.addEventListener("click", async (event) => {
      const button = event.target.closest("[data-action]");

      if (!button) {
        return;
      }

      const id = Number(button.dataset.id);

      switch (button.dataset.action) {
        case "delete":
          await this.deleteCategory(id);

          break;
      }
    });
  }

  async deleteCategory(categoryId) {
    try {
      const response = await Ajax.post("/admin/api/category/delete.php", {
        id: categoryId,
      });

      if (!response.success) {
        alert(response.message ?? "Action failed.");
        return;
      }

      await this.refresh(true);
    } catch (error) {
      console.error(error);
      alert(error.message ?? "Action failed.");
    }
  }
}

document.addEventListener(
  "DOMContentLoaded",

  () => {
    const page = new CategoryManagement();

    page.initialize();
  },
);