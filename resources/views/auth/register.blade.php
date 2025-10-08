@extends('layouts.app')

@section('content')
<div class="max-w-sm mx-auto mt-10 bg-white p-6 rounded shadow">
    <h2 class="text-2xl font-bold mb-4">Registro de usuario</h2>

    <form id="registerForm">
        @csrf
        <div class="mb-3">
            <label>Nombre</label>
            <input type="text" name="name" class="w-full border rounded p-2" required>
        </div>
        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="w-full border rounded p-2" required>
        </div>
        <div class="mb-3">
            <label>Contraseña</label>
            <input type="password" name="password" class="w-full border rounded p-2" required>
        </div>
        <div class="mb-3">
            <label>Confirmar contraseña</label>
            <input type="password" name="password_confirmation" class="w-full border rounded p-2" required>
        </div>

        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded w-full">Registrarse</button>
    </form>

    <p class="mt-3 text-center">
        ¿Ya tienes cuenta?
        <a href="{{ route('login.form') }}" class="text-blue-600">Inicia sesión</a>
    </p>
</div>

<script>
document.getElementById('registerForm').addEventListener('submit', async (e) => {
    e.preventDefault();

    const form = e.target;
    const data = {
        name: form.name.value,
        email: form.email.value,
        password: form.password.value,
        password_confirmation: form.password_confirmation.value
    };

    const res = await fetch('/api/v1/register', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify(data)
    });

    const json = await res.json();

    if (json.success) {
        alert(json.message);
        window.location.href = '/login';
    } else {
        alert('Error: ' + (json.message || 'Datos inválidos'));
    }
});
</script>
@endsection
