import Ajax from "../../core/Ajax.js";
import Navigation from "../../core/Navigation.js";

export default class CheckoutSubmission {

    constructor(dom, renderer) {

        this.dom = dom;
        this.renderer = renderer;

    }

    buildPayload(couponCode) {

        return {
            address: this.dom.address.value.trim(),
            phone: this.dom.contactPhone.value.trim(),
            payment_method: this.dom.paymentMethod?.value ?? "cod",
            // Server re-validates this against the authoritative subtotal
            // computed at submit time - it does not trust the discount
            // the client displayed earlier.
            coupon_code: couponCode ?? null,
        };

    }

    async submit(couponCode) {

        const response = await Ajax.post(
            "/api/shop/checkout.php",
            this.buildPayload(couponCode),
        );

        if (!response.success) {
            this.renderer.showSubmitError(
                response.message ?? "Order could not be placed.",
            );
            return false;
        }

        Navigation.redirect(`/order-success.php?id=${response.order_id}&fresh=1`);
        return true;

    }

}
