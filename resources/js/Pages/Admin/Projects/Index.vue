<template>

    <Head title="Projetos" />

    <AdminLayout>
        <template #header>
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Projetos
                </h2>
                <div class="fixed top-4 right-16 flex space-x-2">
                    <Link :href="route('admin.projects.create')"
                        class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-medium text-sm text-white hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-all duration-200 shadow-sm">
                    <PlusIcon class="w-4 h-4 mr-2" />
                    Novo Projeto
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-8 min-h-screen">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100">
                    <div class="p-6">
                        <!-- Filtros -->
                        <div class="mb-6 flex flex-col sm:flex-row gap-3">
                            <div class="flex-1">
                                <div class="relative">
                                    <input v-model="search" type="text" placeholder="Buscar projetos por nome..."
                                        class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-gray-500 focus:border-gray-500 bg-white text-sm placeholder-gray-400 transition-colors"
                                        @input="handleSearch">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <MagnifyingGlassIcon class="h-4 w-4 text-gray-400" />
                                    </div>
                                </div>
                            </div>
                            <div class="sm:w-48">
                                <select v-model="statusFilter"
                                    class="w-full border border-gray-200 rounded-lg focus:ring-2 focus:ring-gray-500 focus:border-gray-500 bg-white text-sm py-2.5 px-3 transition-colors"
                                    @change="handleFilter">
                                    <option value="">Todos os Status</option>
                                    <option value="1">Ativo</option>
                                    <option value="0">Inativo</option>
                                </select>
                            </div>
                            <div v-if="search || statusFilter">
                                <button @click="clearFilters"
                                    class="px-4 py-2.5 text-sm text-gray-600 hover:text-gray-800 border border-gray-200 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-gray-500 transition-colors"
                                    title="Limpar filtros">
                                    Limpar
                                </button>
                            </div>
                        </div>

                        <!-- Tabela -->
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-100">
                                <thead class="bg-gray-25">
                                    <tr>
                                        <th scope="col"
                                            class="px-6 py-4 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                                            Nome
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-4 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                                            Descrição
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-4 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                                            Status
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-4 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                                            Fotos
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-4 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                                            Votos
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-4 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                                            Ações
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-50">
                                    <tr v-for="project in projects.data" :key="project.id"
                                        class="hover:bg-gray-25 transition-colors duration-200">
                                        <td class="px-6 py-5 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ project.name }}</div>
                                        </td>
                                        <td class="px-6 py-5">
                                            <div class="text-sm text-gray-600 max-w-xs truncate">{{ project.description
                                            }}</div>
                                        </td>
                                        <td class="px-6 py-5 whitespace-nowrap">
                                            <span v-if="project.is_active"
                                                class="inline-flex items-center px-3 py-1 text-xs font-medium rounded-full bg-green-50 text-green-700">
                                                <div class="w-1.5 h-1.5 bg-green-500 rounded-full mr-1.5"></div>
                                                Ativo
                                            </span>
                                            <span v-else
                                                class="inline-flex items-center px-3 py-1 text-xs font-medium rounded-full bg-red-50 text-red-700">
                                                <div class="w-1.5 h-1.5 bg-red-500 rounded-full mr-1.5"></div>
                                                Inativo
                                            </span>
                                        </td>
                                        <td class="px-6 py-5 whitespace-nowrap text-sm text-gray-600">
                                            {{ project.photos_count || 0 }}
                                        </td>
                                        <td class="px-6 py-5 whitespace-nowrap text-sm text-gray-600">
                                            {{ project.votes_count || 0 }}
                                        </td>
                                        <td class="px-6 py-5 whitespace-nowrap text-sm font-medium">
                                            <div class="flex items-center space-x-2">
                                                <Link :href="route('admin.projects.show', project.id)"
                                                    class="inline-flex items-center justify-center w-8 h-8 text-indigo-600 hover:text-indigo-900 hover:bg-indigo-50 rounded-md transition-colors duration-200"
                                                    title="Ver projeto">
                                                <EyeIcon class="w-4 h-4" />
                                                </Link>
                                                <Link :href="route('admin.projects.edit', project.id)"
                                                    class="inline-flex items-center justify-center w-8 h-8 text-blue-600 hover:text-blue-900 hover:bg-blue-50 rounded-md transition-colors duration-200"
                                                    title="Editar projeto">
                                                <PencilIcon class="w-4 h-4" />
                                                </Link>
                                                <button @click="confirmDelete(project)"
                                                    class="inline-flex items-center justify-center w-8 h-8 text-red-600 hover:text-red-900 hover:bg-red-50 rounded-md transition-colors duration-200"
                                                    title="Excluir projeto">
                                                    <TrashIcon class="w-4 h-4" />
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Paginação -->
                        <div v-if="projects.links && projects.links.length > 3"
                            class="mt-8 px-6 py-4 bg-gray-50/50 border-t border-gray-100">
                            <nav class="flex items-center justify-between">
                                <div class="flex-1 flex justify-between sm:hidden">
                                    <Link v-if="projects.prev_page_url" :href="projects.prev_page_url"
                                        class="relative inline-flex items-center px-4 py-2 border border-gray-200 text-sm font-medium rounded-lg text-gray-600 bg-white hover:bg-gray-50 transition-colors duration-200">
                                    Anterior
                                    </Link>
                                    <Link v-if="projects.next_page_url" :href="projects.next_page_url"
                                        class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-200 text-sm font-medium rounded-lg text-gray-600 bg-white hover:bg-gray-50 transition-colors duration-200">
                                    Próximo
                                    </Link>
                                </div>
                                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                                    <div>
                                        <p class="text-sm text-gray-600">
                                            Mostrando
                                            <span class="font-semibold text-gray-900">{{ projects.from }}</span>
                                            até
                                            <span class="font-semibold text-gray-900">{{ projects.to }}</span>
                                            de
                                            <span class="font-semibold text-gray-900">{{ projects.total }}</span>
                                            resultados
                                        </p>
                                    </div>
                                    <div>
                                        <nav class="relative z-0 inline-flex rounded-lg shadow-sm -space-x-px"
                                            aria-label="Pagination">
                                            <template v-for="(link, index) in projects.links" :key="index">
                                                <Link v-if="link.url" :href="link.url" v-html="link.label"
                                                    class="relative inline-flex items-center px-3 py-2 border text-sm font-medium transition-colors duration-200"
                                                    :class="[
                                                        link.active
                                                            ? 'z-10 bg-indigo-50 border-indigo-200 text-indigo-700'
                                                            : 'bg-white border-gray-200 text-gray-600 hover:bg-gray-50',
                                                        index === 0 ? 'rounded-l-lg' : '',
                                                        index === projects.links.length - 1 ? 'rounded-r-lg' : ''
                                                    ]" />
                                                <span v-else v-html="link.label"
                                                    class="relative inline-flex items-center px-3 py-2 border border-gray-200 bg-white text-sm font-medium text-gray-400" />
                                            </template>
                                        </nav>
                                    </div>
                                </div>
                            </nav>
                        </div>

                        <!-- Estado vazio -->
                        <div v-if="projects.data.length === 0" class="text-center py-16">
                            <div
                                class="mx-auto h-16 w-16 rounded-full bg-gray-100 flex items-center justify-center mb-4">
                                <FolderIcon class="h-8 w-8 text-gray-400" />
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Nenhum projeto encontrado</h3>
                            <p class="text-sm text-gray-500 mb-6">Comece criando um novo projeto para organizar suas
                                fotos e
                                votações.</p>
                            <Link :href="route('admin.projects.create')"
                                class="inline-flex items-center px-6 py-3 bg-indigo-600 border border-transparent rounded-lg font-medium text-sm text-white hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-all duration-200 shadow-sm">
                            <PlusIcon class="w-4 h-4 mr-2" />
                            Novo Projeto
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal de Confirmação de Exclusão -->
        <Modal :show="showDeleteModal" @close="showDeleteModal = false">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900">
                    Tem certeza que deseja excluir este projeto?
                </h2>

                <p class="mt-1 text-sm text-gray-600">
                    Esta ação não pode ser desfeita. Todas as fotos e votos associados a este projeto também serão
                    excluídos.
                </p>

                <div class="mt-6 flex justify-end space-x-3">
                    <SecondaryButton @click="showDeleteModal = false">
                        Cancelar
                    </SecondaryButton>

                    <DangerButton @click="deleteProject" :disabled="processing">
                        Excluir Projeto
                    </DangerButton>
                </div>
            </div>
        </Modal>
    </AdminLayout>
