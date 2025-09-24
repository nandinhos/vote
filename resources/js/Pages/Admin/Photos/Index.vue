<template>
    <Head title="Fotos" />

    <AdminLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Fotos
                </h2>
                <Link
                    :href="route('admin.photos.create')"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                >
                    <PlusIcon class="w-4 h-4 mr-2" />
                    Nova Foto
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <!-- Filtros -->
                        <div class="mb-6 flex flex-col sm:flex-row gap-4">
                            <div class="flex-1">
                                <div class="relative">
                                    <input
                                        v-model="search"
                                        type="text"
                                        placeholder="Buscar fotos por legenda..."
                                        class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                                        @input="handleSearch"
                                    >
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <MagnifyingGlassIcon class="h-5 w-5 text-gray-400" />
                                    </div>
                                </div>
                            </div>
                            <div class="flex gap-2">
                                <select
                                    v-model="projectFilter"
                                    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                    @change="handleFilter"
                                >
                                    <option value="">Todos os Projetos</option>
                                    <option v-for="project in projects" :key="project.id" :value="project.id">
                                        {{ project.name }}
                                    </option>
                                </select>
                                <button
                                    v-if="search || projectFilter"
                                    @click="clearFilters"
                                    class="px-3 py-2 text-sm text-gray-600 hover:text-gray-800 border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    title="Limpar filtros"
                                >
                                    Limpar
                                </button>
                            </div>
                        </div>

                        <!-- Grid de Fotos -->
                        <div v-if="photos.data.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                            <div v-for="photo in photos.data" :key="photo.id" class="group relative bg-white border border-gray-200 rounded-lg overflow-hidden hover:shadow-lg transition-shadow duration-200">
                                <div class="aspect-w-1 aspect-h-1">
                                    <img
                                        :src="`/storage/${photo.file_path}`"
                                        :alt="photo.caption"
                                        class="w-full h-48 object-cover group-hover:opacity-75 transition-opacity duration-200"
                                    >
                                </div>
                                <div class="p-4">
                                    <div class="flex items-center justify-between mb-2">
                                        <div class="flex items-center space-x-2 bg-yellow-50 px-3 py-1 rounded-full border border-yellow-200">
                                            <StarIcon class="w-4 h-4 text-yellow-500 fill-current" />
                                            <span class="text-sm font-bold text-yellow-700">{{ photo.votes_count || 0 }}</span>
                                            <span class="text-xs text-yellow-600">{{ photo.votes_count === 1 ? 'voto' : 'votos' }}</span>
                                        </div>
                                        <div class="flex space-x-2">
                                            <Link
                                                :href="route('admin.photos.show', photo.id)"
                                                class="inline-flex items-center justify-center w-8 h-8 text-indigo-600 hover:text-indigo-900 hover:bg-indigo-50 rounded-md transition-colors duration-200"
                                                title="Ver foto"
                                            >
                                                <EyeIcon class="w-4 h-4" />
                                            </Link>
                                            <Link
                                                :href="route('admin.photos.edit', photo.id)"
                                                class="inline-flex items-center justify-center w-8 h-8 text-blue-600 hover:text-blue-900 hover:bg-blue-50 rounded-md transition-colors duration-200"
                                                title="Editar foto"
                                            >
                                                <PencilIcon class="w-4 h-4" />
                                            </Link>
                                            <button
                                                @click="confirmDelete(photo)"
                                                class="inline-flex items-center justify-center w-8 h-8 text-red-600 hover:text-red-900 hover:bg-red-50 rounded-md transition-colors duration-200"
                                                title="Excluir foto"
                                            >
                                                <TrashIcon class="w-4 h-4" />
                                            </button>
                                        </div>
                                    </div>
                                    <p class="text-sm text-gray-900 font-medium truncate mb-1" v-html="photo.caption || '&nbsp;'"></p>
                                    <p class="text-xs text-gray-500 mb-1">
                                        <Link :href="route('admin.projects.show', photo.project.id)" class="hover:text-gray-700">
                                            {{ photo.project.name }}
                                        </Link>
                                    </p>
                                    <p class="text-xs text-gray-500">{{ formatDate(photo.created_at) }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Paginação -->
                        <div class="mt-6" v-if="photos.links.length > 3">
                            <nav class="flex items-center justify-between">
                                <div class="flex justify-between flex-1 sm:hidden">
                                    <Link
                                        v-if="photos.prev_page_url"
                                        :href="photos.prev_page_url"
                                        class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:text-gray-400"
                                    >
                                        Anterior
                                    </Link>
                                    <Link
                                        v-if="photos.next_page_url"
                                        :href="photos.next_page_url"
                                        class="relative ml-3 inline-flex items-center px-4 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:text-gray-400"
                                    >
                                        Próximo
                                    </Link>
                                </div>
                                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                                    <div>
                                        <p class="text-sm text-gray-700">
                                            Mostrando
                                            <span class="font-medium">{{ photos.from }}</span>
                                            a
                                            <span class="font-medium">{{ photos.to }}</span>
                                            de
                                            <span class="font-medium">{{ photos.total }}</span>
                                            resultados
                                        </p>
                                    </div>
                                    <div>
                                        <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                                            <template v-for="(link, index) in photos.links" :key="index">
                                                <Link
                                                    v-if="link.url"
                                                    :href="link.url"
                                                    class="relative inline-flex items-center px-2 py-2 text-sm font-medium"
                                                    :class="[
                                                        link.active
                                                            ? 'z-10 bg-indigo-50 border-indigo-500 text-indigo-600'
                                                            : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50',
                                                        index === 0 ? 'rounded-l-md' : '',
                                                        index === photos.links.length - 1 ? 'rounded-r-md' : '',
                                                        'border'
                                                    ]"
                                                    v-html="link.label"
                                                >
                                                </Link>
                                                <span
                                                    v-else
                                                    class="relative inline-flex items-center px-2 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 cursor-default"
                                                    :class="[
                                                        index === 0 ? 'rounded-l-md' : '',
                                                        index === photos.links.length - 1 ? 'rounded-r-md' : ''
                                                    ]"
                                                    v-html="link.label"
                                                >
                                                </span>
                                            </template>
                                        </nav>
                                    </div>
                                </div>
                            </nav>
                        </div>

                        <!-- Estado vazio -->
                        <div v-if="photos.data.length === 0" class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Nenhuma foto encontrada</h3>
                            <p class="mt-1 text-sm text-gray-500">Comece adicionando uma nova foto.</p>
                            <div class="mt-6">
                                <Link
                                    :href="route('admin.photos.create')"
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                                >
                                    Nova Foto
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal de Confirmação de Exclusão -->
        <Modal :show="showDeleteModal" @close="showDeleteModal = false">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900">
                    Tem certeza que deseja excluir esta foto?
                </h2>

                <p class="mt-1 text-sm text-gray-600">
                    Esta ação não pode ser desfeita. Todos os votos associados a esta foto também serão excluídos.
                </p>

                <div class="mt-6 flex justify-end space-x-3">
                    <SecondaryButton @click="showDeleteModal = false">
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
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { 
    EyeIcon, 
    PencilIcon, 
    TrashIcon, 
    PlusIcon, 
    MagnifyingGlassIcon,
    PhotoIcon,
    StarIcon
} from '@heroicons/vue/24/outline';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';

const props = defineProps({
    photos: Object,
    projects: Array,
    filters: Object
});

const search = ref(props.filters.search || '');
const projectFilter = ref(props.filters.project || '');
const showDeleteModal = ref(false);
const photoToDelete = ref(null);
const processing = ref(false);

let searchTimeout = null;

const handleSearch = () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        router.get(route('admin.photos.index'), {
            search: search.value,
            project: projectFilter.value
        }, {
            preserveState: true,
            replace: true
        });
    }, 300);
};

const handleFilter = () => {
    router.get(route('admin.photos.index'), {
        search: search.value,
        project: projectFilter.value
    }, {
        preserveState: true,
        replace: true
    });
};

const clearFilters = () => {
    search.value = '';
    projectFilter.value = '';
    router.get(route('admin.photos.index'), {}, {
        preserveState: true,
        replace: true
    });
};

const confirmDelete = (photo) => {
    photoToDelete.value = photo;
    showDeleteModal.value = true;
};

const deletePhoto = () => {
    processing.value = true;
    router.delete(route('admin.photos.destroy', photoToDelete.value.id), {
        data: {
            search: search.value,
            project: projectFilter.value
        },
        onSuccess: () => {
            showDeleteModal.value = false;
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