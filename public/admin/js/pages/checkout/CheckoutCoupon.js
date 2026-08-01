import Ajax from "../../core/Ajax.js";
import Money from "../../core/Money.js";

export default class CheckoutCoupon {

    constructor() {

        this.code = null;
        this.discount = 0;

    }

    get isApplied() {

        return this.code !== null;

    }

    // subtotal must be the current, authoritative subtotal shown on
    // screen right now - apply.php validates minimum_order against it.
    async apply(code, subtotal) {

        const response = await Ajax.post("/api/coupon/apply.php", {
            code,
            subtotal,
        });

        if (!response.success) {
            this.clear();
            throw new Error(response.message ?? "Invalid promo code.");
        }

        const data = response.data ?? {};

        // Server already resolves fixed vs percentage and applies the
        // maximum_discount cap - never redo that math here.
        this.code = code;
        this.discount = Money.toNumber(data.discount);

        return data;

    }

    clear() {

        this.code = null;
        this.discount = 0;

    }

}