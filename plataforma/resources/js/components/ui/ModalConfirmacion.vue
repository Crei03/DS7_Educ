<template>
  <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
      <!-- Header -->
      <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900">{{ titulo }}</h3>
      </div>
      
      <!-- Contenido -->
      <div class="px-6 py-4">
        <p class="text-gray-600">{{ mensaje }}</p>
      </div>
      
      <!-- Footer -->
      <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-3">
        <button
          @click="$emit('cancelar')"
          class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
        >
          {{ textoCancelar }}
        </button>
        
        <button
          @click="$emit('confirmar')"
          class="px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
        >
          {{ textoConfirmar }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  titulo: {
    type: String,
    required: true
  },
  mensaje: {
    type: String,
    required: true
  },
  textoConfirmar: {
    type: String,
    default: 'Confirmar'
  },
  textoCancelar: {
    type: String,
    default: 'Cancelar'
  }
})

defineEmits(['confirmar', 'cancelar'])
</script>
