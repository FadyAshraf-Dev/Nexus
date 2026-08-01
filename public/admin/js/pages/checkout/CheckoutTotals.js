import Money from "../../core/Money.js";

const SHIPPING_PRICE = 50;

export default class CheckoutTotals {

    static SHIPPING_PRICE = SHIPPING_PRICE;

    static resolveSubtotal(payload, items) {

        const directSubtotal = payload.subtotal ?? payload.cart_subtotal;

        if (directSubtotal !== undefined && directSubtotal !== null) {
            return Money.toNumber(directSubtotal);
        }

        return items.reduce((sum, item) => {

            const lineTotal = Money.toNumber(item.line_total);

            if (lineTotal > 0) {
                return sum + lineTotal;
            }

            const unitPrice = Money.toNumber(
                item.unit_price ?? item.price ?? item.selling_price ?? 0,
            );
            const quantity = Money.toNumber(item.quantity);

            return sum + unitPrice * quantity;

        }, 0);

    }

    // discount is a flat amount, already resolved by CheckoutCoupon
    static resolveTotal(subtotal, discount = 0) {

        const total = subtotal + SHIPPING_PRICE - discount;

        return total < 0 ? 0 : total;

    }

}
