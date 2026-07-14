import Dom from "../../core/Dom.js";

import FormValidation from "../../forms/FormValidation.js";
import Wizard from "../../forms/Wizard.js";
import WizardValidation from "../../forms/WizardValidation.js";

import ProductImageGallery from "../../products/ProductImageGallery.js";
import DiscountController from "../../products/DiscountController.js";
import DynamicConstraints from "../../products/DynamicConstraints.js";

const ELEMENTS = {

    form: "addProductForm",

    costPrice: "inputCostPrice",
    sellingPrice: "inputSellingPrice",

    stockQuantity: "inputStockQuantity",
    lowStock: "inputLowStockThreshold",

    discountType: "selectDiscountType",
    discountValue: "inputDiscountValue",

    imageGallery: "inputImageGallery",
    imagePreviewContainer: "imagePreviewContainer"

};

class AddProductPage {

    constructor() {

        this.dom = new Dom(ELEMENTS);
        
        this.validation =
            new FormValidation(this.dom);

        this.wizard =
            new Wizard(this.dom);

        this.constraints =
            new DynamicConstraints(this.dom);

        this.discount =
            new DiscountController(this.dom);

        this.imageGallery =
            new ProductImageGallery(this.dom);

        this.wizardValidation =
            new WizardValidation(
                this.dom,
                this.wizard,
                this.validation
            );

    }

    initialize() {

        this.validation.initialize();

        this.wizard.initialize();

        this.constraints.initialize();

        this.discount.initialize();

        this.imageGallery.initialize();

        this.wizardValidation.initialize();

    }

}

document.addEventListener(

    "DOMContentLoaded",

    () => {

        const page = new AddProductPage();

        page.initialize();

    }

);