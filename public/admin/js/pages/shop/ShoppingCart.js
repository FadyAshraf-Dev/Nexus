import Cart from "../../cart/Cart.js";
import ShoppingCartTemplate from "../../templates/ShoppingCartTemplate.js";
import cartBadge from "../../cart/CartBadge.js";

export default class ShoppingCart {
  constructor() {
    this.cart = new Cart();
    this.template = new ShoppingCartTemplate();

    this.itemsContainer = document.getElementById("cart-items");
    this.emptyState = document.getElementById("cart-empty");
    this.subtotalElement = document.getElementById("cart-subtotal");
    this.totalElement = document.getElementById("cart-total");
    this.updateButton = document.querySelector(".continue__btn.update__btn a");
  }

  async init() {
    await this.loadCart();
    this.registerEvents();
  }

  async loadCart() {
    try {
      const response = await this.cart.getCart();
      const payload = response.data ?? {};

      const items = Array.isArray(payload.items) ? payload.items : [];

      this.renderItems(items);
      this.renderSummary(payload, items);
      await cartBadge.refresh();
    } catch (error) {
      console.error(error);

      this.renderItems([]);
      this.renderSummary({}, []);
      await cartBadge.refresh();
    }
  }

  renderItems(items) {
    if (!this.itemsContainer) {
      return;
    }

    if (items.length === 0) {
      this.itemsContainer.innerHTML = "";

      if (this.emptyState) {
        this.emptyState.hidden = false;
      }

      return;
    }

    this.itemsContainer.innerHTML = this.template.render(items);

    if (this.emptyState) {
      this.emptyState.hidden = true;
    }

    this.lockQuantityInputs();
  }

  renderSummary(payload, items) {
    const subtotal = this.resolveSubtotal(payload, items);
    const total = this.resolveTotal(payload, subtotal);

    if (this.subtotalElement) {
      this.subtotalElement.textContent = `$${this.formatMoney(subtotal)}`;
    }

    if (this.totalElement) {
      this.totalElement.textContent = `$${this.formatMoney(total)}`;
    }
  }

  registerEvents() {
    if (this.itemsContainer) {
      this.itemsContainer.addEventListener("click", async (event) => {
        const qtyButton = event.target.closest(".qtybtn");
        const removeButton = event.target.closest(".remove-cart-item");

        if (qtyButton) {
          event.preventDefault();
          await this.handleQuantityClick(qtyButton);
          return;
        }

        if (removeButton) {
          event.preventDefault();
          await this.handleRemoveClick(removeButton);
        }
      });
    }

    this.updateButton?.addEventListener("click", async (event) => {
      event.preventDefault();
      await this.loadCart();
    });
  }

  async handleQuantityClick(button) {
    const row = button.closest("tr[data-product-id]");

    if (!row) {
      return;
    }

    const input = row.querySelector(".cart-quantity");

    if (!input) {
      return;
    }

    const productId = Number.parseInt(
      input.dataset.productId || row.dataset.productId,
      10,
    );

    if (!Number.isInteger(productId) || productId < 1) {
      return;
    }

    const currentQuantity = Number.parseInt(input.value, 10) || 1;
    const isIncrement = button.classList.contains("inc");

    button.style.pointerEvents = "none";

    try {
      if (isIncrement) {
        await this.cart.update(productId, currentQuantity + 1);
      } else {
        if (currentQuantity === 1) {
          await this.cart.remove(productId);
        } else {
          await this.cart.update(productId, currentQuantity - 1);
        }
      }

      await this.loadCart();
    } catch (error) {
      console.error(error);
      await this.loadCart();
    } finally {
      button.style.pointerEvents = "";
    }
  }
  async handleRemoveClick(button) {
    const productId = Number(button.dataset.productId);

    if (!productId) {
      return;
    }

    button.style.pointerEvents = "none";

    try {
      const response = await this.cart.remove(productId);

      if (!response.success) {
        throw new Error(response.message);
      }

      await this.loadCart();
    } catch (error) {
      console.error(error);
    } finally {
      button.style.pointerEvents = "";
    }
  }
  lockQuantityInputs() {
    if (!this.itemsContainer) {
      return;
    }

    this.itemsContainer.querySelectorAll(".cart-quantity").forEach((input) => {
      input.readOnly = true;
    });
  }

  resolveCartCount(payload, items) {
    const directCount =
      payload.item_count ?? payload.cart_count ?? payload.count;

    if (directCount !== undefined && directCount !== null) {
      return Number(directCount) || 0;
    }

    return items.reduce((sum, item) => {
      return sum + (Number(item.quantity) || 0);
    }, 0);
  }

  resolveSubtotal(payload, items) {
    const directSubtotal = payload.subtotal ?? payload.cart_subtotal;

    if (directSubtotal !== undefined && directSubtotal !== null) {
      return Number(directSubtotal) || 0;
    }

    return items.reduce((sum, item) => {
      const unitPrice = Number(item.display_price ?? item.price ?? 0);

      const quantity = Number(item.quantity) || 0;

      const lineTotal = Number(item.line_total);

      if (Number.isFinite(lineTotal) && lineTotal > 0) {
        return sum + lineTotal;
      }

      return sum + unitPrice * quantity;
    }, 0);
  }

  resolveTotal(payload, subtotal) {
    const directTotal = payload.total ?? payload.cart_total;

    if (directTotal !== undefined && directTotal !== null) {
      return Number(directTotal) || 0;
    }

    return Number(subtotal) || 0;
  }

  formatMoney(value) {
    return Number(value || 0).toFixed(2);
  }
}

const shoppingCart = new ShoppingCart();
shoppingCart.init();
