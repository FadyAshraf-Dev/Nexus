import Ajax from "../core/Ajax.js";

export default class Order {

    async find(orderId) {
        return await Ajax.get(`/api/order/show.php?id=${orderId}`);
    }

    async list() {
        return await Ajax.get("/api/order/index.php");
    }

}