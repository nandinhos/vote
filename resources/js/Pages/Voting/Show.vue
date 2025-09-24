<template>
    <Head :title="`Votar em ${project.name}`" />

    <VotingLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        {{ project.name }}
                    </h2>
                    <p v-if="project.description" class="text-sm text-gray-600 mt-1">
                        {{ project.description }}
                    </p>
                </div>
                <Link
                    :href="route('voting.index')"
                    class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150"
                >
                    ← Voltar aos Projetos
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Estatísticas do Projeto -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-8">
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div class="text-center">
                                <div class="text-2xl font-bold text-indigo-600">{{ photos.length }}</div>
                                <div class="text-sm text-gray-500">Fotos</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-yellow-600">{{ project.vote_count }}</div>
                                <div class="text-sm text-gray-500">Total de Votos</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-green-600">{{ userVotes.length }}</div>
                                <div class="text-sm text-gray-500">Seus Votos</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-purple-600">{{ project.unique_voters }}</div>
                                <div class="text-sm text-gray-500">Votantes</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Grid de Fotos -->
                <div v-if="photos.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    <div v-for="photo in photos" :key="photo.id" class="group relative bg-white border border-gray-200 rounded-lg overflow-hidden hover:shadow-lg transition-all duration-200">
                        <div class="aspect-w-1 aspect-h-1 relative">
                            <img
                                :src="`/storage/${photo.file_path}`"
                                :alt="photo.caption"
                                class="w-full h-64 object-cover group-hover:opacity-90 transition-opacity duration-200"
                            >
                            
                            <!-- Overlay de Voto -->
                            <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 transition-all duration-200 flex items-center justify-center">
                                <button
                                    v-if="!hasVoted(photo.id)"
                                    @click="vote(photo.id)"
                                    :disabled="processing"
                                    class="opacity-0 group-hover:opacity-100 transition-opacity duration-200 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-medium flex items-center space-x-2 disabled:opacity-50 disabled:cursor-not-allowed"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                    </svg>
                                    <span>Votar</span>
                                </button>
                                
                                <button
                                    v-else
                                    @click="unvote(photo.id)"
                                    :disabled="processing"
                                    class="opacity-0 group-hover:opacity-100 transition-opacity duration-200 bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium flex items-center space-x-2 disabled:opacity-50 disabled:cursor-not-allowed"
                                >
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z"></path>
                                    </svg>
                                    <span>Remover Voto</span>
                                </button>
                            </div>
                            
                            <!-- Indicador de Voto -->
                            <div v-if="hasVoted(photo.id)" class="absolute top-2 right-2">
                                <div class="bg-red-500 text-white rounded-full p-2">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                        
                        <div class="p-4">
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center space-x-1">
                                    <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                    </svg>
                                    <span class="text-sm font-medium text-gray-900">{{ photo.vote_count }}</span>
                                </div>
                                <span v-if="hasVoted(photo.id)" class="text-xs text-red-600 font-medium">Você votou</span>
                            </div>
                            
                            <p class="text-sm text-gray-900 font-medium mb-1" v-html="photo.caption || '&nbsp;'"></p>
                            <p class="text-xs text-gray-500">{{ formatDate(photo.created_at) }}</p>
                        </div>
                    </div>
                </div>
                
                <!-- Estado vazio -->
                <div v-else class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Nenhuma foto disponível</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        Este projeto ainda não possui fotos para votação.
                    </p>
                </div>
            </div>
        </div>

        <!-- Toast de Feedback -->
        <div v-if="showToast" class="fixed bottom-4 right-4 z-50">
            <div class="bg-white border border-gray-200 rounded-lg shadow-lg p-4 max-w-sm">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg v-if="toastType === 'success'" class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        <svg v-else class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-900">{{ toastMessage }}</p>
                    </div>
                </div>
            </div>
        </div>
    </VotingLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import VotingLayout from '@/Layouts/VotingLayout.vue';

const props = defineProps({
    project: Object,
    photos: Array,
    userVotes: Array
});

const processing = ref(false);
const showToast = ref(false);
const toastMessage = ref('');
const toastType = ref('success');

const hasVoted = (photoId) => {
    return props.userVotes.some(vote => vote.photo_id === photoId);
};

const vote = (photoId) => {
    if (processing.value) return;
    
    processing.value = true;
    router.post(route('voting.vote', photoId), {}, {
        onSuccess: () => {
            showToastMessage('Voto registrado com sucesso!', 'success');
        },
        onError: (errors) => {
            const errorMessage = Object.values(errors)[0] || 'Erro ao registrar voto';
            showToastMessage(errorMessage, 'error');
        },
        onFinish: () => {
            processing.value = false;
        }
    });
};

const unvote = (photoId) => {
    if (processing.value) return;
    
    processing.value = true;
    router.delete(route('voting.unvote', photoId), {
        onSuccess: () => {
            showToastMessage('Voto removido com sucesso!', 'success');
        },
        onError: (errors) => {
            const errorMessage = Object.values(errors)[0] || 'Erro ao remover voto';
            showToastMessage(errorMessage, 'error');
        },
        onFinish: () => {
            processing.value = false;
        }
    });
};

const showToastMessage = (message, type) => {
    toastMessage.value = message;
    toastType.value = type;
    showToast.value = true;
    
    setTimeout(() => {
        showToast.value = false;
    }, 3000);
};

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('pt-BR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    });
};
</script>