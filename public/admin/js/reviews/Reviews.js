import Ajax from "../core/Ajax.js";
import Renderer from "../core/Renderer.js";
import ReviewTemplate from "../templates/ReviewTemplate.js";

export default class Reviews {

    constructor(productId) {

        this.productId = productId;
        this.template = new ReviewTemplate();

        this.ratingSummary = document.querySelector(".product__details__text .rating");
        this.reviewsTabLabel = document.querySelector('a[href="#tabs-reviews"]');
        this.reviewsPane = document.getElementById("tabs-reviews");

    }

    async initialize() {

        if (!this.reviewsPane) {
            return;
        }

        try {

            const response = await Ajax.get(
                `/api/review/index.php?product_id=${this.productId}`,
            );

            if (!response.success) {
                return;
            }

            this.render(response.data);

        } catch (error) {
            console.error(error);
        }

    }

    render({ reviews, viewer }) {

        this.renderSummary(reviews);
        this.renderList(reviews);
        this.renderForm(viewer);

    }

    renderSummary(reviews) {

        if (!this.ratingSummary) {
            return;
        }

        const count = reviews.length;
        const avg = count > 0
            ? reviews.reduce((sum, r) => sum + Number(r.rate), 0) / count
            : 0;

        Renderer.replace(this.ratingSummary, this.template.summary(avg, count));

        if (this.reviewsTabLabel) {
            this.reviewsTabLabel.textContent = `Customer Reviews (${count})`;
        }

    }

    renderList(reviews) {

        const listContainer = this.reviewsPane.querySelector(
            ".product__details__tab__content",
        );

        if (!listContainer) {
            return;
        }

        listContainer.innerHTML = `
            <div class="review__section">
                <div id="review-list" class="review__list">${this.template.reviewList(reviews)}</div>
                <div id="review-form-container"></div>
            </div>
        `;

    }

    renderForm(viewer) {

        const container = document.getElementById("review-form-container");

        if (!container) {
            return;
        }

        if (viewer.own_review) {
            Renderer.replace(container, this.template.form(viewer.own_review));
            this.bindForm(true, viewer.own_review.id);
            return;
        }

        if (viewer.can_review) {
            Renderer.replace(container, this.template.form(null));
            this.bindForm(false, null);
            return;
        }

        // Not logged in, or logged in but not eligible (no delivered
        // order for this product, or already reviewed and own_review
        // somehow wasn't returned) - no form, just the reviews list.
        container.innerHTML = "";

    }

    bindForm(isEditing, reviewId) {

        const form = document.getElementById("review-form");

        if (!form) {
            return;
        }

        this.bindStarInput();

        form.addEventListener("submit", (event) => {
            event.preventDefault();
            this.submitForm(form, isEditing, reviewId);
        });

        const deleteButton = document.getElementById("review-delete");

        deleteButton?.addEventListener("click", () => {
            this.deleteReview(reviewId);
        });

    }

    bindStarInput() {

        const container = document.getElementById("review-star-input");
        const hiddenInput = document.getElementById("review-rate");

        if (!container || !hiddenInput) {
            return;
        }

        const halves = [...container.querySelectorAll(".review__star-half")];

        const paint = (rate) => {

            container.querySelectorAll(".review__star").forEach((starEl) => {

                const star = Number(starEl.dataset.star);
                const fillLevel = Math.max(0, Math.min(1, rate / 2 - (star - 1)));
                const icon = starEl.querySelector("i");

                icon.className = "fa " + (
                    fillLevel >= 1 ? "fa-star"
                    : fillLevel >= 0.5 ? "fa-star-half-o"
                    : "fa-star-o"
                );

            });

        };

        let committedRate = Number(hiddenInput.value);

        halves.forEach((half) => {

            half.addEventListener("mouseenter", () => {
                paint(Number(half.dataset.rate));
            });

            half.addEventListener("click", () => {
                committedRate = Number(half.dataset.rate);
                hiddenInput.value = committedRate;
                container.dataset.value = committedRate;
                container.setAttribute("aria-valuenow", committedRate);
                paint(committedRate);
            });

        });

        container.addEventListener("mouseleave", () => {
            paint(committedRate);
        });

        // Keyboard support: left/right arrows adjust by half a star,
        // since the widget has role="slider" for accessibility.
        container.addEventListener("keydown", (event) => {

            if (event.key !== "ArrowRight" && event.key !== "ArrowLeft") {
                return;
            }

            event.preventDefault();

            const delta = event.key === "ArrowRight" ? 1 : -1;
            committedRate = Math.max(1, Math.min(10, committedRate + delta));

            hiddenInput.value = committedRate;
            container.dataset.value = committedRate;
            container.setAttribute("aria-valuenow", committedRate);
            paint(committedRate);

        });

    }

    async submitForm(form, isEditing, reviewId) {

        const errorBox = document.getElementById("review-form-error");
        const rate = form.querySelector("#review-rate").value;
        const content = form.querySelector("#review-content").value.trim();

        errorBox.hidden = true;

        try {

            const response = isEditing
                ? await Ajax.post("/api/review/update.php", {
                    id: reviewId,
                    rate,
                    content,
                })
                : await Ajax.post("/api/review/submit.php", {
                    product_id: this.productId,
                    rate,
                    content,
                });

            if (!response.success) {
                this.showFormError(response.message ?? "Could not save review.");
                return;
            }

            await this.initialize();

        } catch (error) {
            this.showFormError(error.message ?? "Could not save review.");
        }

    }

    async deleteReview(reviewId) {

        if (!confirm("Delete your review?")) {
            return;
        }

        try {

            const response = await Ajax.post("/api/review/delete.php", {
                id: reviewId,
            });

            if (!response.success) {
                alert(response.message ?? "Could not delete review.");
                return;
            }

            await this.initialize();

        } catch (error) {
            console.error(error);
        }

    }

    showFormError(message) {

        const errorBox = document.getElementById("review-form-error");

        if (!errorBox) {
            return;
        }

        errorBox.textContent = message;
        errorBox.hidden = false;

    }

}