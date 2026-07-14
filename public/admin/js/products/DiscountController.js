export default class DiscountController {

    constructor(dom) {

        this.dom = dom;

    }

    initialize() {

        if (!this.isAvailable()) {
            return;
        }

        this.bindEvents();

        this.update();

    }

    bindEvents() {

        this.dom.discountType.addEventListener(
            "change",
            () => this.update()
        );

        this.dom.sellingPrice.addEventListener(
            "input",
            () => {

                if (this.isFixed()) {
                    this.update();
                }

            }
        );

    }

    update() {

        const hasDiscount = this.hasDiscount();

        this.dom.discountValue.disabled = !hasDiscount;
        this.dom.discountValue.required = hasDiscount;

        if (!hasDiscount) {

            this.reset();

            return;

        }

        this.updateMaximum();

    }

    updateMaximum() {

        if (this.isFixed()) {

            this.dom.discountValue.max =
                this.dom.sellingPrice.value || 0;

            return;

        }

        this.dom.discountValue.max = 100;

    }

    reset() {

        this.dom.discountValue.value = "";

        this.dom.discountValue.removeAttribute("max");

    }

    hasDiscount() {

        return this.dom.discountType.value !== "";

    }

    isFixed() {

        return this.dom.discountType.value === "fixed";

    }

    isAvailable() {

        return (
            this.dom.discountType &&
            this.dom.discountValue &&
            this.dom.sellingPrice
        );

    }

}