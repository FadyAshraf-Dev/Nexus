import Template from "../core/Template.js";

export default class ReviewTemplate extends Template {

    stars(rate) {

        // rate is 1-10 internally; display on a 5-star visual scale
        // with half-star precision (FA 4.7.0 syntax - this project
        // does not have FA6's fa-solid prefix loaded).
        const starsOutOf5 = rate / 2;
        const fullStars = Math.floor(starsOutOf5);
        const hasHalfStar = starsOutOf5 - fullStars >= 0.5;
        const emptyStars = 5 - fullStars - (hasHalfStar ? 1 : 0);

        let html = "";

        for (let i = 0; i < fullStars; i++) {
            html += `<i class="fa fa-star"></i>`;
        }

        if (hasHalfStar) {
            html += `<i class="fa fa-star-half-o"></i>`;
        }

        for (let i = 0; i < emptyStars; i++) {
            html += `<i class="fa fa-star-o"></i>`;
        }

        return html;

    }

    summary(avgRating, reviewCount) {

        return `
            ${this.stars(avgRating)}
            <span> - ${reviewCount} Review${reviewCount === 1 ? "" : "s"}</span>
        `;

    }

    reviewItem(review) {

        return `
            <div class="review__item" data-review-id="${review.id}">
                <div class="review__item__rating">
                    ${this.stars(review.rate)}
                </div>
                <div class="review__item__author">
                    ${this.escape(review.reviewer_name)}
                    <span class="review__item__date">
                        ${this.formatDate(review.created_at)}
                    </span>
                </div>
                ${review.content
                    ? `<p class="review__item__content">${this.escape(review.content)}</p>`
                    : ""
                }
            </div>
        `;

    }

    reviewList(reviews) {

        if (reviews.length === 0) {
            return `<p class="review__empty">No reviews yet. Be the first to review this product.</p>`;
        }

        return reviews.map((review) => this.reviewItem(review)).join("");

    }

    // The submission/edit form - same markup serves both, caller fills
    // in existing values when editing.
    form(existingReview = null) {

        const rate = existingReview?.rate ?? 10;
        const content = existingReview?.content ?? "";
        const submitLabel = existingReview ? "Update review" : "Submit review";

        return `
            <form id="review-form" class="review__form">
                <h6>${submitLabel === "Update review" ? "Edit your review" : "Write a review"}</h6>
                <div class="review__form__rating">
                    <label>Your rating</label>
                    <div
                        id="review-star-input"
                        class="review__star-input"
                        data-value="${rate}"
                        role="slider"
                        aria-label="Rating out of 5 stars"
                        aria-valuemin="1"
                        aria-valuemax="10"
                        aria-valuenow="${rate}"
                        tabindex="0"
                    >
                        ${this.starInputIcons(rate)}
                    </div>
                    <input type="hidden" id="review-rate" name="rate" value="${rate}" required>
                </div>
                <div class="review__form__content">
                    <label for="review-content">Your review (optional)</label>
                    <textarea
                        id="review-content"
                        name="content"
                        maxlength="1000"
                        rows="4"
                    >${this.escape(content)}</textarea>
                </div>
                <div id="review-form-error" class="review__form__error" hidden></div>
                <button type="submit" class="site-btn">${submitLabel}</button>
                ${existingReview
                    ? `<button type="button" id="review-delete" class="review__delete-btn">Delete review</button>`
                    : ""
                }
            </form>
        `;

    }

    // Renders 5 star icons, each split into a left half (value = star
    // index - 0.5, i.e. rate points X*2-1) and right half (value =
    // star index, i.e. rate points X*2), so a 5-star visual maps onto
    // the full 1-10 rate scale. This markup is shared between the
    // static display (stars()) and this interactive input - kept
    // separate because the input needs per-half click targets and
    // data attributes that the read-only display doesn't.
    starInputIcons(rate) {

        const starsOutOf5 = rate / 2;

        let html = "";

        for (let star = 1; star <= 5; star++) {

            const fillLevel = Math.max(0, Math.min(1, starsOutOf5 - (star - 1)));

            let iconClass = "fa-star-o";
            if (fillLevel >= 1) {
                iconClass = "fa-star";
            } else if (fillLevel >= 0.5) {
                iconClass = "fa-star-half-o";
            }

            html += `
                <span class="review__star" data-star="${star}">
                    <i class="fa ${iconClass}"></i>
                    <span class="review__star-half review__star-half--left" data-rate="${star * 2 - 1}"></span>
                    <span class="review__star-half review__star-half--right" data-rate="${star * 2}"></span>
                </span>
            `;

        }

        return html;

    }

    formatDate(rawDate) {

        const date = new Date(rawDate);

        if (Number.isNaN(date.getTime())) {
            return "";
        }

        return date.toLocaleDateString(undefined, {
            year: "numeric",
            month: "long",
            day: "numeric",
        });

    }

}