import Template from "../core/Template.js";

export default class ReviewManagementTemplate extends Template {

  table(reviews) {
    if (!reviews.length) {
      return this.emptyState();
    }

    return reviews.map((review) => this.row(review)).join("");
  }

  row(review) {
    return `
        <tr>
            <td>${this.escape(review.reviewer_name)}</td>

            <td>${this.escape(review.product_name)}</td>

            <td>${this.rating(review.rate)}</td>

            <td>${this.truncate(review.content, 60)}</td>

            <td>${this.date(review.created_at)}</td>

            <td>${this.status(review.deleted_at)}</td>

            <td class="text-end">${this.actions(review)}</td>
        </tr>
    `;
  }

  rating(rate) {
    return `<strong>${rate}</strong> / 10`;
  }

  date(rawDate) {
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

  status(deletedAt) {
    return deletedAt
      ? this.badge("Hidden", "bg-secondary")
      : this.badge("Active", "bg-success");
  }

  actions(review) {
    const isHidden = Boolean(review.deleted_at);

    return `
            <div class="d-inline-flex">

                ${this.button({
                  icon: isHidden ? "eye" : "eye-off",
                  action: isHidden ? "unhide" : "hide",
                  id: review.id,
                  title: isHidden ? "Unhide review" : "Hide review",
                  className: "btn-datatable btn-icon btn-transparent-dark me-2",
                })}

            </div>
        `;
  }

  emptyState() {
    return `
            <tr>

                <td colspan="7" class="text-center py-5">

                    <i
                        data-feather="star"
                        style="width:48px;height:48px;"
                        class="mb-3"
                    ></i>

                    <h5>No reviews found.</h5>

                    <p class="text-muted">
                        Reviews will appear here once customers start leaving them.
                    </p>

                </td>

            </tr>
        `;
  }
}