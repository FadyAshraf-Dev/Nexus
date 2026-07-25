import Cart from "./Cart.js";

class CartBadge {
    constructor() {
        this.cart = new Cart();

        this.badges = document.querySelectorAll("#cart-count");
    }

    async refresh() {
        try {
            const response = await this.cart.count();

            const payload = response.data ?? {};

            const count = Number(
                payload.item_count ??
                payload.cart_count ??
                payload.count ??
                0
            );

            this.render(count);

        } catch (error) {
            console.error(error);
            this.render(0);
        }
    }

    render(count) {
        this.badges.forEach((badge) => {
            badge.textContent = String(count);
            badge.hidden = count === 0;
        });
    }
}

const cartBadge = new CartBadge();

cartBadge.refresh();

export default cartBadge;