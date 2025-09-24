<template>
    <AdminLayout>
        <Head title="Editar Usuário" />

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div class="flex justify-between items-center mb-6">
                            <h2 class="text-2xl font-semibold text-gray-800">Editar Usuário: {{ user.name }}</h2>
                            <button @click="goBack" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Voltar
                            </button>
                        </div>

                        <form @submit.prevent="submit" class="space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Nome -->
                                <div>
                                    <InputLabel for="name" value="Nome" />
                                    <TextInput
                                        id="name"
                                        type="text"
                                        class="mt-1 block w-full"
                                        v-model="form.name"
                                        required
                                        autofocus
                                        autocomplete="name"
                                    />
                                    <InputError class="mt-2" :message="form.errors.name" />
                                </div>

                                <!-- SARAM -->
                                <div>
                                    <InputLabel for="saram" value="SARAM" />
                                    <TextInput
                                        id="saram"
                                        type="text"
                                        class="mt-1 block w-full"
                                        v-model="form.saram"
                                        required
                                        autocomplete="username"
                                    />
                                    <InputError class="mt-2" :message="form.errors.saram" />
                                </div>



                                <!-- Função -->
                                <div>
                                    <InputLabel for="role" value="Função" />
                                    <select
                                        id="role"
                                        v-model="form.role"
                                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                        required
                                    >
                                        <option value="">Selecione uma função</option>
                                        <option value="voter">Votante</option>
                                        <option value="admin">Administrador</option>
                                    </select>
                                    <InputError class="mt-2" :message="form.errors.role" />
                                </div>

                                <!-- Nova Senha (opcional) -->
                                <div>
                                    <InputLabel for="password" value="Nova Senha (opcional)" />
                                    <TextInput
                                        id="password"
                                        type="password"
                                        class="mt-1 block w-full"
                                        v-model="form.password"
                                        autocomplete="new-password"
                                    />
                                    <InputError class="mt-2" :message="form.errors.password" />
                                    <p class="text-sm text-gray-600 mt-1">Deixe em branco para manter a senha atual</p>
                                </div>

                                <!-- Confirmar Nova Senha -->
                                <div>
                                    <InputLabel for="password_confirmation" value="Confirmar Nova Senha" />
                                    <TextInput
                                        id="password_confirmation"
                                        type="password"
                                        class="mt-1 block w-full"
                                        v-model="form.password_confirmation"
                                        autocomplete="new-password"
                                    />
                                    <InputError class="mt-2" :message="form.errors.password_confirmation" />
                                </div>
                            </div>

                            <div class="flex items-center justify-end">
                                <Link :href="route('admin.users.index')" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded mr-4 flex items-center">
                                    <ArrowLeftIcon class="h-4 w-4 mr-2" />
                                    Cancelar
                                </Link>
                                <PrimaryButton class="ml-4 flex items-center" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                    <UserIcon class="h-4 w-4 mr-2" />
                                    Atualizar Usuário
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
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
import { ArrowLeftIcon, UserIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    user: Object,
});

const form = useForm({
    name: props.user.name,
    saram: props.user.saram,
    password: '',
    password_confirmation: '',
    role: props.user.role,
});

const goBack = () => {
    history.back();
};

const submit = () => {
    form.put(route('admin.users.update', props.user.id));
};
</script>