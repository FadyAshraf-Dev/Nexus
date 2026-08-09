import Ajax from "../../core/Ajax.js";
import Renderer from "../../core/Renderer.js";

import CouponManagementTemplate from "../../templates/CouponManagementTemplate.js";
import PaginationTemplate from "../../templates/PaginationTemplate.js";

class CouponManagement {
  constructor() {
    this.couponTemplate = new CouponManagementTemplate();

    this.paginationTemplate = new PaginationTemplate();

    this.tbody = document.querySelector("#couponsTableBody");

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
      const response = await Ajax.get("/admin/api/coupon/?" + query);

      const { coupons, pagination } = response.data;

      Renderer.replace(this.tbody, this.couponTemplate.table(coupons));

      Renderer.replace(
        this.pagination,
        this.paginationTemplate.render(pagination, "coupons"),
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
    const status = document.querySelector("#statusFilter");

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
        case "toggle-status":
          await this.toggleStatus(id);

          break;

        case "delete":
          await this.deleteCoupon(id);

          break;
      }
    });
  }

  async toggleStatus(couponId) {
    try {
      const response = await Ajax.post(
        "/admin/api/coupon/toggle-status.php",
        { id: couponId },
      );

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

  async deleteCoupon(couponId) {
    try {
      const response = await Ajax.post("/admin/api/coupon/delete.php", {
        id: couponId,
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
    const page = new CouponManagement();

    page.initialize();
  },
);