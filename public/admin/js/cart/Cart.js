import Ajax from "../core/Ajax.js";

export default class Cart {

    async getCart() {
        return await Ajax.get("/api/cart/index.php");
    }

    async add(productId, quantity = 1) {
        
        return await Ajax.post("/api/cart/add.php", {
            product_id: productId,
            quantity,
        });
    }

    async update(productId, quantity) {
        return await Ajax.post("/api/cart/update.php", {
            product_id: productId,
            quantity,
        });
    }

    async remove(productId) {
        return await Ajax.post("/api/cart/remove.php", {
            product_id: productId,
        });
    }

    async clear() {
        return await Ajax.post("/api/cart/clear.php");
    }

    async count() {
        return await Ajax.get("/api/cart/count.php");
    }
}