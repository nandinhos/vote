<template>

    <Head title="Visualizar Foto" />

    <AdminLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-4">
                    <button @click="goBack" class="text-gray-500 hover:text-gray-700">
                        <ArrowLeftIcon class="w-5 h-5" />
                    </button>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        Visualizar Foto
                    </h2>
                </div>
                <div class="fixed top-4 right-16 flex space-x-2">
                    <Link :href="route('admin.photos.edit', photo.id)"
                        class="inline-flex items-center justify-center w-10 h-10 bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                        title="Editar foto">
                    <PencilIcon class="h-4 w-4 text-white" />
                    </Link>
                    <button @click="confirmDelete"
                        class="inline-flex items-center justify-center w-10 h-10 bg-red-600 border border-transparent rounded-md hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150"
                        title="Excluir foto">
                        <TrashIcon class="h-4 w-4 text-white" />
                    </button>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Foto Principal -->
                    <div class="lg:col-span-2">
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <div class="aspect-w-16 aspect-h-9 mb-4">
                                    <img :src="`/storage/${photo.file_path}`" :alt="photo.caption"
                                        class="w-full h-96 object-contain bg-gray-50 rounded-lg">
                                </div>
                                <div v-if="photo.caption" class="mt-4">
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">Legenda</h3>
                                    <p class="text-gray-700">{{ photo.caption }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Informações da Foto -->
                    <div class="space-y-6">
                        <!-- Estatísticas -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Estatísticas</h3>
                                <div class="space-y-4">
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm font-medium text-gray-500">Total de Votos</span>
                                        <div class="flex items-center">
                                            <StarIcon class="w-5 h-5 text-yellow-400 mr-1" />
                                            <span class="text-2xl font-bold text-gray-900">{{ votes ? votes.total : 0
                                                }}</span>
                                        </div>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm font-medium text-gray-500">Votantes Únicos</span>
                                        <span class="text-lg font-semibold text-gray-900">{{ votes ? votes.data.length :
                                            0
                                            }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Informações do Projeto -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Projeto</h3>
                                <div class="space-y-3">
                                    <div>
                                        <span class="text-sm font-medium text-gray-500">Nome:</span>
                                        <Link :href="route('admin.projects.show', photo.project.id)"
                                            class="block text-indigo-600 hover:text-indigo-900 font-medium">
                                        {{ photo.project.name }}
                                        </Link>
                                    </div>
                                    <div>
                                        <span class="text-sm font-medium text-gray-500">Status:</span>
                                        <span class="block">
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                                :class="photo.project.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                                                {{ photo.project.is_active ? 'Ativo' : 'Inativo' }}
                                            </span>
                                        </span>
                                    </div>
                                    <div v-if="photo.project.description">
                                        <span class="text-sm font-medium text-gray-500">Descrição:</span>
                                        <p class="text-sm text-gray-700 mt-1">{{ photo.project.description }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Informações da Foto -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Detalhes</h3>
                                <div class="space-y-3">
                                    <div>
                                        <span class="text-sm font-medium text-gray-500">Criada em:</span>
                                        <span class="block text-sm text-gray-900">{{ formatDate(photo.created_at)
                                            }}</span>
                                    </div>
                                    <div>
                                        <span class="text-sm font-medium text-gray-500">Última atualização:</span>
                                        <span class="block text-sm text-gray-900">{{ formatDate(photo.updated_at)
                                            }}</span>
                                    </div>
                                    <div>
                                        <span class="text-sm font-medium text-gray-500">Arquivo:</span>
                                        <span class="block text-sm text-gray-900">{{ photo.file_path.split('/').pop()
                                            }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Lista de Votos -->
                <div v-if="votes.data.length > 0" class="mt-8">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Votos Recebidos</h3>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Usuário
                                            </th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                SARAM
                                            </th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Data do Voto
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <tr v-for="vote in votes.data" :key="vote.id">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                {{ vote.user.name }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ vote.user.saram }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ formatDate(vote.created_at) }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Paginação dos Votos -->
                            <div class="mt-6" v-if="votes.links.length > 3">
                                <nav class="flex items-center justify-between">
                                    <div class="flex justify-between flex-1 sm:hidden">
                                        <Link v-if="votes.prev_page_url" :href="votes.prev_page_url"
                                            class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:text-gray-400">
                                        Anterior
                                        </Link>
                                        <Link v-if="votes.next_page_url" :href="votes.next_page_url"
                                            class="relative ml-3 inline-flex items-center px-4 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:text-gray-400">
                                        Próximo
                                        </Link>
                                    </div>
                                    <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                                        <div>
                                            <p class="text-sm text-gray-700">
                                                Mostrando
                                                <span class="font-medium">{{ votes.from }}</span>
                                                a
                                                <span class="font-medium">{{ votes.to }}</span>
                                                de
                                                <span class="font-medium">{{ votes.total }}</span>
                                                votos
                                            </p>
                                        </div>
                                        <div>
                                            <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                                                <template v-for="(link, index) in votes.links" :key="index">
                                                    <Link v-if="link.url" :href="link.url"
                                                        class="relative inline-flex items-center px-2 py-2 text-sm font-medium"
                                                        :class="[
                                                            link.active
                                                                ? 'z-10 bg-indigo-50 border-indigo-500 text-indigo-600'
                                                                : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50',
                                                            index === 0 ? 'rounded-l-md' : '',
                                                            index === votes.links.length - 1 ? 'rounded-r-md' : '',
                                                            'border'
                                                        ]" v-html="link.label">
                                                    </Link>
                                                    <span v-else
                                                        class="relative inline-flex items-center px-2 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 cursor-default"
                                                        :class="[
                                                            index === 0 ? 'rounded-l-md' : '',
                                                            index === votes.links.length - 1 ? 'rounded-r-md' : ''
                                                        ]" v-html="link.label">
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
import { ArrowLeftIcon, PencilIcon, TrashIcon, StarIcon } from '@heroicons/vue/24/outline';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';

const props = defineProps({
    photo: Object,
    votes: Object
});

const showDeleteModal = ref(false);
const processing = ref(false);

const confirmDelete = () => {
    showDeleteModal.value = true;
};

const deletePhoto = () => {
    processing.value = true;
    router.delete(route('admin.photos.destroy', props.photo.id), {
        onSuccess: () => {
            // Redirect will be handled by the controller
        },
        onFinish: () => {
            processing.value = false;
        }
    });
};

const goBack = () => {
    history.back();
};

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('pt-BR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};
</script>