import Template from "../core/Template.js";

export default class VendorRequestsTemplate extends Template {

    table(requests) {

        if (!Array.isArray(requests) || requests.length === 0) {
            return `
                <tr>
                    <td colspan="6" class="text-center py-5 text-muted">
                        No vendor requests found.
                    </td>
                </tr>
            `;
        }

        return requests.map(request => this.row(request)).join("");

    }

    row(request) {

        return `
            <tr>
                <td>${this.escape(request.store_name)}</td>
                <td>
                    <div>${this.escape(request.username)}</div>
                    <div class="text-muted small">${this.escape(request.email)}</div>
                </td>
                <td>${this.escape(request.phone)}</td>
                <td>${this.formatDate(request.created_at)}</td>
                <td>${this.statusBadge(request.status)}</td>
                <td class="text-end">${this.actions(request)}</td>
            </tr>
        `;

    }

    statusBadge(status) {

        const classes = {
            pending: "bg-warning",
            approved: "bg-success",
            rejected: "bg-danger",
        };

        return this.badge(
            status.charAt(0).toUpperCase() + status.slice(1),
            classes[status] ?? "bg-secondary"
        );

    }

    actions(request) {

        if (request.status !== "pending") {
            return `<span class="text-muted small">Reviewed</span>`;
        }

        return `
            <div class="d-inline-flex">

                ${this.button({
                    icon: "check",
                    action: "approve",
                    id: request.id,
                    title: "Approve",
                    className: "btn-datatable btn-icon btn-transparent-dark me-2",
                })}

                ${this.button({
                    icon: "x",
                    action: "reject",
                    id: request.id,
                    title: "Reject",
                    className: "btn-datatable btn-icon btn-transparent-dark me-2",
                })}

            </div>
        `;

    }

    formatDate(rawDate) {

        const date = new Date(rawDate);

        if (Number.isNaN(date.getTime())) {
            return "";
        }

        return date.toLocaleDateString(undefined, {
            year: "numeric",
            month: "short",
            day: "numeric",
        });

    }

}
