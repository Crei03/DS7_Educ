<template>
    <div class="home-container">
        <h1 class="headline">Bienvenido a la Plataforma Académica</h1>
        <p class="subtext">
            Comienza explorando tus cursos y progreso.
        </p>
        <div class="cta">
            <router-link to="/cursos" class="btn-primary">Ver Cursos</router-link>
        </div>
    </div>
</template>

<script setup>
// Vista principal de inicio
</script>

<style scoped>
.home-container {
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    background: #f2f2f2;
    padding: 2rem;
}
.headline {
    font-size: 2.5rem;
    font-weight: 800;
    text-align: center;
    color: #1f2937;
    margin-bottom: 1.5rem;
}
.subtext {
    margin-top: 1rem;
    font-size: 1.125rem;
    color: #1f2937;
    text-align: center;
}
.cta {
    margin-top: 2rem;
}
.btn-primary {
    background: #0554f2;
    color: #fff;
    padding: 0.75rem 1.5rem;
    border-radius: 0.5rem;
    box-shadow: 0 2px 8px rgba(5, 84, 242, 0.08);
    font-size: 1rem;
    font-weight: 600;
    border: none;
    cursor: pointer;
    transition: background 0.2s;
    text-decoration: none;
    display: inline-block;
}
.btn-primary:hover {
    background: #0540f2;
}
</style>
