<template>
    <Head title="Editar Projeto" />

    <AdminLayout>
        <template #header>
            <div class="flex items-center space-x-4">
                <button
                        @click="goBack"
                        class="text-gray-500 hover:text-gray-700"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </button>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Editar Projeto: {{ project.name }}
                </h2>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <form @submit.prevent="submit" class="p-6 space-y-6">
                        <div>
                            <InputLabel for="name" value="Nome do Projeto" />
                            <TextInput
                                id="name"
                                v-model="form.name"
                                type="text"
                                class="mt-1 block w-full"
                                required
                                autofocus
                                autocomplete="name"
                            />
                            <InputError class="mt-2" :message="form.errors.name" />
                        </div>

                        <div>
                            <InputLabel for="description" value="Descrição" />
                            <textarea
                                id="description"
                                v-model="form.description"
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                rows="4"
                                placeholder="Descreva o projeto..."
                            ></textarea>
                            <InputError class="mt-2" :message="form.errors.description" />
                        </div>

                        <div class="flex items-center">
                            <Checkbox
                                id="is_active"
                                v-model:checked="form.is_active"
                                name="is_active"
                            />
                            <InputLabel for="is_active" value="Projeto Ativo" class="ml-2" />
                            <InputError class="mt-2" :message="form.errors.is_active" />
                        </div>

                        <div class="text-sm text-gray-600">
                            <p><strong>Projeto Ativo:</strong> Permite que os usuários votem nas fotos deste projeto.</p>
                            <p><strong>Projeto Inativo:</strong> As fotos ficam visíveis apenas para administradores.</p>
                        </div>

                        <!-- Informações do Projeto -->
                        <div class="border-t pt-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Informações do Projeto</h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <div class="text-sm font-medium text-gray-500">Total de Fotos</div>
                                    <div class="text-2xl font-bold text-gray-900">{{ project.photos_count }}</div>
                                </div>
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <div class="text-sm font-medium text-gray-500">Total de Votos</div>
                                    <div class="text-2xl font-bold text-gray-900">{{ project.votes_count }}</div>
                                </div>
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <div class="text-sm font-medium text-gray-500">Criado em</div>
                                    <div class="text-sm font-medium text-gray-900">{{ formatDate(project.created_at) }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-end space-x-4">
                            <Link
                                :href="route('admin.projects.index')"
                                class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150"
                            >
                                <ArrowLeftIcon class="h-4 w-4 mr-2" />
                                Cancelar
                            </Link>

                            <Link
                                :href="route('admin.projects.show', project.id)"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                            >
                                <EyeIcon class="h-4 w-4 mr-2" />
                                Ver Projeto
                            </Link>

                            <PrimaryButton class="flex items-center" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                <FolderIcon class="h-4 w-4 mr-2" />
                                Atualizar Projeto
                            </PrimaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeftIcon, FolderIcon, EyeIcon } from '@heroicons/vue/24/outline';
import Checkbox from '@/Components/Checkbox.vue';

const props = defineProps({
    project: Object
});

const form = useForm({
    name: props.project.name,
    description: props.project.description,
    is_active: props.project.is_active
});

const goBack = () => {
    history.back();
};

const submit = () => {
    form.put(route('admin.projects.update', props.project.id));
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