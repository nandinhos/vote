<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div
        class="min-h-screen flex flex-col justify-center items-center bg-gradient-to-br from-purple-50 via-blue-50 to-indigo-50">
        <div class="text-center mb-6">
            <!-- Ícone roxo com checkmark -->
            <div
                class="mx-auto mb-4 w-16 h-16 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-md">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>

            <!-- Título EventosPro -->
            <h1 class="text-2xl font-bold text-gray-800 mb-1">GAC-PAC</h1>
            <p class="text-gray-500 text-sm font-medium">Enquete - Fotos para quadros</p>
        </div>

        <div class="w-full sm:max-w-sm px-6 py-8 bg-white shadow-lg rounded-xl border border-gray-100">
            <slot />
        </div>
    </div>
</template>
