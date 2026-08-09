import Ajax from "../../core/Ajax.js";
import Renderer from "../../core/Renderer.js";

import ReviewManagementTemplate from "../../templates/ReviewManagementTemplate.js";
import PaginationTemplate from "../../templates/PaginationTemplate.js";

class ReviewManagement {
  constructor() {
    this.reviewTemplate = new ReviewManagementTemplate();

    this.paginationTemplate = new PaginationTemplate();

    this.tbody = document.querySelector("#reviewsTableBody");

    this.pagination = document.querySelector("#paginationContainer");

    this.filters = {
      page: 1,

      per_page: 10,

      search: "",

      min_rate: "",

      status: "",

      sort_by: "created_at",

      sort_direction: "DESC",
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
      const response = await Ajax.get("/admin/api/review/?" + query);

      const { reviews, pagination } = response.data;

      Renderer.replace(this.tbody, this.reviewTemplate.table(reviews));

      Renderer.replace(
        this.pagination,
        this.paginationTemplate.render(pagination, "reviews"),
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

    this.bindFilters();

    this.bindPagination();

    this.bindActions();
  }

  bindSearch() {
    const search = document.querySelector("#search");

    if (!search) {
      return;
    }

    const performSearch = async () => {
      this.filters.search = search.value.trim();

      this.filters.page = 1;

      await this.refresh();
    };

    search.addEventListener(
      "keydown",

      async (event) => {
        if (event.key !== "Enter") {
          return;
        }

        event.preventDefault();

        await performSearch();
      },
    );
    search.addEventListener("input", async (event) => {
      if (event.target.value.trim() !== "") {
        return;
      }

      this.filters.search = "";

      this.filters.page = 1;

      await this.refresh();
    });
  }

  bindFilters() {
    const minRate = document.querySelector("#minRateFilter");
    const status = document.querySelector("#statusFilter");

    minRate?.addEventListener("change", async (event) => {
      this.filters.min_rate = event.target.value;

      this.filters.page = 1;

      await this.refresh();
    });

    status?.addEventListener("change", async (event) => {
      this.filters.status = event.target.value;

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
    document.addEventListener("click", async (event) => {
      const button = event.target.closest("[data-action]");

      if (!button) {
        return;
      }

      const id = Number(button.dataset.id);

      switch (button.dataset.action) {
        case "hide":
          await this.moderate("hide", id);

          break;

        case "unhide":
          await this.moderate("unhide", id);

          break;
      }
    });
  }

  async moderate(action, reviewId) {
    try {
      const response = await Ajax.post(`/admin/api/review/${action}.php`, {
        id: reviewId,
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
    const page = new ReviewManagement();

    page.initialize();
  },
);