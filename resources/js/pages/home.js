document.addEventListener("DOMContentLoaded", async () => {
    let currentPage = 1;
    const token = localStorage.getItem("token");
    if (!token) {
        console.warn("No hay token, redirigiendo a login...");
        window.location.href = "/login";
        return;
    }

    let id_cart = null;
    const agentBox = document.getElementById("agentBox");
    const toggleBtn = document.getElementById("agentToggle");

    toggleBtn.addEventListener("click", () => {
        agentBox.classList.toggle("h-12");
        agentBox.classList.toggle("w-[260px]");
    });
    async function getOrCreateCart() {
        try {
            const res = await fetch("/api/v1/cart/current", {
                headers: {
                    Authorization: `Bearer ${token}`,
                    Accept: "application/json",
                },
            });
            const json = await res.json();
            if (res.ok && json.data) {
                id_cart = json.data.id_cart;
                localStorage.setItem("id_cart", id_cart);
            } else {
                console.error("No se pudo obtener el carrito:", json);
            }
        } catch (err) {
            console.error("Error al obtener carrito:", err);
        }
    }

    await getOrCreateCart();
    async function cargarProductos(page = 1) {
        try {
            const token = localStorage.getItem("token");
            const response = await fetch(`/api/v1/products?page=${page}`, {
                headers: {
                    Authorization: `Bearer ${token}`,
                    Accept: "application/json",
                    "Content-Type": "application/json",
                },
            });

            const result = await response.json();
            const products = result.data.items;
            const meta = result.data.meta;
            const container = document.getElementById("product-grid");
            container.innerHTML = "";

            if (products && products.length > 0) {
                products.forEach((product) => {
                    container.innerHTML += `
                            <div class="w-44 bg-white shadow-md rounded-xl overflow-hidden hover:shadow-lg transition-transform transform hover:-translate-y-1">
                                <div class="w-full aspect-square overflow-hidden">
                                    <img src="${
                                        product.image_url ??
                                        "https://via.placeholder.com/200"
                                    }"
                                        alt="${product.name}"
                                        class="object-cover w-full h-full transition-transform duration-300 hover:scale-105">
                                </div>
                                <div class="p-3 text-center">
                                    <h2 class="text-sm font-semibold text-gray-800 truncate">${
                                        product.name
                                    }</h2>
                                    <p class="text-xs text-gray-500 mb-1 line-clamp-2">${
                                        product.description ?? "Sin descripción"
                                    }</p>
                                    <p class="text-blue-600 font-bold text-sm mb-3">$${parseFloat(
                                        product.price
                                    ).toFixed(2)}</p>
                                    <div class="flex items-center justify-center gap-2 mb-3">
                                        <button class="qty-dec bg-[#d9c8b6] text-[#4a3b2f] px-2 py-1 rounded hover:bg-[#cbb6a2] transition">−</button>
                                        <input type="number" min="1" max="${
                                            product.stock
                                        }" value="1"
                                            class="qty-input w-12 text-center border border-[#d9c8b6] rounded text-[#4a3b2f]"/>
                                        <button class="qty-inc bg-[#d9c8b6] text-[#4a3b2f] px-2 py-1 rounded hover:bg-[#cbb6a2] transition">+</button>
                                    </div>

                                    <button class="add-cart w-full bg-[#a67c52] text-white py-1.5 rounded-lg text-sm hover:bg-[#8c6644] transition"
                                        data-id="${product.id_product}">
                                        Agregar
                                    </button>
                                </div>
                            </div>
                        `;
                });
                document.querySelectorAll(".qty-inc").forEach((btn) => {
                    btn.addEventListener("click", () => {
                        const input =
                            btn.parentElement.querySelector(".qty-input");
                        const max = parseInt(input.max);
                        let value = parseInt(input.value);
                        if (value < max) input.value = value + 1;
                    });
                });

                document.querySelectorAll(".qty-dec").forEach((btn) => {
                    btn.addEventListener("click", () => {
                        const input =
                            btn.parentElement.querySelector(".qty-input");
                        let value = parseInt(input.value);
                        if (value > 1) input.value = value - 1;
                    });
                });
                document.querySelectorAll(".add-cart").forEach((btn) => {
                    btn.addEventListener("click", async () => {
                        const id_product = btn.dataset.id;
                        const productElement = btn.closest("div");
                        const qty = parseInt(
                            productElement.querySelector(".qty-input").value
                        );
                        const price = parseFloat(
                            productElement
                                .querySelector(".text-blue-600")
                                .textContent.replace("$", "")
                        );
                        await addToCart(id_product, qty, price);
                    });
                });
            } else {
                container.innerHTML =
                    "<p class='col-span-full text-center text-gray-600'>No hay productos disponibles.</p>";
            }

            document.getElementById(
                "currentPage"
            ).textContent = `Página ${meta.current_page} de ${meta.last_page}`;
            document.getElementById("prevPage").disabled =
                !result.data.links.prev;
            document.getElementById("nextPage").disabled =
                !result.data.links.next;

            document.getElementById("prevPage").onclick = () => {
                if (meta.current_page > 1) {
                    currentPage--;
                    cargarProductos(currentPage);
                }
            };
            document.getElementById("nextPage").onclick = () => {
                if (meta.current_page < meta.last_page) {
                    currentPage++;
                    cargarProductos(currentPage);
                }
            };
        } catch (error) {
            console.error("Error al cargar productos:", error);
        }
    }

    cargarProductos(currentPage);
});
async function addToCart(id_product, quantity = 1, unitPrice = 0) {
    const token = localStorage.getItem("token");
    const id_cart = localStorage.getItem("id_cart");

    try {
        const response = await fetch("/api/v1/cart-details", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                Authorization: `Bearer ${token}`,
                Accept: "application/json",
            },
            body: JSON.stringify({
                id_cart: id_cart,
                id_product: id_product,
                quantity: quantity,
                unit_price: unitPrice,
                subtotal: unitPrice * quantity,
            }),
        });

        const data = await response.json();
        if (response.ok) {
            alert("🛒 Producto agregado al carrito");
        } else {
            console.error("Error al agregar:", data);
            alert(data.message || "No se pudo agregar el producto");
        }
    } catch (err) {
        console.error("Error de red o servidor:", err);
        alert("Error en la conexión con el servidor");
    }
}
