<template>
    <AdminLayout>
        <Head :title="`Usuário: ${user.name}`" />

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Header com botões de ação -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="flex justify-between items-center">
                            <div>
                                <h1 class="text-3xl font-bold text-gray-900">{{ user.name }}</h1>
                                <p class="mt-1 text-sm text-gray-600">Detalhes do usuário</p>
                            </div>
                            <div class="flex space-x-3">
                                <Link
                                    :href="route('admin.users.edit', user.id)"
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150"
                                >
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                    Editar
                                </Link>
                                <Link
                                    :href="route('admin.users.index')"
                                    class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150"
                                >
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                    </svg>
                                    Voltar
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Informações do Usuário -->
                    <div class="lg:col-span-1">
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h2 class="text-lg font-medium text-gray-900 mb-4">Informações Pessoais</h2>
                                
                                <!-- Avatar -->
                                <div class="flex justify-center mb-6">
                                    <div class="w-24 h-24 bg-gray-300 rounded-full flex items-center justify-center">
                                        <svg class="w-12 h-12 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                </div>

                                <!-- Dados do usuário -->
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Nome Completo</label>
                                        <p class="mt-1 text-sm text-gray-900 bg-gray-50 rounded-md px-3 py-2">{{ user.name }}</p>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">SARAM</label>
                                        <p class="mt-1 text-sm text-gray-900 bg-gray-50 rounded-md px-3 py-2">{{ user.saram }}</p>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Função</label>
                                        <span class="mt-1 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                              :class="user.role === 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800'">
                                            {{ user.role === 'admin' ? 'Administrador' : 'Votante' }}
                                        </span>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Status de Votação</label>
                                        <span class="mt-1 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                              :class="votesCount > 0 ? 'bg-green-100 text-green-800' : 'bg-orange-100 text-orange-800'">
                                            {{ votesCount > 0 ? 'Já Votou' : 'Pendente' }}
                                        </span>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Data de Cadastro</label>
                                        <p class="mt-1 text-sm text-gray-900 bg-gray-50 rounded-md px-3 py-2">
                                            {{ new Date(user.created_at).toLocaleDateString('pt-BR', { 
                                                year: 'numeric', 
                                                month: 'long', 
                                                day: 'numeric',
                                                hour: '2-digit',
                                                minute: '2-digit'
                                            }) }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Estatísticas -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-6">
                            <div class="p-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Estatísticas</h3>
                                <div class="grid grid-cols-1 gap-4">
                                    <div class="bg-blue-50 rounded-lg p-4">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0">
                                                <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                            </div>
                                            <div class="ml-4">
                                                <p class="text-sm font-medium text-blue-900">Total de Votos</p>
                                                <p class="text-2xl font-bold text-blue-600">{{ votesCount }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Histórico de Votos -->
                    <div class="lg:col-span-2">
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h2 class="text-lg font-medium text-gray-900 mb-4">Histórico de Votos</h2>
                                
                                <div v-if="user.votes && user.votes.length > 0" class="space-y-4">
                                    <div v-for="vote in user.votes" :key="vote.id" 
                                         class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors duration-150">
                                        <div class="flex items-start space-x-4">
                                            <!-- Thumbnail da foto -->
                                            <div class="flex-shrink-0">
                                                <div class="w-16 h-16 bg-gray-200 rounded-lg overflow-hidden">
                                                    <img 
                                                        v-if="vote.photo.file_path"
                                                        :src="`/storage/${vote.photo.file_path}`"
                                                        :alt="vote.photo.caption || 'Foto'"
                                                        class="w-full h-full object-cover"
                                                        @error="handleImageError"
                                                    />
                                                    <div v-else class="w-full h-full flex items-center justify-center">
                                                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                        </svg>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <!-- Informações do voto -->
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center justify-between">
                                                    <div>
                                                        <h3 class="text-sm font-medium text-gray-900">
                                                            {{ vote.photo.title }}
                                                        </h3>
                                                        <p class="text-sm text-gray-500">
                                                            Projeto: {{ vote.photo.project.name }}
                                                        </p>
                                                    </div>
                                                    <div class="text-right">
                                                        <p class="text-sm text-gray-500">
                                                            {{ new Date(vote.created_at).toLocaleDateString('pt-BR', {
                                                                day: '2-digit',
                                                                month: '2-digit',
                                                                year: 'numeric',
                                                                hour: '2-digit',
                                                                minute: '2-digit'
                                                            }) }}
                                                        </p>
                                                    </div>
                                                </div>
                                                
                                                <div class="mt-2">
                                                    <p class="text-xs text-gray-400">
                                                        ID do Voto: #{{ vote.id }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Estado vazio -->
                                <div v-else class="text-center py-12">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <h3 class="mt-2 text-sm font-medium text-gray-900">Nenhum voto registrado</h3>
                                    <p class="mt-1 text-sm text-gray-500">Este usuário ainda não realizou nenhum voto.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    user: Object,
    votesCount: Number,
});

const handleImageError = (event) => {
    // Esconde a imagem e mostra o ícone de fallback
    event.target.style.display = 'none';
    event.target.parentElement.innerHTML = `
        <div class="w-full h-full flex items-center justify-center">
            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
        </div>
    `;
};
</script>