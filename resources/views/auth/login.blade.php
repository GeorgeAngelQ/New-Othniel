@extends('layouts.app')

@section('content')
<div class="max-w-sm mx-auto mt-10 bg-white p-6 rounded shadow">
    <h2 class="text-2xl font-bold mb-4">Iniciar sesión</h2>

    <form id="loginForm">
        @csrf
        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="w-full border rounded p-2" required>
        </div>

        <div class="mb-3">
            <label>Contraseña</label>
            <input type="password" name="password" class="w-full border rounded p-2" required>
        </div>

        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded w-full">Entrar</button>
    </form>

    <p class="mt-3 text-center">
        ¿No tienes cuenta?
        <a href="{{ route('register.form') }}" class="text-blue-600">Regístrate</a>
    </p>
</div>

<script>
document.getElementById('loginForm').addEventListener('submit', async (e) => {
    e.preventDefault();

    const form = e.target;
    const data = {
        email: form.email.value,
        password: form.password.value
    };

    const res = await fetch('/api/v1/login', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify(data)
    });

    const json = await res.json();

    if (json.success) {
        alert('Bienvenido, ' + json.user.name);
        window.location.href = '/dashboard';
    } else {
        alert(json.message);
    }
});
</script>
@endsection
