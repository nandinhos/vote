<template>
    <Head title="Projetos" />

    <AdminLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Projetos
                </h2>
                <Link
                    :href="route('admin.projects.create')"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                >
                    <PlusIcon class="w-4 h-4 mr-2" />
                    Novo Projeto
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
                                        placeholder="Buscar projetos..."
                                        class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                                        @input="handleSearch"
                                    >
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <MagnifyingGlassIcon class="h-5 w-5 text-gray-400" />
                                    </div>
                                </div>
                            </div>
                            <div>
                                <select
                                    v-model="statusFilter"
                                    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                    @change="handleFilter"
                                >
                                    <option value="">Todos os Status</option>
                                    <option value="active">Ativos</option>
                                    <option value="inactive">Inativos</option>
                                </select>
                            </div>
                        </div>

                        <!-- Tabela -->
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Nome
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Descrição
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Status
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Fotos
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Votos
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Criado em
                                        </th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Ações
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="project in projects.data" :key="project.id" class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ project.name }}</div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm text-gray-900 max-w-xs truncate">{{ project.description || 'Sem descrição' }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" :class="project.is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'">
                                                {{ project.is_active ? 'Ativo' : 'Inativo' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ project.photos_count }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ project.votes_count }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ formatDate(project.created_at) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="flex justify-end space-x-3">
                                                <Link
                                                    :href="route('admin.projects.show', project.id)"
                                                    class="inline-flex items-center justify-center w-8 h-8 text-indigo-600 hover:text-indigo-900 hover:bg-indigo-50 rounded-md transition-colors duration-200"
                                                    title="Ver projeto"
                                                >
                                                    <EyeIcon class="w-4 h-4" />
                                                </Link>
                                                <Link
                                                    :href="route('admin.projects.edit', project.id)"
                                                    class="inline-flex items-center justify-center w-8 h-8 text-blue-600 hover:text-blue-900 hover:bg-blue-50 rounded-md transition-colors duration-200"
                                                    title="Editar projeto"
                                                >
                                                    <PencilIcon class="w-4 h-4" />
                                                </Link>
                                                <button
                                                    @click="confirmDelete(project)"
                                                    class="inline-flex items-center justify-center w-8 h-8 text-red-600 hover:text-red-900 hover:bg-red-50 rounded-md transition-colors duration-200"
                                                    title="Excluir projeto"
                                                >
                                                    <TrashIcon class="w-4 h-4" />
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Paginação -->
                        <div class="mt-6" v-if="projects.links.length > 3">
                            <nav class="flex items-center justify-between">
                                <div class="flex justify-between flex-1 sm:hidden">
                                    <Link
                                        v-if="projects.prev_page_url"
                                        :href="projects.prev_page_url"
                                        class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:text-gray-400"
                                    >
                                        Anterior
                                    </Link>
                                    <Link
                                        v-if="projects.next_page_url"
                                        :href="projects.next_page_url"
                                        class="relative ml-3 inline-flex items-center px-4 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:text-gray-400"
                                    >
                                        Próximo
                                    </Link>
                                </div>
                                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                                    <div>
                                        <p class="text-sm text-gray-700">
                                            Mostrando
                                            <span class="font-medium">{{ projects.from }}</span>
                                            a
                                            <span class="font-medium">{{ projects.to }}</span>
                                            de
                                            <span class="font-medium">{{ projects.total }}</span>
                                            resultados
                                        </p>
                                    </div>
                                    <div>
                                        <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                                            <template v-for="(link, index) in projects.links" :key="index">
                                                <Link
                                                    v-if="link.url"
                                                    :href="link.url"
                                                    class="relative inline-flex items-center px-2 py-2 text-sm font-medium"
                                                    :class="[
                                                        link.active
                                                            ? 'z-10 bg-indigo-50 border-indigo-500 text-indigo-600'
                                                            : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50',
                                                        index === 0 ? 'rounded-l-md' : '',
                                                        index === projects.links.length - 1 ? 'rounded-r-md' : '',
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
                                                        index === projects.links.length - 1 ? 'rounded-r-md' : ''
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
                        <div v-if="projects.data.length === 0" class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Nenhum projeto encontrado</h3>
                            <p class="mt-1 text-sm text-gray-500">Comece criando um novo projeto.</p>
                            <div class="mt-6">
                                <Link
                                    :href="route('admin.projects.create')"
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                                >
                                    Novo Projeto
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
                    Tem certeza que deseja excluir este projeto?
                </h2>

                <p class="mt-1 text-sm text-gray-600">
                    Esta ação não pode ser desfeita. Todas as fotos e votos associados a este projeto também serão excluídos.
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