</template>

<script setup>
import { ref, watch } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import {
    EyeIcon,
    PencilIcon,
    TrashIcon,
    PlusIcon,
    MagnifyingGlassIcon,
    FolderIcon,
    CheckCircleIcon,
    XCircleIcon
} from '@heroicons/vue/24/outline';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';

const props = defineProps({
    projects: Object,
    filters: Object
});

const search = ref(props.filters.search || '');
const statusFilter = ref(props.filters.status || '');
const showDeleteModal = ref(false);
const projectToDelete = ref(null);
const processing = ref(false);

let searchTimeout = null;

const handleSearch = () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        router.get(route('admin.projects.index'), {
            search: search.value,
            status: statusFilter.value
        }, {
            preserveState: true,
            replace: true
        });
    }, 300);
};

const handleFilter = () => {
    router.get(route('admin.projects.index'), {
        search: search.value,
        status: statusFilter.value
    }, {
        preserveState: true,
        replace: true
    });
};

const clearFilters = () => {
    search.value = '';
    statusFilter.value = '';
    router.get(route('admin.projects.index'), {}, {
        preserveState: true,
        replace: true
    });
};

const confirmDelete = (project) => {
    projectToDelete.value = project;
    showDeleteModal.value = true;
};

const deleteProject = () => {
    processing.value = true;
    router.delete(route('admin.projects.destroy', projectToDelete.value.id), {
        onSuccess: () => {
            showDeleteModal.value = false;
            projectToDelete.value = null;
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