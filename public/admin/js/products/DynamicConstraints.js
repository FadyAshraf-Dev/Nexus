export default class DynamicConstraints {

    constructor(dom) {

        this.costPrice = dom.costPrice;

        this.sellingPrice = dom.sellingPrice;

        this.stockQuantity = dom.stockQuantity;

        this.lowStock = dom.lowStock;

    }

    initialize() {

        this.initializeSellingPrice();

        this.initializeLowStock();

    }

    initializeSellingPrice() {

        if (!this.costPrice || !this.sellingPrice) {
            return;
        }

        this.costPrice.addEventListener(
            "input",
            () => this.updateSellingPrice()
        );

        this.updateSellingPrice();

    }

    initializeLowStock() {

        if (!this.stockQuantity || !this.lowStock) {
            return;
        }

        this.stockQuantity.addEventListener(
            "input",
            () => this.updateLowStock()
        );

        this.updateLowStock();

    }

    updateSellingPrice() {

        this.sellingPrice.min =
            this.costPrice.value || 0;

    }

    updateLowStock() {

        this.lowStock.max =
            this.stockQuantity.value || "";

    }

}