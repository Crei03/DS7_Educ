<template>
    <div class="flex flex-col items-center justify-center min-h-screen bg-bg">
        <h1 class="headline">Bienvenido a la Plataforma Académica</h1>
        <p class="mt-4 text-lg text-text-main">
            Comienza explorando tus cursos y progreso.
        </p>
        <div class="cta">
            <router-link to="/cursos" class="btn btn-primary"
                >Ver Cursos</router-link
            >
        </div>
    </div>
</template>

<script setup>
// Vista principal de inicio
</script>

<style scoped>
.headline {
    @apply text-4xl md:text-5xl font-extrabold;
}
.btn-primary {
    @apply bg-primary text-white px-6 py-3 rounded shadow hover:bg-primary-dk transition;
}
</style>
