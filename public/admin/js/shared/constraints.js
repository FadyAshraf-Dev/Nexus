/* =====================================================
   Dynamic Constraints
===================================================== */

function initializeDynamicConstraints(dom) {

    const costPrice = dom.costPrice;
    const sellingPrice = dom.sellingPrice;

    const stockQuantity = dom.stockQuantity;
    const lowStock = dom.lowStock;

    if (costPrice && sellingPrice) {

        function updateSellingPrice() {

            sellingPrice.min = costPrice.value || 0;

            if (
                costPrice.value &&
                sellingPrice.value &&
                Number(sellingPrice.value) < Number(costPrice.value)
            ) {
                sellingPrice.value = costPrice.value;
            }

        }

        costPrice.addEventListener("input", updateSellingPrice);

        updateSellingPrice();

    }

    if (stockQuantity && lowStock) {

        function updateLowStock() {

            lowStock.max = stockQuantity.value || "";

            if (
                stockQuantity.value &&
                lowStock.value &&
                Number(lowStock.value) > Number(stockQuantity.value)
            ) {
                lowStock.value = stockQuantity.value;
            }

        }

        stockQuantity.addEventListener("input", updateLowStock);

        updateLowStock();

    }

}
