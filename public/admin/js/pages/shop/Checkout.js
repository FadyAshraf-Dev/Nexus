import Dom from "../../core/Dom.js";

import FormValidation
from "../../forms/FormValidation.js";

import CheckoutWizard
from "../../forms/CheckoutWizard.js";

import CheckoutWizardValidation
from "../../forms/CheckoutWizardValidation.js";

const ELEMENTS = {

    form: "checkout-form",

    previousButton: "checkout-prev",

    nextButton: "checkout-next",

    submitButton: "checkout-submit",

    address: "checkout-address",

    shippingCountry: "shipping-country",

    shippingCity: "shipping-city",

    shippingAddress1: "shipping-address-1",

    shippingAddress2: "shipping-address-2",

    shippingState: "shipping-state",

    shippingZip: "shipping-zip",

    contactPhone: "contact-phone",

    reviewAddress: "checkout-review-address",

    reviewPhone: "checkout-review-phone"

};

class Checkout {

    constructor() {

        this.dom =
            new Dom(ELEMENTS);

        this.validation =
            new FormValidation(this.dom);

        this.wizard =
            new CheckoutWizard(this.dom);

        this.wizardValidation =
            new CheckoutWizardValidation(

                this.dom,

                this.wizard,

                this.validation

            );

    }

    initialize() {

        this.validation.initialize();

        this.wizard.initialize();

        this.wizardValidation.initialize();

    }

}

document.addEventListener(

    "DOMContentLoaded",

    () => {

        new Checkout().initialize();

    }

);