<template>
    <VotingLayout>
        <Head title="Galeria de Votação" />
        
        <!-- Header da Galeria -->
        <div class="bg-white shadow-sm border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Galeria de Votação</h1>
                        <p class="mt-1 text-sm text-gray-600">
                            Clique nas fotos para visualizar em tamanho ampliado e votar. Você pode votar em até 10 fotos.
                        </p>
                    </div>
                    
                    <!-- Contador de Votos -->
                    <div class="bg-indigo-50 border border-indigo-200 rounded-lg px-4 py-3">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-indigo-600">{{ userVotes.length }}</div>
                            <div class="text-xs text-indigo-700">de 10 votos</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtros e Controles -->
        <div class="bg-gray-50 border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <!-- Filtro de Projetos -->
                    <div class="flex items-center space-x-4">
                        <label class="text-sm font-medium text-gray-700">Filtrar por projeto:</label>
                        <select 
                            v-model="selectedProject" 
                            @change="filterPhotos"
                            class="border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        >
                            <option value="">Todos os projetos</option>
                            <option v-for="project in projects" :key="project.id" :value="project.id">
                                {{ project.name }}
                            </option>
                        </select>
                    </div>

                    <!-- Estatísticas -->
                    <div class="flex items-center space-x-4 text-sm text-gray-600">
                        <span>{{ filteredPhotos.length }} fotos</span>
                        <span>•</span>
                        <span>{{ projects.length }} projetos</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Grid de fotos -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div v-if="filteredPhotos.length === 0" class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Nenhuma foto encontrada</h3>
                <p class="mt-1 text-sm text-gray-500">Não há fotos disponíveis para o filtro selecionado.</p>
            </div>

            <div v-else class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
                <div v-for="photo in filteredPhotos" 
                     :key="photo.id" 
                     class="relative group bg-white rounded-lg shadow-sm hover:shadow-md transition-all duration-300 overflow-hidden cursor-pointer"
                     @click="openPhotoModal(photo)">
                    
                    <!-- Card da Foto -->
                    <div :class="[
                        'bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-all duration-300 group cursor-pointer relative',
                        isPhotoVoted(photo.id) ? 'ring-4 ring-green-400 ring-opacity-60' : ''
                    ]">
                        <!-- Badge do Projeto -->
                        <div class="absolute top-2 left-2 z-10">
                            <span class="bg-blue-600 text-white text-xs px-2 py-1 rounded-full">
                                {{ photo.project_name }}
                            </span>
                        </div>
                    
                        <!-- Botão de Coração para Voto Direto - Parte Inferior Centralizada -->
                        <div class="absolute bottom-2 left-1/2 transform -translate-x-1/2 z-20">
                            <button 
                                @click.stop="handleDirectVote(photo.id)"
                                :disabled="!canVoteDirectly(photo.id)"
                                :class="[
                                    'p-2 rounded-full transition-all duration-300 shadow-lg transform',
                                    isPhotoVoted(photo.id) 
                                        ? 'bg-red-500 text-white hover:bg-red-600 animate-pulse' 
                                        : 'bg-white text-gray-600 hover:bg-red-50 hover:text-red-500',
                                    !canVoteDirectly(photo.id) && !isPhotoVoted(photo.id) 
                                        ? 'opacity-50 cursor-not-allowed' 
                                        : 'hover:scale-110 active:scale-95'
                                ]"
                                :title="getVoteButtonTitle(photo.id)"
                            >
                                <svg class="w-5 h-5 transition-all duration-300" 
                                     :fill="isPhotoVoted(photo.id) ? 'currentColor' : 'none'" 
                                     stroke="currentColor" 
                                     viewBox="0 0 24 24"
                                     :class="isPhotoVoted(photo.id) ? 'animate-bounce' : ''"
                                >
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                </svg>
                            </button>
                        </div>
                    
                        <!-- Overlay para abrir modal (clique na imagem) -->
                        <div @click="openPhotoModal(photo)" class="cursor-pointer">
                            <!-- Imagem -->
                            <div class="aspect-w-4 aspect-h-3">
                                <img :src="`/storage/${photo.file_path}`" 
                                     :alt="photo.caption || 'Foto do projeto'"
                                     class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-300">
                            </div>
                    
                            <!-- Informações da foto -->
                            <div class="p-3">
                                <!-- Espaço reservado para legenda - mantém altura consistente -->
                                <div class="h-10 mb-2 flex items-start">
                                    <p v-if="photo.caption" class="text-sm text-gray-700 line-clamp-2">{{ photo.caption }}</p>
                                </div>
                                
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center text-sm text-gray-500">
                                        <svg class="w-4 h-4 mr-1 text-yellow-500" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                                        </svg>
                                        <span class="font-medium">{{ photo.votes_count || 0 }}</span>
                                    </div>
                                    
                                    <div class="text-xs text-gray-400">
                                        {{ formatDate(photo.created_at) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal de Foto Ampliada -->
        <PhotoModal 
            :show="showPhotoModal"
            :photo="selectedPhoto"
            :is-voted="selectedPhoto ? isPhotoVoted(selectedPhoto.id) : false"
            :can-vote="canVoteOnPhoto"
            :show-navigation="true"
            :has-previous="currentPhotoIndex > 0"
            :has-next="currentPhotoIndex < filteredPhotos.length - 1"
            @close="closePhotoModal"
            @vote="handlePhotoVote"
            @unvote="handlePhotoUnvote"
            @previous="navigatePhoto(-1)"
            @next="navigatePhoto(1)"
        />

        <!-- Toast de feedback -->
        <div v-if="showToast" 
             :class="toastType === 'success' ? 'bg-green-500' : 'bg-red-500'"
             class="fixed top-4 right-4 text-white px-6 py-3 rounded-lg shadow-lg z-50 transition-all duration-300">
            {{ toastMessage }}
        </div>
    </VotingLayout>
</template>

<script>
import { Head, Link } from '@inertiajs/vue3';
import { router } from '@inertiajs/vue3';
import VotingLayout from '@/Layouts/VotingLayout.vue';
import PhotoModal from '@/Components/PhotoModal.vue';

export default {
    components: {
        Head,
        Link,
        VotingLayout,
        PhotoModal
    },
    props: {
        photos: Array,
        userVotes: Array,
        projects: Array
    },
    data() {
        return {
            selectedProject: '',
            showPhotoModal: false,
            selectedPhoto: null,
            currentPhotoIndex: 0,
            showToast: false,
            toastMessage: '',
            toastType: 'success',
            // Tornar userVotes reativo
            userVotes: [...(this.userVotes || [])]
        }
    },
    computed: {
        filteredPhotos() {
            if (!this.selectedProject) {
                return this.photos;
            }
            return this.photos.filter(photo => photo.project_id == this.selectedProject);
        },
        canVoteOnPhoto() {
            if (!this.selectedPhoto) return false;
            
            // Se já votou nesta foto, pode remover o voto
            if (this.isPhotoVoted(this.selectedPhoto.id)) {
                return true;
            }
            
            // Se não votou, pode votar se não atingiu o limite
            return this.userVotes.length < 10;
        }
    },
    mounted() {
        // Inicializar userVotes corretamente
        this.userVotes = [...(this.$page.props.userVotes || [])];
        
        // Debug para verificar os dados
        console.log('Props userVotes:', this.$page.props.userVotes);
        console.log('Data userVotes:', this.userVotes);
        console.log('Total de fotos:', this.photos.length);
        
        // Verificar se há inconsistências
        if (this.userVotes.length > 0) {
            console.log('Usuário tem votos em:', this.userVotes);
            this.userVotes.forEach(photoId => {
                const photo = this.photos.find(p => p.id === photoId);
                if (photo) {
                    console.log(`Foto ${photoId} (${photo.caption}) - Votada`);
                }
            });
        }
    },
    methods: {
        filterPhotos() {
            // Método chamado quando o filtro de projeto muda
            // A computed property filteredPhotos já cuida da filtragem
        },
        
        isPhotoVoted(photoId) {
            return this.userVotes.includes(photoId);
        },

        canVoteDirectly(photoId) {
            // Se já votou nesta foto, pode remover o voto
            if (this.isPhotoVoted(photoId)) {
                return true;
            }
            
            // Se não votou, pode votar se não atingiu o limite
            return this.userVotes.length < 10;
        },

        getVoteButtonTitle(photoId) {
            if (this.isPhotoVoted(photoId)) {
                return 'Clique para remover seu voto';
            }
            
            if (this.userVotes.length >= 10) {
                return 'Limite de 10 votos atingido';
            }
            
            return 'Clique para votar nesta foto';
        },

        async handleDirectVote(photoId) {
            if (this.isPhotoVoted(photoId)) {
                // Se já votou, remove o voto
                await this.handlePhotoUnvote(photoId);
            } else {
                // Se não votou, adiciona o voto
                await this.handlePhotoVote(photoId);
            }
        },
        
        openPhotoModal(photo) {
            this.selectedPhoto = photo;
            this.currentPhotoIndex = this.filteredPhotos.findIndex(p => p.id === photo.id);
            this.showPhotoModal = true;
        },
        
        closePhotoModal() {
            this.showPhotoModal = false;
            this.selectedPhoto = null;
            this.currentPhotoIndex = 0;
        },
        
        navigatePhoto(direction) {
            const newIndex = this.currentPhotoIndex + direction;
            if (newIndex >= 0 && newIndex < this.filteredPhotos.length) {
                this.currentPhotoIndex = newIndex;
                this.selectedPhoto = this.filteredPhotos[newIndex];
            }
        },
        
        async handlePhotoVote(photoId) {
            try {
                const response = await router.post(route('voting.vote', photoId), {}, {
                    preserveState: true,
                    preserveScroll: true,
                    onSuccess: (page) => {
                        // Atualiza os votos do usuário com os dados mais recentes do servidor
                        if (page.props && page.props.flash && page.props.flash.userVotes) {
                            this.userVotes = [...page.props.flash.userVotes];
                        } else if (page.props && page.props.userVotes) {
                            this.userVotes = [...page.props.userVotes];
                        } else {
                            // Fallback: adiciona o voto localmente se não receber dados do servidor
                            if (!this.userVotes.includes(photoId)) {
                                this.userVotes.push(photoId);
                            }
                        }
                        
                        // Atualiza o contador de votos da foto
                        const photo = this.photos.find(p => p.id === photoId);
                        if (photo) {
                            photo.votes_count = (photo.votes_count || 0) + 1;
                        }
                        
                        this.showToastMessage('❤️ Voto adicionado!', 'success');
                    },
                    onError: (errors) => {
                        console.error('Erro ao votar:', errors);
                        let errorMessage = 'Erro ao registrar voto. Tente novamente.';
                        
                        // Verifica se há mensagens de erro específicas
                        if (errors && typeof errors === 'object') {
                            const firstError = Object.values(errors)[0];
                            if (Array.isArray(firstError) && firstError.length > 0) {
                                errorMessage = firstError[0];
                            } else if (typeof firstError === 'string') {
                                errorMessage = firstError;
                            }
                        }
                        
                        this.showToastMessage(errorMessage, 'error');
                    }
                });
            } catch (error) {
                console.error('Erro ao processar voto:', error);
                this.showToastMessage('Erro ao processar voto. Tente novamente.', 'error');
            }
        },
        
        async handlePhotoUnvote(photoId) {
            try {
                const response = await router.delete(route('voting.unvote', photoId), {
                    preserveState: true,
                    preserveScroll: true,
                    onSuccess: (page) => {
                        // Atualiza os votos do usuário com os dados mais recentes do servidor
                        if (page.props && page.props.flash && page.props.flash.userVotes) {
                            this.userVotes = [...page.props.flash.userVotes];
                        } else if (page.props && page.props.userVotes) {
                            this.userVotes = [...page.props.userVotes];
                        } else {
                            // Fallback: remove o voto localmente se não receber dados do servidor
                            const index = this.userVotes.indexOf(photoId);
                            if (index > -1) {
                                this.userVotes.splice(index, 1);
                            }
                        }
                        
                        // Atualiza o contador de votos da foto
                        const photo = this.photos.find(p => p.id === photoId);
                        if (photo && photo.votes_count > 0) {
                            photo.votes_count = photo.votes_count - 1;
                        }
                        
                        this.showToastMessage('💔 Voto removido!', 'success');
                    },
                    onError: (errors) => {
                        console.error('Erro ao remover voto:', errors);
                        let errorMessage = 'Erro ao remover voto. Tente novamente.';
                        
                        // Verifica se há mensagens de erro específicas
                        if (errors && typeof errors === 'object') {
                            const firstError = Object.values(errors)[0];
                            if (Array.isArray(firstError) && firstError.length > 0) {
                                errorMessage = firstError[0];
                            } else if (typeof firstError === 'string') {
                                errorMessage = firstError;
                            }
                        }
                        
                        this.showToastMessage(errorMessage, 'error');
                    }
                });
            } catch (error) {
                console.error('Erro ao processar remoção de voto:', error);
                this.showToastMessage('Erro ao processar remoção de voto. Tente novamente.', 'error');
            }
        },
        
        showToastMessage(message, type = 'success') {
            this.toastMessage = message;
            this.toastType = type;
            this.showToast = true;
            
            setTimeout(() => {
                this.showToast = false;
            }, 3000);
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

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>