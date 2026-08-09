import Ajax from "../../core/Ajax.js";
import Renderer from "../../core/Renderer.js";

import OrderManagementTemplate from "../../templates/OrderManagementTemplate.js";
import PaginationTemplate from "../../templates/PaginationTemplate.js";

class OrderManagement {
  constructor() {
    this.orderTemplate = new OrderManagementTemplate();

    this.paginationTemplate = new PaginationTemplate();

    this.tbody = document.querySelector("#ordersTableBody");

    this.pagination = document.querySelector("#paginationContainer");

    this.filters = {
      page: 1,

      per_page: 10,

      status: "",
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
      const response = await Ajax.get("/admin/api/order/?" + query);

      const { orders, pagination } = response.data;

      Renderer.replace(this.tbody, this.orderTemplate.table(orders));

      Renderer.replace(
        this.pagination,
        this.paginationTemplate.render(pagination, "orders"),
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

    this.bindStatusChange();
  }

  bindFilters() {
    const status = document.querySelector("#statusFilter");

    status?.addEventListener("change", async (event) => {
      // Only the toolbar's filter dropdown is bound directly here - the
      // per-row status-change selects also fire "change" but are
      // handled separately via delegation in bindStatusChange, since
      // they don't exist yet at bind time (they're rendered later).
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

  bindStatusChange() {
    document.addEventListener("change", async (event) => {
      const select = event.target.closest('[data-action="update-status"]');

      if (!select) {
        return;
      }

      const orderId = Number(select.dataset.id);
      const newStatus = select.value;

      select.disabled = true;

      try {
        const response = await Ajax.post(
          "/admin/api/order/update-status.php",
          {
            id: orderId,
            status: newStatus,
          },
        );

        if (!response.success) {
          alert(response.message ?? "Could not update order status.");
        }

        await this.refresh(true);
      } catch (error) {
        console.error(error);
        alert(error.message ?? "Could not update order status.");
        await this.refresh();
      } finally {
        select.disabled = false;
      }
    });
  }
}

document.addEventListener(
  "DOMContentLoaded",

  () => {
    const page = new OrderManagement();

    page.initialize();
  },
);