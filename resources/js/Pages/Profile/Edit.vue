<script setup>
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import { Head, router } from '@inertiajs/vue3';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const goBack = () => {
    window.history.back();
};
</script>

<template>
    <Head title="Alterar Senha" />

    <div class="min-h-screen bg-gray-100">
        <!-- Header simples -->
        <div class="bg-white shadow">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex h-16 items-center justify-between">
                    <div class="flex items-center">
                        <button
                            @click="goBack"
                            class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                        >
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Voltar
                        </button>
                    </div>
                    <h1 class="text-xl font-semibold text-gray-900">
                        Alterar Senha
                    </h1>
                    <div class="w-20"></div> <!-- Spacer para centralizar o título -->
                </div>
            </div>
        </div>

        <!-- Conteúdo principal -->
        <div class="py-12">
            <div class="mx-auto max-w-2xl px-4 sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <UpdatePasswordForm />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
