<nav class="bg-[#f0e8e0] border-b border-[#d9c8b6]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 items-center">
            <img src="/storage/assets/OthinelLogo3.png" class ="h-35 w-25 full object-cover" alt="Logo">
            <!-- Nombre de la tienda -->
            <a href="{{ url('/') }}" class="text-2xl font-semibold text-[#5c4033] hover:text-[#3e2a20] transition">
                Othniel Store
            </a>

            <!-- Menú derecho -->
            <div class="flex items-center space-x-6">
                <nav class="bg-[#f0e8e0] border-b border-[#d9c8b6] shadow-sm">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div class="flex justify-between h-16 items-center" >
                            <div id="navbar-user" class="flex items-center space-x-6"></div>
                        </div>
                    </div>
                </nav>
            </div>
        </div>
    </div>
</nav>
<script>
document.addEventListener("DOMContentLoaded", async () => {
  const navbarUser = document.getElementById("navbar-user");
  const token = localStorage.getItem("token");

  if (!token) {
    navbarUser.innerHTML = `
      <a href="/login" class="text-[#5c4033] font-semibold hover:text-[#3e2a20] transition">
        Iniciar sesión
      </a>
      <a href="/register" class="bg-[#d9c8b6] hover:bg-[#c7b39a] text-[#3e2a20] font-semibold px-4 py-2 rounded-lg transition">
        Registrarse
      </a>
    `;
    return;
  }

  try {
    const res = await fetch("/api/v1/check-token", {
      headers: {
        "Authorization": `Bearer ${token}`,
        "Accept": "application/json"
      }
    });

    const data = await res.json();

    if (res.ok && data.authenticated) {
      navbarUser.innerHTML = `
        <span class="text-[#5c4033] font-medium">
          ${data.user.name}
        </span>
        <button id="logoutBtn"
                class="bg-[#d9c8b6] hover:bg-[#c7b39a] text-[#3e2a20] font-semibold px-4 py-2 rounded-lg transition">
          Cerrar sesión
        </button>
      `;

      document.getElementById("logoutBtn").addEventListener("click", async () => {
        await fetch("/api/v1/logout", {
          method: "POST",
          headers: {
            "Authorization": `Bearer ${token}`,
            "Accept": "application/json"
          }
        });
        localStorage.removeItem("token");
        window.location.href = "/login";
      });
    } else {
      localStorage.removeItem("token");
      navbarUser.innerHTML = `
        <a href="/login" class="text-[#5c4033] font-semibold hover:text-[#3e2a20] transition">
          Iniciar sesión
        </a>
        <a href="/register" class="bg-[#d9c8b6] hover:bg-[#c7b39a] text-[#3e2a20] font-semibold px-4 py-2 rounded-lg transition">
          Registrarse
        </a>
      `;
    }
  } catch (error) {
    console.error("Error verificando token:", error);
    navbarUser.innerHTML = `
      <a href="/login" class="text-[#5c4033] font-semibold hover:text-[#3e2a20] transition">
        Iniciar sesión
      </a>
      <a href="/register" class="bg-[#d9c8b6] hover:bg-[#c7b39a] text-[#3e2a20] font-semibold px-4 py-2 rounded-lg transition">
        Registrarse
      </a>
    `;
  }
});
</script>

