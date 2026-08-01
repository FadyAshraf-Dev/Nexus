import Renderer from "../../core/Renderer.js";
import CheckoutTemplate from "./CheckoutTemplate.js";
import Money from "../../core/Money.js";

export default class CheckoutRenderer {

    constructor(dom) {

        this.dom = dom;

    }

    renderOrder(items) {

        const products = items.map((item) => ({
            image_path: item.image_path ?? "",
            product_name: item.product_name,
            category_name: item.category_name,
            brand: item.brand,
            quantity: item.quantity,
            unit_price: item.unit_price,
            price: item.unit_price ?? item.price ?? item.selling_price,
        }));

        Renderer.replace(
            this.dom.overviewList,
            CheckoutTemplate.orderItems(products),
        );

    }

    renderTotals({ subtotal, shipping, discount, total }) {

        this.dom.subtotal.textContent = Money.format(subtotal);
        this.dom.shipping.textContent = Money.format(shipping);
        this.dom.total.textContent = Money.format(total);

        this.renderDiscount(discount);

    }

    renderDiscount(discount) {

        if (!this.dom.discountRow) {
            return;
        }

        const hasDiscount = discount > 0;

        this.dom.discountRow.hidden = !hasDiscount;

        if (hasDiscount && this.dom.discount) {
            this.dom.discount.textContent = `-${Money.format(discount)}`;
        }

    }

    showPromoFeedback(message, isError = false) {

        if (!this.dom.promoFeedback) {
            return;
        }

        this.dom.promoFeedback.textContent = message;
        this.dom.promoFeedback.hidden = false;
        this.dom.promoFeedback.classList.toggle("text-danger", isError);
        this.dom.promoFeedback.classList.toggle("text-success", !isError);

    }

    clearPromoFeedback() {

        if (!this.dom.promoFeedback) {
            return;
        }

        this.dom.promoFeedback.textContent = "";
        this.dom.promoFeedback.hidden = true;

    }

    showSubmitError(message) {

        if (!this.dom.submitErrors) {
            return;
        }

        this.dom.submitErrors.textContent = message;
        this.dom.submitErrors.hidden = false;

    }

    clearSubmitErrors() {

        if (!this.dom.submitErrors) {
            return;
        }

        this.dom.submitErrors.textContent = "";
        this.dom.submitErrors.hidden = true;

    }

    setSubmitting(isSubmitting, currentStep) {

        if (this.dom.submitButton) {
            this.dom.submitButton.disabled = isSubmitting;
        }

        if (this.dom.nextButton) {
            this.dom.nextButton.disabled = isSubmitting;
        }

        if (this.dom.previousButton) {
            this.dom.previousButton.disabled =
                isSubmitting || currentStep === 1;
        }

    }

}
