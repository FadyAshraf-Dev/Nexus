import Template from "../core/Template.js";

export default class PaginationTemplate extends Template {

    static WINDOW_SIZE = 5;

    render(pagination) {

        if (pagination.total_pages <= 1) {
            return "";
        }

        return `
            <div class="d-flex justify-content-between align-items-center">

                <small class="text-muted">
                    ${this.summary(pagination)}
                </small>

                <nav aria-label="Products pagination">

                    <ul class="pagination pagination-sm mb-0">


                        ${this.previous(pagination)}

                        ${this.pages(pagination)}

                        ${this.next(pagination)}


                    </ul>

                </nav>

            </div>
        `;

    }

    summary(pagination) {

        const start =
            pagination.total_products === 0
                ? 0
                : pagination.offset + 1;

        const end =
            Math.min(
                pagination.offset + pagination.per_page,
                pagination.total_products
            );

        return `
            Showing
            <strong>${start}</strong>
            –
            <strong>${end}</strong>
            of
            <strong>${pagination.total_products}</strong>
            products
        `;

    }

    first(pagination) {

        return this.button(
            1,
            "&laquo;",
            !pagination.has_previous
        );

    }

    previous(pagination) {

        return this.button(
            pagination.current_page - 1,
            "&lsaquo;",
            !pagination.has_previous
        );

    }

    next(pagination) {

        return this.button(
            pagination.current_page + 1,
            "&rsaquo;",
            !pagination.has_next
        );

    }

    last(pagination) {

        return this.button(
            pagination.total_pages,
            "&raquo;",
            !pagination.has_next
        );

    }

    pages(pagination) {

        const html = [];

        const total = pagination.total_pages;

        const current = pagination.current_page;

        const window = PaginationTemplate.WINDOW_SIZE;

        let start =
            Math.max(
                1,
                current - Math.floor(window / 2)
            );

        let end =
            Math.min(
                total,
                start + window - 1
            );

        start =
            Math.max(
                1,
                end - window + 1
            );

        if (start > 1) {

            html.push(
                this.page(1)
            );

            if (start > 2) {

                html.push(
                    this.ellipsis()
                );

            }

        }

        for (
            let page = start;
            page <= end;
            page++
        ) {

            html.push(
                this.page(
                    page,
                    page === current
                )
            );

        }

        if (end < total) {

            if (end < total - 1) {

                html.push(
                    this.ellipsis()
                );

            }

            html.push(
                this.page(total)
            );

        }

        return html.join("");

    }

    page(number, active = false) {

        return `
            <li class="page-item ${active ? "active" : ""}">

                <button
                    type="button"
                    class="page-link"
                    data-page="${number}"
                >
                    ${number}
                </button>

            </li>
        `;

    }

    button(page, icon, disabled = false) {

        return `
            <li class="page-item ${disabled ? "disabled" : ""}">

                <button
                    type="button"
                    class="page-link"
                    data-page="${page}"
                    ${disabled ? "disabled" : ""}
                >
                    ${icon}
                </button>

            </li>
        `;

    }

    ellipsis() {

        return `
            <li class="page-item disabled">

                <span class="page-link">
                    ...
                </span>

            </li>
        `;

    }

}