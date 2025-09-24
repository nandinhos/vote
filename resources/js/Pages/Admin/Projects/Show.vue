<template>
    <Head :title="project.name" />

    <AdminLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <Link
                        :href="route('admin.projects.index')"
                        class="text-gray-500 hover:text-gray-700"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </Link>
                    <div>
                        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                            {{ project.name }}
                        </h2>
                        <div class="flex items-center space-x-2 mt-1">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" :class="project.is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'">
                                {{ project.is_active ? 'Ativo' : 'Inativo' }}
                            </span>
                            <span class="text-sm text-gray-500">Criado em {{ formatDate(project.created_at) }}</span>
                        </div>
                    </div>
                </div>
                <div class="flex space-x-2">
                    <Link
                        :href="route('admin.projects.edit', project.id)"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                    >
                        Editar
                    </Link>
                    <Link
                        :href="route('admin.photos.create', { project_id: project.id })"
                        class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150"
                    >
                        Adicionar Foto
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <!-- Descrição do Projeto -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg" v-if="project.description">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Descrição</h3>
                        <p class="text-gray-700">{{ project.description }}</p>
                    </div>
                </div>

                <!-- Estatísticas -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-purple-500 rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-500">Total de Fotos</div>
                                    <div class="text-2xl font-bold text-gray-900">{{ project.photos_count }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-red-500 rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-500">Total de Votos</div>
                                    <div class="text-2xl font-bold text-gray-900">{{ project.votes_count }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-yellow-500 rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-500">Foto Mais Votada</div>
                                    <div class="text-2xl font-bold text-gray-900">{{ topPhoto ? topPhoto.vote_count : 0 }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-indigo-500 rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-500">Votantes Únicos</div>
                                    <div class="text-2xl font-bold text-gray-900">{{ project.unique_voters_count }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Fotos do Projeto -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-lg font-medium text-gray-900">Fotos do Projeto</h3>
                            <Link
                                :href="route('admin.photos.create', { project_id: project.id })"
                                class="inline-flex items-center px-3 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150"
                            >
                                Adicionar Foto
                            </Link>
                        </div>

                        <div v-if="photos.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                            <div v-for="photo in photos" :key="photo.id" class="group relative bg-white border border-gray-200 rounded-lg overflow-hidden hover:shadow-lg transition-shadow duration-200">
                                <div class="aspect-w-1 aspect-h-1">
                                    <img
                                        :src="`/storage/${photo.file_path}`"
                                        :alt="photo.caption"
                                        class="w-full h-48 object-cover group-hover:opacity-75 transition-opacity duration-200"
                                    >
                                </div>
                                <div class="p-4">
                                    <div class="flex items-center justify-between mb-2">
                                        <div class="flex items-center space-x-1">
                                            <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                            </svg>
                                            <span class="text-sm font-medium text-gray-900">{{ photo.vote_count }}</span>
                                        </div>
                                        <div class="flex space-x-1">
                                            <Link
                                                :href="route('admin.photos.edit', photo.id)"
                                                class="text-blue-600 hover:text-blue-900 text-sm"
                                            >
                                                Editar
                                            </Link>
                                            <button
                                                @click="confirmDeletePhoto(photo)"
                                                class="text-red-600 hover:text-red-900 text-sm"
                                            >
                                                Excluir
                                            </button>
                                        </div>
                                    </div>
                                    <p class="text-sm text-gray-600 truncate" v-html="photo.caption || '&nbsp;'"></p>
                                    <p class="text-xs text-gray-500 mt-1">Adicionada em {{ formatDate(photo.created_at) }}</p>
                                </div>
                            </div>
                        </div>

                        <div v-else class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Nenhuma foto encontrada</h3>
                            <p class="mt-1 text-sm text-gray-500">Comece adicionando uma foto a este projeto.</p>
                            <div class="mt-6">
                                <Link
                                    :href="route('admin.photos.create', { project_id: project.id })"
                                    class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150"
                                >
                                    Adicionar Primeira Foto
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal de Confirmação de Exclusão de Foto -->
        <Modal :show="showDeletePhotoModal" @close="showDeletePhotoModal = false">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900">
                    Tem certeza que deseja excluir esta foto?
                </h2>

                <p class="mt-1 text-sm text-gray-600">
                    Esta ação não pode ser desfeita. Todos os votos associados a esta foto também serão excluídos.
                </p>

                <div class="mt-6 flex justify-end space-x-3">
                    <SecondaryButton @click="showDeletePhotoModal = false">
                        Cancelar
                    </SecondaryButton>

                    <DangerButton @click="deletePhoto" :disabled="processing">
                        Excluir Foto
                    </DangerButton>
                </div>
            </div>
        </Modal>
    </AdminLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';

const props = defineProps({
    project: Object,
    photos: Array
});

const showDeletePhotoModal = ref(false);
const photoToDelete = ref(null);
const processing = ref(false);

const topPhoto = computed(() => {
    if (props.photos.length === 0) return null;
    return props.photos.reduce((max, photo) => 
        photo.vote_count > max.vote_count ? photo : max
    );
});

const confirmDeletePhoto = (photo) => {
    photoToDelete.value = photo;
    showDeletePhotoModal.value = true;
};

const deletePhoto = () => {
    processing.value = true;
    router.delete(route('admin.photos.destroy', photoToDelete.value.id), {
        onSuccess: () => {
            showDeletePhotoModal.value = false;
            photoToDelete.value = null;
        },
        onFinish: () => {
            processing.value = false;
        }
    });
};

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('pt-BR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    });
};
</script>