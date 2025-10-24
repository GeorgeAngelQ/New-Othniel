@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto bg-white rounded-xl shadow-md p-6 mt-6">
    <h1 class="text-2xl font-bold mb-6 text-center text-[#4a3b2f]">🛒 Mi Carrito</h1>

    <!-- Contenedor de productos -->
    <div id="cart-items" class="divide-y divide-[#d9c8b6] mb-6">
        <p class="text-center text-[#6b5846]">Cargando carrito...</p>
    </div>

    <!-- Total -->
    <div class="flex justify-between items-center p-4 border-t border-[#d9c8b6] bg-[#f9f5f2] rounded-lg">
        <span class="font-semibold text-lg text-[#4a3b2f]">Total:</span>
        <span id="cart-total" class="text-xl font-bold text-[#a67c52]">$0.00</span>
    </div>

    <div class="flex flex-col items-center justify-center mt-8">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Escoge tu método de pago</h2>

        <div class="flex flex-wrap justify-center gap-6">
            <button
                onclick="redirectToMercadoPago()"
                class="w-70 h-36 bg-white rounded-2xl shadow-md hover:shadow-lg flex items-center justify-center transition duration-200">
                <img src="/storage/assets/MercadoPago.png" alt="MercadoPago" class="w-48 h-auto">
            </button>
            <button
                onclick="redirectToPayPal()"
                class="w-70 h-36 bg-white rounded-2xl shadow-md hover:shadow-lg flex items-center justify-center transition duration-200">
                <img src="/storage/assets/PayPal.png" alt="PayPal" class="w-48 h-auto">
            </button>
            <button
                onclick="redirectToCoinGate()"
                class="w-70 h-36 bg-white rounded-2xl shadow-md hover:shadow-lg flex items-center justify-center transition duration-200">
                <img src="/storage/assets/Coingate.png" alt="CoinGate" class="w-48 h-auto">
            </button>
        </div>
    </div>
</div>
@endsection

<script>
    async function createOrder(id_cart) {
        const token = localStorage.getItem("token");

        const res = await fetch(`/api/v1/orders/create`, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "Authorization": `Bearer ${token}`,
                "Accept": "application/json "
            },
            body: JSON.stringify({ id_cart: id_cart })
        });

        const data = await res.json();

        if (data.status === "success" && data.order) {
            console.log("Orden creada:", data.order);
            return data.order;
        } else {
            alert("Error al crear la orden: " + data.message +" Data cart:"+ data.cart);
            throw new Error(data.message + data.cart);
        }
    }

    async function redirectToMercadoPago() {
        try {
            const order = await createOrder();

            const res = await fetch(`/api/v1/payments/mercadopago/${order.id_order}`, {
                method: "GET",
                headers: {
                    "Authorization": `Bearer ${localStorage.getItem('token')}`,
                    "Accept": "application/json"
                }
            });

            const data = await res.json();

            if (data.status === "success" && (data.sandbox_init_point || data.init_point)) {
                const url = data.sandbox_init_point || data.init_point;
                window.location.href = url;
            } else {
                alert("Error al redirigir a MercadoPago: " + data.message);
            }
        } catch (err) {
            console.error(err);
            alert("Error en el flujo de pago.");
        }
    }
    document.addEventListener("DOMContentLoaded", async () => {
        const token = localStorage.getItem("token");
        const id_cart = localStorage.getItem("id_cart");

        if (!token || !id_cart) {
            window.location.href = "/";
            return;
        }

        const cartContainer = document.getElementById("cart-items");
        const totalElement = document.getElementById("cart-total");
        async function loadCart() {
            try {
                const res = await fetch(`/api/v1/carts/${id_cart}`, {
                    headers: {
                        "Authorization": `Bearer ${token}`,
                        "Accept": "application/json"
                    },
                });

                const json = await res.json();
                const cartDetails = json.data?.cartDetails ?? [];

                if (!res.ok || cartDetails.length === 0) {
                    cartContainer.innerHTML = `<p class="text-center text-[#6b5846] py-4">Tu carrito está vacío.</p>`;
                    totalElement.textContent = "$0.00";
                    return;
                }

                cartContainer.innerHTML = "";
                let total = 0;

                cartDetails.forEach(item => {
                    const subtotal = parseFloat(item.unit_price) * item.quantity;
                    total += subtotal;

                    cartContainer.innerHTML += `
                    <div class="flex items-center justify-between py-4 border-b border-[#d9c8b6] gap-4">
                        <div class="flex items-center gap-4 flex-1">
                            <img
                            src="${item.product?.image_url}"
                            alt="${item.product?.name}"
                            class="w-20 h-20 object-cover rounded-xl border border-[#d9c8b6] shadow-sm"
                            >
                            <div>
                            <h3 class="font-semibold text-lg text-[#4a3b2f]">
                                ${item.product?.name ?? 'Producto sin nombre'}
                            </h3>
                            <p class="text-sm text-[#6b5846] mt-1">
                                $${parseFloat(item.unit_price).toFixed(2)} <span class="text-xs text-[#8b7764]">c/u</span>
                            </p>
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            <input
                            type="number"
                            min="1"
                            value="${item.quantity}"
                            data-id="${item.id_cart_detail}"
                            class="cart-qty w-16 text-center border border-[#d9c8b6] rounded-lg text-[#4a3b2f] focus:outline-none focus:ring focus:ring-[#d9c8b6]"
                            >
                            <p class="font-semibold text-[#a67c52] text-lg min-w-[80px] text-right">
                            $${subtotal.toFixed(2)}
                            </p>
                            <button
                            class="delete-item bg-red-500 text-white px-3 py-1 rounded-lg hover:bg-red-600 transition"
                            data-id="${item.id_cart_detail}"
                            title="Eliminar producto"
                            >
                            🗑️
                            </button>
                        </div>
                    </div>
      `;
                });

                totalElement.textContent = `$${total.toFixed(2)}`;

                document.querySelectorAll(".cart-qty").forEach(input => {
                    input.addEventListener("change", async () => {
                        const idDetail = input.dataset.id;
                        const newQty = parseInt(input.value);
                        if (newQty > 0) {
                            await updateQuantity(idDetail, newQty);
                            await loadCart();
                        }
                    });
                });

                document.querySelectorAll(".delete-item").forEach(btn => {
                    btn.addEventListener("click", async () => {
                        const idDetail = btn.dataset.id;
                        await deleteCartItem(idDetail);
                        await loadCart();
                    });
                });

            } catch (err) {
                console.error("Error al cargar carrito:", err);
                cartContainer.innerHTML = `<p class="text-center text-red-600">Error al cargar el carrito.</p>`;
            }
        }

        async function updateQuantity(idDetail, quantity) {
            try {
                await fetch(`/api/v1/cart-details/${idDetail}`, {
                    method: "PATCH",
                    headers: {
                        "Authorization": `Bearer ${token}`,
                        "Accept": "application/json",
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({
                        quantity
                    })
                });
            } catch (err) {
                console.error("Error al actualizar cantidad:", err);
            }
        }

        async function deleteCartItem(idDetail) {
            try {
                await fetch(`/api/v1/cart-details/${idDetail}`, {
                    method: "DELETE",
                    headers: {
                        "Authorization": `Bearer ${token}`,
                        "Accept": "application/json"
                    }
                });
            } catch (err) {
                console.error("Error al eliminar producto:", err);
            }
        }

        loadCart();


    });
</script>
