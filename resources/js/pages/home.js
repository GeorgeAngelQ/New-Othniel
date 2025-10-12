document.addEventListener("DOMContentLoaded", async () => {
    let currentPage = 1;

    async function cargarProductos(page = 1) {
        try {
            const token = localStorage.getItem("token");
            const response = await fetch(`/api/v1/products?page=${page}`, {
                headers: {
                    Authorization: `Bearer ${token}`,
                    Accept: "application/json",
                },
            });

            const result = await response.json();
            const products = result.data.items;
            const meta = result.data.meta;
            console.log(result);
            console.log(products);
            console.log(meta);
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
                                    data-id="${product.id}">
                                    Agregar
                                </button>
                            </div>
                        </div>
                    `;
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
