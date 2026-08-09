import Ajax from "../../core/Ajax.js";
import Renderer from "../../core/Renderer.js";

import UserManagementTemplate from "../../templates/UserManagementTemplate.js";
import PaginationTemplate from "../../templates/PaginationTemplate.js";

class UserManagement {
  constructor() {
    this.userTemplate = new UserManagementTemplate();

    this.paginationTemplate = new PaginationTemplate();

    this.tbody = document.querySelector("#usersTableBody");

    this.pagination = document.querySelector("#paginationContainer");

    this.filters = {
      page: 1,

      per_page: 10,

      role_id: "",

      search: "",
    };
    this.previousRequest = "";

    this.searchDebounce = null;
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
      const response = await Ajax.get("/admin/api/user/?" + query);

      const { users, pagination } = response.data;

      Renderer.replace(this.tbody, this.userTemplate.table(users));

      Renderer.replace(
        this.pagination,
        this.paginationTemplate.render(pagination, "users"),
      );

      feather.replace();
    } catch (error) {
      Renderer.showError(this.tbody, error.message);

      Renderer.clear(this.pagination);

      console.error(error);
    }
  }

  bindEvents() {
    this.bindFilters();

    this.bindPagination();

    this.bindActions();
  }

  bindFilters() {
    const role = document.querySelector("#roleFilter");

    role?.addEventListener("change", async (event) => {
      this.filters.role_id = event.target.value;

      this.filters.page = 1;

      await this.refresh();
    });

    const search = document.querySelector("#searchFilter");

    search?.addEventListener("input", (event) => {
      clearTimeout(this.searchDebounce);

      this.searchDebounce = setTimeout(async () => {
        this.filters.search = event.target.value;

        this.filters.page = 1;

        await this.refresh();
      }, 350);
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
    document.addEventListener("click", async (event) => {
      const button = event.target.closest("[data-action]");

      if (!button) {
        return;
      }

      const id = Number(button.dataset.id);

      switch (button.dataset.action) {
        case "delete":
          await this.deleteUser(id);

          break;
      }
    });
  }

  async deleteUser(userId) {
    try {
      const response = await Ajax.post("/admin/api/user/delete.php", {
        id: userId,
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
    const page = new UserManagement();

    page.initialize();
  },
);