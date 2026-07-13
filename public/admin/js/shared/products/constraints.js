/* =====================================================
   Dynamic Constraints
===================================================== */

function initializeDynamicConstraints(app) {

    const { dom } = app;

    const costPrice = dom.costPrice;
    const sellingPrice = dom.sellingPrice;

    const stockQuantity = dom.stockQuantity;
    const lowStock = dom.lowStock;

    if (costPrice && sellingPrice) {

        function updateSellingPrice() {

            sellingPrice.min = costPrice.value || 0;
            

        }

        costPrice.addEventListener("input", updateSellingPrice);

        updateSellingPrice();

    }

    if (stockQuantity && lowStock) {

        function updateLowStock() {

            lowStock.max = stockQuantity.value || "";
            

        }

        stockQuantity.addEventListener("input", updateLowStock);

        updateLowStock();

    }

}