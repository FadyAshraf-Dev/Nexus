import CheckoutWizard from "../../forms/CheckoutWizard.js";
import CheckoutWizardValidation from "../../forms/CheckoutWizardValidation.js";
import FormValidation from "../../forms/FormValidation.js";
import Cart from "../../cart/Cart.js";
import Dom from "../../core/Dom.js";
import Navigation from "../../core/Navigation.js";
import CheckoutRenderer from "./CheckoutRenderer.js";
import CheckoutTotals from "./CheckoutTotals.js";
import CheckoutCoupon from "./CheckoutCoupon.js";
import CheckoutSubmission from "./CheckoutSubmission.js";

const ELEMENTS = {
  form: "checkout-form",
  address: "checkout-address",
  paymentMethod: "checkout-payment-method",
  previousButton: "checkout-prev",
  nextButton: "checkout-next",
  submitButton: "checkout-submit",
  shippingCountry: "shipping-country",
  shippingCity: "shipping-city",
  shippingAddress1: "shipping-address-1",
  shippingAddress2: "shipping-address-2",
  shippingState: "shipping-state",
  shippingZip: "shipping-zip",
  contactPhone: "contact-phone",
  reviewAddress: "checkout-review-address",
  reviewPhone: "checkout-review-phone",
  overviewList: "checkout-overview-list",
  subtotal: "checkout-subtotal",
  shipping: "checkout-shipping",
  total: "checkout-total",
  discountRow: "checkout-discount-row",
  discount: "checkout-discount",
  promoInput: "checkout-promo-input",
  applyPromo: "checkout-apply-promo",
  promoFeedback: "checkout-promo-feedback",
  submitErrors: "checkout-submit-errors",
};

export default class Checkout {

  constructor() {

    this.dom = new Dom(ELEMENTS);
    this.cart = new Cart();
    this.coupon = new CheckoutCoupon();

    this.renderer = new CheckoutRenderer(this.dom);

    this.validation = new FormValidation(this.dom);
    this.wizard = new CheckoutWizard(this.dom);
    this.wizardValidation = new CheckoutWizardValidation(
      this.dom,
      this.wizard,
      this.validation,
    );

    this.submission = new CheckoutSubmission(this.dom, this.renderer);

    this.items = [];
    this.payload = {};

  }

  async initialize() {

    this.validation.initialize();
    this.wizard.initialize();
    this.wizardValidation.initialize();

    await this.load();

    this.bindSubmit();
    this.bindPromo();

  }

  async load() {

    try {

      const response = await this.cart.getCart();

      if (!response.success) {
        throw new Error(response.message ?? "Failed to load cart.");
      }

      this.payload = response.data ?? {};
      this.items = Array.isArray(this.payload.items) ? this.payload.items : [];

      if (this.items.length === 0) {
        Navigation.redirect("/shopping-cart.php");
        return;
      }

      this.renderer.renderOrder(this.items);
      this.renderTotals();

    } catch (error) {
      console.error(error);
      this.renderer.showSubmitError(error.message ?? "Failed to load checkout.");
    }

  }

  currentSubtotal() {

    return CheckoutTotals.resolveSubtotal(this.payload, this.items);

  }

  renderTotals() {

    const subtotal = this.currentSubtotal();
    const discount = this.coupon.discount;
    const total = CheckoutTotals.resolveTotal(subtotal, discount);

    this.renderer.renderTotals({
      subtotal,
      shipping: CheckoutTotals.SHIPPING_PRICE,
      discount,
      total,
    });

  }

  bindSubmit() {

    this.dom.form.addEventListener("submit", (event) => this.submit(event));

  }

  bindPromo() {

    if (!this.dom.applyPromo) {
      return;
    }

    this.dom.applyPromo.addEventListener("click", (event) => {
      event.preventDefault();
      this.applyPromo();
    });

  }

  async applyPromo() {

    const code = this.dom.promoInput?.value.trim();

    if (!code) {
      this.renderer.showPromoFeedback("Please enter a promo code.", true);
      return;
    }

    try {

      // Send the subtotal currently on screen - apply.php checks
      // minimum_order against this value.
      await this.coupon.apply(code, this.currentSubtotal());
      this.renderer.clearPromoFeedback();
      this.renderer.showPromoFeedback("Promo code applied.");
      this.renderTotals();

    } catch (error) {
      this.coupon.clear();
      this.renderer.showPromoFeedback(error.message, true);
      this.renderTotals();
    }

  }

  async submit(event) {

    event.preventDefault();

    this.validation.start();

    if (!this.validation.validateForm()) {
      return;
    }

    this.wizardValidation.populateReview();
    this.renderer.clearSubmitErrors();
    this.renderer.setSubmitting(true, this.wizard.currentStep);

    try {
      // Server re-validates the coupon against its own authoritative
      // subtotal at submit time - this is just telling it which code
      // the user applied, not trusting our displayed discount.
      const placed = await this.submission.submit(this.coupon.code);

      if (!placed) {
        // Order was rejected (e.g. coupon expired/exhausted between
        // apply and submit, stock changed, etc). Don't leave the promo
        // UI showing a discount that no longer applies.
        this.coupon.clear();
        this.renderer.clearPromoFeedback();
        this.renderTotals();
      }

    } catch (error) {
      console.error(error);
      this.coupon.clear();
      this.renderer.clearPromoFeedback();
      this.renderTotals();
      this.renderer.showSubmitError(error.message ?? "Order could not be placed.");
    } finally {
      this.renderer.setSubmitting(false, this.wizard.currentStep);
    }

  }

}

document.addEventListener(
  "DOMContentLoaded",
  () => {
    const page = new Checkout();
    page.initialize();
  },
);