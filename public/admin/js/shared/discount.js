/* =====================================================
   Discount Toggle
===================================================== */

function initializeDiscountToggle(dom) {

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

            if (
                discountValue.value &&
                Number(discountValue.value) > Number(sellingPrice.value)
            ) {
                discountValue.value = sellingPrice.value;
            }

        }
        else {

            discountValue.max = 100;

            if (
                discountValue.value &&
                Number(discountValue.value) > 100
            ) {
                discountValue.value = 100;
            }

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
