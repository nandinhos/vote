<template>
    <AdminLayout>
        <Head title="Gerenciar Usuários" />

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Estatísticas -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                                        <UsersIcon class="w-5 h-5 text-white" />
                                    </div>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">Total de Usuários</dt>
                                        <dd class="text-lg font-medium text-gray-900">{{ stats.total_users }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-yellow-500 rounded-full flex items-center justify-center">
                                        <UserIcon class="w-5 h-5 text-white" />
                                    </div>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">Votantes</dt>
                                        <dd class="text-lg font-medium text-gray-900">{{ stats.total_voters }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-gray-500 rounded-full flex items-center justify-center">
                                        <ShieldCheckIcon class="w-5 h-5 text-white" />
                                    </div>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">Administradores</dt>
                                        <dd class="text-lg font-medium text-gray-900">{{ stats.total_admins }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabela de Usuários -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div class="flex justify-between items-center mb-6">
                            <h2 class="text-2xl font-semibold text-gray-800">Gerenciar Usuários</h2>
                            <Link :href="route('admin.users.create')" class="inline-flex items-center px-4 py-2 bg-blue-500 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200">
                                <PlusIcon class="w-5 h-5 mr-2" />
                                Criar Usuário
                            </Link>
                        </div>

                        <!-- Filtros e Busca -->
                        <div class="mb-6 flex flex-col sm:flex-row gap-4">
                            <div class="flex-1">
                                <div class="relative">
                                    <input v-model="search" 
                                           @input="performSearch"
                                           type="text" 
                                           placeholder="Buscar por nome ou SARAM..." 
                                           class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <MagnifyingGlassIcon class="h-5 w-5 text-gray-400" />
                                    </div>
                                </div>
                            </div>
                            <div>
                                <select
                                    v-model="roleFilter"
                                    @change="performSearch"
                                    class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                >
                                    <option value="">Todas as funções</option>
                                    <option value="voter">Votantes</option>
                                    <option value="admin">Administradores</option>
                                </select>
                            </div>
                        </div>

                        <!-- Tabela -->
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Usuário
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            SARAM
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Função
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Cadastro
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Ações
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="user in users.data" :key="user.id" class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ user.name }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ user.saram }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span :class="user.role === 'admin' ? 'bg-gray-100 text-gray-800' : 'bg-yellow-100 text-yellow-800'" 
                                                  class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                                                {{ user.role === 'admin' ? 'Administrador' : 'Votante' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ new Date(user.created_at).toLocaleDateString('pt-BR') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex space-x-3">
                                                <Link :href="route('admin.users.show', user.id)" 
                                                      class="inline-flex items-center justify-center w-8 h-8 text-blue-600 hover:text-blue-900 hover:bg-blue-50 rounded-md transition-colors duration-200"
                                                      title="Ver usuário">
                                                    <EyeIcon class="w-4 h-4" />
                                                </Link>
                                                <Link :href="route('admin.users.edit', user.id)" 
                                                      class="inline-flex items-center justify-center w-8 h-8 text-indigo-600 hover:text-indigo-900 hover:bg-indigo-50 rounded-md transition-colors duration-200"
                                                      title="Editar usuário">
                                                    <PencilIcon class="w-4 h-4" />
                                                </Link>
                                                <button @click="confirmDelete(user)" 
                                                        class="inline-flex items-center justify-center w-8 h-8 text-red-600 hover:text-red-900 hover:bg-red-50 rounded-md transition-colors duration-200"
                                                        title="Excluir usuário">
                                                    <TrashIcon class="w-4 h-4" />
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Paginação -->
                        <div class="mt-6" v-if="users.links.length > 3">
                            <nav class="flex items-center justify-between">
                                <div class="flex-1 flex justify-between sm:hidden">
                                    <Link v-if="users.prev_page_url" :href="users.prev_page_url" 
                                          class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                        Anterior
                                    </Link>
                                    <Link v-if="users.next_page_url" :href="users.next_page_url" 
                                          class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                        Próximo
                                    </Link>
                                </div>
                                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                                    <div>
                                        <p class="text-sm text-gray-700">
                                            Mostrando {{ users.from }} a {{ users.to }} de {{ users.total }} resultados
                                        </p>
                                    </div>
                                    <div>
                                        <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                                            <template v-for="(link, index) in users.links" :key="index">
                                                <Link v-if="link.url" :href="link.url" 
                                                      :class="[
                                                          'relative inline-flex items-center px-4 py-2 border text-sm font-medium',
                                                          link.active 
                                                              ? 'z-10 bg-blue-50 border-blue-500 text-blue-600' 
                                                              : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50'
                                                      ]"
                                                      v-html="link.label">
                                                </Link>
                                                <span v-else 
                                                      :class="[
                                                          'relative inline-flex items-center px-4 py-2 border text-sm font-medium',
                                                          'bg-white border-gray-300 text-gray-300 cursor-default'
                                                      ]"
                                                      v-html="link.label">
                                                </span>
                                            </template>
                                        </nav>
                                    </div>
                                </div>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal de Confirmação de Exclusão -->
        <div v-if="showDeleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                <div class="mt-3 text-center">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                        <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mt-2">Confirmar Exclusão</h3>
                    <div class="mt-2 px-7 py-3">
                        <p class="text-sm text-gray-500">
                            Tem certeza que deseja excluir o usuário <strong>{{ userToDelete?.name }}</strong>? 
                            Esta ação não pode ser desfeita.
                        </p>
                    </div>
                    <div class="items-center px-4 py-3">
                        <button @click="deleteUser" 
                                class="px-4 py-2 bg-red-500 text-white text-base font-medium rounded-md w-24 mr-2 hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-300">
                            Excluir
                        </button>
                        <button @click="showDeleteModal = false" 
                                class="px-4 py-2 bg-gray-500 text-white text-base font-medium rounded-md w-24 hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-300">
                            Cancelar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { 
    EyeIcon, 
    PencilIcon, 
    TrashIcon, 
    PlusIcon,
    MagnifyingGlassIcon,
    UsersIcon,
    UserIcon,
    ShieldCheckIcon
} from '@heroicons/vue/24/outline';

const props = defineProps({
    users: Object,
    filters: Object,
    stats: Object,
});

const search = ref(props.filters.search || '');
const roleFilter = ref(props.filters.role || '');
const showDeleteModal = ref(false);
const userToDelete = ref(null);

const performSearch = () => {
    router.get(route('admin.users.index'), {
        search: search.value,
        role: roleFilter.value,
    }, {
        preserveState: true,
        replace: true,
    });
};

const confirmDelete = (user) => {
    userToDelete.value = user;
    showDeleteModal.value = true;
};

const deleteUser = () => {
    router.delete(route('admin.users.destroy', userToDelete.value.id), {
        onSuccess: () => {
            showDeleteModal.value = false;
            userToDelete.value = null;
        }
    });
};
</script>