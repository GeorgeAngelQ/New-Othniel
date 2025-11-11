@extends('layouts.app')

@section('header')
<h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
    {{ __('Tienda Othniel') }}
</h2>
@endsection

@section('content')
<div class="py-10 px-6">
    <main class="max-w-7xl mx-auto">
        <h2 class="text-3xl font-bold mb-6 text-gray-900 dark:text-black text-center">
            Nuestros productos
        </h2>

        <!-- Grid de productos -->
        <section id="product-grid"
            class="grid gap-6 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-5">
            <!-- Productos cargados por JS -->
        </section>
        <div class="flex justify-center items-center gap-3 mt-6">
            <button id="prevPage"
                class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300 text-sm disabled:opacity-50">
                ◀ Anterior
            </button>

            <span id="currentPage" class="text-gray-700 text-sm">Página 1</span>

            <button id="nextPage"
                class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300 text-sm disabled:opacity-50">
                Siguiente ▶
            </button>
        </div>
    </main>
</div>
<!-- WIDGET DEL CHATBOT -->
<div id="agentBox"
    class="fixed bottom-6 right-6 w-[340px] max-h-[75vh] bg-white rounded-xl shadow-2xl border border-gray-300 flex flex-col overflow-hidden z-[9999]
    max-sm:right-3 max-sm:left-3 max-sm:w-auto">

    <!-- Header -->
    <div class="flex items-center gap-2 px-4 py-2 border-b border-gray-200 font-semibold bg-gray-100">
        🛍️ Othniel · Asistente

        <button id="agentToggle"
            class="ml-auto text-gray-600 hover:text-gray-900 text-lg leading-none"
            title="Minimizar" aria-label="Minimizar">
            —
        </button>
    </div>

    <!-- Log -->
    <div id="agentLog" class="flex-1 overflow-y-auto px-4 py-2 space-y-2 text-sm">
        <div class="text-gray-500 text-xs">
            Tip: pregunta “polo color negro / jogger talla S / camisa menos de 60”.
        </div>
    </div>

    <!-- Input -->
    <div id="agentMessages" class="p-3 h-64 overflow-y-auto bg-gray-50 space-y-2"></div>
    <div class="border-t border-gray-200 flex">
        <input id="agentQ"
            placeholder="Escribe aquí..."
            class="flex-1 px-3 py-2 text-sm outline-none bg-white"
            autocomplete="off">

        <button id="agentSend"
            class="px-4 bg-emerald-600 hover:bg-emerald-700 text-white text-sm rounded-none">
            Enviar
        </button>
    </div>

    <script>
        const input = document.getElementById("agentQ");
        const sendBtn = document.getElementById("agentSend");
        const chatBox = document.getElementById("agentMessages");

        // ✅ Renderizar mensaje en el chat
        function addMessage(role, text) {
            const bubble = document.createElement("div");
            bubble.className = `p-2 my-1 rounded-lg text-sm max-w-[80%] ${
            role === "user" ? "bg-emerald-600 text-white ml-auto" : "bg-gray-200 text-gray-900"
        }`;
            bubble.textContent = text;

            chatBox.appendChild(bubble);
            chatBox.scrollTop = chatBox.scrollHeight;
        }

        async function sendMessage() {
            const msg = input.value.trim();
            if (!msg) return;

            addMessage("user", msg);

            try {
                const res = await fetch("http://127.0.0.1:5000/agent/chat", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                    },
                    body: JSON.stringify({
                        message: msg
                    }),
                });

                const data = await res.json();
                console.log("🟢 Respuesta agente:", data);

                if (data.type === "chat") addMessage("agent", data.data);

                if (data.type === "products") renderProducts(data.data);

            } catch (error) {
                console.error("❌ Error enviando mensaje al agente:", error);
                addMessage("agent", "Error conectando con el asistente 😣");
            }

            input.value = ""; // limpiar input
        }

        // ✅ Renderizar cards de productos
        function renderProducts(products) {
            const container = document.createElement("div");
            container.className = "grid grid-cols-2 gap-3 my-3";

            products.forEach(p => {
                const card = document.createElement("div");
                card.className = "border rounded-lg p-2 shadow-sm bg-white";

                card.innerHTML = `
                <img src="${p.img}" class="w-full h-28 object-cover rounded" />
                <div class="mt-2 text-xs font-semibold">${p.nombre}</div>
                <div class="text-sm font-bold">S/ ${p.precio}</div>
                <button class="mt-2 w-full bg-emerald-600 text-white text-xs py-1 rounded">
                    Añadir al carrito
                </button>
            `;

                container.appendChild(card);
            });

            chatBox.appendChild(container);
            chatBox.scrollTop = chatBox.scrollHeight;
        }

        // ✅ Eventos
        sendBtn.addEventListener("click", sendMessage);
        input.addEventListener("keypress", (e) => {
            if (e.key === "Enter") sendMessage();
        });
    </script>

    @endsection
    @section('scripts')
    @vite([
    'resources/css/app.css',
    'resources/js/app.js',
    'resources/js/pages/home.js'
    ])
    @endsection
