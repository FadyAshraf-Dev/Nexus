import Ajax from "../../core/Ajax.js";
import Renderer from "../../core/Renderer.js";
import Cart from "../../cart/Cart.js";
import ShopDetailsGalleryTemplate from "../../templates/ShopDetailsGalleryTemplate.js";

import RelatedProductsTemplate from "../../templates/RelatedProductsTemplate.js";

export default class ShopDetails {
  constructor() {
    this.cart = new Cart();
    this.quantityInput = document.getElementById("quantity");
    console.log(this.quantityInput);

    this.addToCartButton = document.getElementById("add-to-cart");
    console.log(this.addToCartButton);

    this.cartCount = document.getElementById("cart-count");
    console.log(this.cartCount);
    this.galleryTemplate = new ShopDetailsGalleryTemplate();

    this.relatedTemplate = new RelatedProductsTemplate();

    this.slug = new URLSearchParams(window.location.search).get("slug");

    this.galleryNav = document.getElementById("product-gallery-nav");

    this.galleryPreview = document.getElementById("product-gallery-preview");

    this.relatedProducts = document.getElementById("related-products");

    this.productName = document.getElementById("product-name");
    this.shortDescription = document.getElementById(
      "product-short-description",
    );

    this.fullDescription = document.getElementById("product-full-description");

    this.priceContainer = document.getElementById("product-price-container");

    this.categoryRow = document.getElementById("product-category-row");

    this.brandRow = document.getElementById("product-brand-row");
  }

  async init() {
    if (!this.slug) {
      alert("Missing product.");

      return;
    }
    const response = await this.cart.count();

    this.updateCartBadge(response.data.cart_count);
    await this.loadProduct();
  }

  async loadProduct() {
    try {
      const response = await Ajax.get(
        `/api/product/shop-details.php?slug=${encodeURIComponent(this.slug)}`,
      );

      const product = response.data;

      this.product = product;
      this.renderGallery(product.images);

      this.renderProduct(product);

      this.renderRelatedProducts(product.related_products);

      $(".set-bg").each(function () {
        $(this).css("background-image", `url(${$(this).data("setbg")})`);
      });
      this.registerEvents();
    } catch (error) {
      console.error(error);
    }
  }
  updateCartBadge(count) {
    this.cartCount.textContent = count;

    this.cartCount.hidden = count === 0;
  }
  async addToCart() {
    try {
      const quantity = parseInt(this.quantityInput.value, 10) || 1;

      const response = await this.cart.add(this.product.id, quantity);
      this.updateCartBadge(response.data.cart_count);

      // TODO:
      // Show toast:
      // "Added to cart."
    } catch (error) {
      console.error(error);
    }
  }
  registerEvents() {
    this.addToCartButton?.addEventListener("click", () => this.addToCart());
  }
  renderGallery(images) {
    const gallery = this.galleryTemplate.render(images);

    Renderer.replace(this.galleryNav, gallery.thumbnails);

    Renderer.replace(this.galleryPreview, gallery.preview);
  }

  renderRelatedProducts(products) {
    this.relatedProducts.innerHTML = this.relatedTemplate.render(products);
  }
  renderProduct(product) {
    document.title = product.product_name;

    this.productName.textContent = product.product_name;

    this.shortDescription.textContent = product.short_description;

    this.fullDescription.textContent = product.full_description;

    this.categoryRow.append(
      document.createTextNode(" " + product.category_name),
    );

    this.brandRow.append(document.createTextNode(" " + product.brand));

    this.priceContainer.firstChild.textContent = `$${product.display_price}`;
    if (product.old_price !== null) {
      this.priceContainer.insertAdjacentHTML(
        "beforeend",
        `<span id="product-old-price">$${product.old_price}</span>`,
      );
    }
  }
}
const shopDetails = new ShopDetails();

shopDetails.init();
