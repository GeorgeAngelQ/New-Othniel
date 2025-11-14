const PaymentsCore = {
    totals() {
        const totalText = document.getElementById("cart-total")?.textContent || "$0.00";
        const total = parseFloat(totalText.replace("$", "")) || 0;
        return { tot: total };
    },

    PENtoUSD(amountPEN) {
        const rate = 3.70; // TEMPORAL
        return (amountPEN / rate).toFixed(2);
    },

    clearCart() {
        localStorage.removeItem("id_cart");
    }
};

window.PaymentsCore = PaymentsCore;
