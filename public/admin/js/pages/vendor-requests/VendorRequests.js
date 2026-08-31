import Ajax from "../../core/Ajax.js";
import Renderer from "../../core/Renderer.js";

import VendorRequestsTemplate from "../../templates/VendorRequestsTemplate.js";

class VendorRequests {

    constructor() {

        this.template = new VendorRequestsTemplate();

        this.tbody = document.querySelector("#vendorRequestsTableBody");

        this.statusFilter = document.querySelector("#statusFilter");

        this.rejectModalEl = document.querySelector("#rejectModal");

        this.rejectModal = this.rejectModalEl
            ? bootstrap.Modal.getOrCreateInstance(this.rejectModalEl)
            : null;

        this.rejectForm = document.querySelector("#rejectForm");

        this.rejectReasonInput = document.querySelector("#rejectReason");

        this.rejectReasonError = document.querySelector("#rejectReasonError");

        // Which request the open reject modal is currently for -
        // set by bindActions() when "reject" is clicked, read by
        // the modal's own submit handler.
        this.pendingRejectId = null;

    }

    async initialize() {

        this.bindEvents();

        await this.refresh();

    }

    async refresh() {

        try {

            Renderer.showLoading(this.tbody);

            const status = this.statusFilter?.value ?? "";

            const query = status
                ? `?status=${encodeURIComponent(status)}`
                : "";

            const response = await Ajax.get(
                `/admin/api/vendor-request/index.php${query}`
            );

            if (!response.success) {
                Renderer.showError(this.tbody, response.message);
                return;
            }

            Renderer.replace(
                this.tbody,
                this.template.table(response.data)
            );

            feather.replace();

        } catch (error) {

            Renderer.showError(this.tbody, error.message);

            console.error(error);

        }

    }

    bindEvents() {

        this.bindFilter();

        this.bindActions();

        this.bindRejectForm();

    }

    bindFilter() {

        this.statusFilter?.addEventListener(
            "change",
            () => this.refresh()
        );

    }

    bindActions() {

        document.addEventListener("click", async (event) => {

            const button = event.target.closest("[data-action]");

            if (!button) {
                return;
            }

            const id = Number(button.dataset.id);

            switch (button.dataset.action) {

                case "approve":
                    await this.approve(id);

                    break;

                case "reject":
                    this.openRejectModal(id);

                    break;

            }

        });

    }

    openRejectModal(id) {

        this.pendingRejectId = id;

        this.rejectReasonInput.value = "";

        this.rejectReasonError.textContent = "";

        this.rejectModal?.show();

    }

    bindRejectForm() {

        this.rejectForm?.addEventListener("submit", async (event) => {

            event.preventDefault();

            this.rejectReasonError.textContent = "";

            await this.reject(
                this.pendingRejectId,
                this.rejectReasonInput.value
            );

        });

    }

    async approve(requestId) {

        try {

            const response = await Ajax.post(
                "/admin/api/vendor-request/approve.php",
                { id: requestId }
            );

            if (!response.success) {
                alert(response.message ?? "Action failed.");
                return;
            }

            await this.refresh();

        } catch (error) {

            console.error(error);

            alert(error.message ?? "Action failed.");

        }

    }

    async reject(requestId, reason) {

        try {

            const response = await Ajax.post(
                "/admin/api/vendor-request/reject.php",
                { id: requestId, reason }
            );

            if (!response.success) {

                if (response.errors?.reason) {
                    this.rejectReasonError.textContent = response.errors.reason;
                } else {
                    alert(response.message ?? "Action failed.");
                }

                return;

            }

            this.rejectModal?.hide();

            await this.refresh();

        } catch (error) {

            console.error(error);

            alert(error.message ?? "Action failed.");

        }

    }

}

document.addEventListener(

    "DOMContentLoaded",

    () => {

        const page = new VendorRequests();

        page.initialize();

    }

);
