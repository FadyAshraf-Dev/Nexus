/* =====================================================
   Discount Toggle
===================================================== */

function initializeDiscountToggle(app) {
  const { dom } = app;

  const discountType = dom.discountType;
  const discountValue = dom.discountValue;
  const sellingPrice = dom.sellingPrice;

  if (!discountType || !discountValue || !sellingPrice) {
    return;
  }

  function updateDiscountField() {
    const type = discountType.value;
    const hasDiscount = type !== "";

    discountValue.disabled = !hasDiscount;
    discountValue.required = hasDiscount;

    
    if (!hasDiscount) {
      discountValue.value = "";
      discountValue.removeAttribute("max");

      
      return;
    }

    if (type === "fixed") {
      discountValue.max = sellingPrice.value || 0;

      
    } else {
      discountValue.max = 100;

      
    }
  }

  discountType.addEventListener("change", updateDiscountField);

  sellingPrice.addEventListener("input", function () {
    if (discountType.value === "fixed") {
      updateDiscountField();
    }
  });

  updateDiscountField();
}
