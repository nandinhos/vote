<template>
    <Modal :show="show" @close="$emit('close')" max-width="6xl">
        <div class="bg-white rounded-lg overflow-hidden">
            <!-- Header do Modal -->
            <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">
                            {{ photo?.caption || 'Visualização da Foto' }}
                        </h3>
                        <p class="text-sm text-gray-600 mt-1">
                            Projeto: {{ photo?.project_name }}
                        </p>
                    </div>
                    <button 
                        @click="$emit('close')"
                        class="text-gray-400 hover:text-gray-600 transition-colors"
                    >
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Conteúdo do Modal -->
            <div class="p-6">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Imagem Principal -->
                    <div class="lg:col-span-2">
                        <div class="relative bg-gray-100 rounded-lg overflow-hidden">
                            <img 
                                v-if="photo"
                                :src="`/storage/${photo.file_path}`" 
                                :alt="photo.caption || 'Foto do projeto'"
                                class="w-full h-auto max-h-[70vh] object-contain"
                            />
                            
                            <!-- Loading state -->
                            <div v-else class="w-full h-96 flex items-center justify-center">
                                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Painel de Informações e Votação -->
                    <div class="space-y-6">
                        <!-- Informações da Foto -->
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h4 class="font-semibold text-gray-900 mb-3">Informações</h4>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Projeto:</span>
                                    <span class="font-medium">{{ photo?.project_name }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Total de votos:</span>
                                    <span class="font-medium text-indigo-600">{{ photo?.votes_count || 0 }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Data:</span>
                                    <span class="font-medium">{{ formatDate(photo?.created_at) }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Painel de Votação -->
                        <div class="bg-gradient-to-br from-indigo-50 to-purple-50 rounded-lg p-4 border border-indigo-200">
                            <h4 class="font-semibold text-gray-900 mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11.5V14m0-2.5v-6a1.5 1.5 0 113 0m-3 6a1.5 1.5 0 00-3 0v2a7.5 7.5 0 0015 0v-5a1.5 1.5 0 00-3 0m-6-3V11m0-5.5v-1a1.5 1.5 0 013 0v1m0 0V11m0-5.5T6.5 15a2.5 2.5 0 002.5 2.5z" />
                                </svg>
                                Votação
                            </h4>
                            
                            <!-- Status do Voto -->
                            <div class="mb-4">
                                <div v-if="isVoted" class="flex items-center text-green-700 bg-green-100 px-3 py-2 rounded-lg">
                                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="font-medium">Você votou nesta foto</span>
                                </div>
                                <div v-else class="flex items-center text-gray-600 bg-gray-100 px-3 py-2 rounded-lg">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span>Ainda não votou nesta foto</span>
                                </div>
                            </div>

                            <!-- Botões de Ação -->
                            <div class="space-y-3">
                                <button 
                                    v-if="!isVoted"
                                    @click="handleVote"
                                    :disabled="processing || !canVote"
                                    class="w-full bg-indigo-600 hover:bg-indigo-700 disabled:bg-gray-400 text-white font-semibold py-3 px-4 rounded-lg transition-colors flex items-center justify-center"
                                >
                                    <svg v-if="processing" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    {{ processing ? 'Votando...' : 'Votar nesta foto' }}
                                </button>

                                <button 
                                    v-else
                                    @click="handleUnvote"
                                    :disabled="processing"
                                    class="w-full bg-red-600 hover:bg-red-700 disabled:bg-gray-400 text-white font-semibold py-3 px-4 rounded-lg transition-colors flex items-center justify-center"
                                >
                                    <svg v-if="processing" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    {{ processing ? 'Removendo...' : 'Remover voto' }}
                                </button>
                            </div>

                            <!-- Informações sobre limites -->
                            <div class="mt-4 text-xs text-gray-600 bg-white p-3 rounded border">
                                <p class="font-medium mb-1">Lembrete:</p>
                                <p>• Você pode votar em até 10 fotos</p>
                                <p>• Escolha livremente suas fotos favoritas</p>
                                <p>• Votos podem ser alterados a qualquer momento</p>
                            </div>
                        </div>

                        <!-- Navegação entre fotos -->
                        <div v-if="showNavigation" class="flex justify-between">
                            <button 
                                @click="$emit('previous')"
                                :disabled="!hasPrevious"
                                class="flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                </svg>
                                Anterior
                            </button>
                            
                            <button 
                                @click="$emit('next')"
                                :disabled="!hasNext"
                                class="flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                Próxima
                                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Modal>
</template>

<script>
import Modal from './Modal.vue';
import { router } from '@inertiajs/vue3';

export default {
    components: {
        Modal
    },
    props: {
        show: {
            type: Boolean,
            default: false
        },
        photo: {
            type: Object,
            default: null
        },
        isVoted: {
            type: Boolean,
            default: false
        },
        canVote: {
            type: Boolean,
            default: true
        },
        showNavigation: {
            type: Boolean,
            default: false
        },
        hasPrevious: {
            type: Boolean,
            default: false
        },
        hasNext: {
            type: Boolean,
            default: false
        }
    },
    emits: ['close', 'vote', 'unvote', 'previous', 'next'],
    data() {
        return {
            processing: false
        }
    },
    methods: {
        async handleVote() {
            if (!this.photo || this.processing) return;
            
            this.processing = true;
            
            try {
                await router.post(route('voting.vote', this.photo.id), {}, {
                    preserveState: true,
                    preserveScroll: true,
                    onSuccess: () => {
                        this.$emit('vote', this.photo.id);
                    },
                    onError: (errors) => {
                        console.error('Erro ao votar:', errors);
                    }
                });
            } catch (error) {
                console.error('Erro ao processar voto:', error);
            } finally {
                this.processing = false;
            }
        },
        
        async handleUnvote() {
            if (!this.photo || this.processing) return;
            
            this.processing = true;
            
            try {
                await router.delete(route('voting.unvote', this.photo.id), {
                    preserveState: true,
                    preserveScroll: true,
                    onSuccess: () => {
                        this.$emit('unvote', this.photo.id);
                    },
                    onError: (errors) => {
                        console.error('Erro ao remover voto:', errors);
                    }
                });
            } catch (error) {
                console.error('Erro ao processar remoção de voto:', error);
            } finally {
                this.processing = false;
            }
        },
        
        formatDate(dateString) {
            if (!dateString) return '';
            return new Date(dateString).toLocaleDateString('pt-BR', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric'
            });
        }
    }
}
</script>