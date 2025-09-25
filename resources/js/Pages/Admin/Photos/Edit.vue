<template>

    <Head title="Editar Foto" />

    <AdminLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-4">
                    <button @click="goBack" class="text-gray-500 hover:text-gray-700">
                        <ArrowLeftIcon class="w-5 h-5" />
                    </button>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        Editar Foto
                    </h2>
                </div>
                <div class="fixed top-4 right-16 flex space-x-2">
                    <Link v-if="photo && photo.id" :href="route('admin.photos.show', photo.id)"
                        class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    Ver Foto
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Foto Atual -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Foto Atual</h3>
                            <div v-if="photo && photo.file_path" class="aspect-w-1 aspect-h-1 mb-4">
                                <img :src="`/storage/${photo.file_path}`" :alt="photo.caption || 'Foto'"
                                    class="w-full h-64 object-cover rounded-lg">
                            </div>
                            <div v-if="photo && Object.keys(photo).length > 0" class="space-y-2 text-sm text-gray-600">
                                <div class="flex justify-between">
                                    <span class="font-medium">Projeto:</span>
                                    <Link v-if="photo.project && photo.project.id"
                                        :href="route('admin.projects.show', photo.project.id)"
                                        class="text-indigo-600 hover:text-indigo-900">
                                    {{ photo.project.name }}
                                    </Link>
                                    <span v-else class="text-gray-500">N/A</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="font-medium">Votos:</span>
                                    <span class="flex items-center">
                                        <svg class="w-4 h-4 text-yellow-400 mr-1" fill="currentColor"
                                            viewBox="0 0 20 20">
                                            <path
                                                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                                            </path>
                                        </svg>
                                        {{ photo.votes_count || 0 }}
                                    </span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="font-medium">Criada em:</span>
                                    <span>{{ photo.created_at ? formatDate(photo.created_at) : 'N/A' }}</span>
                                </div>
                            </div>
                            <div v-else class="text-center text-gray-500 py-8">
                                Carregando informações da foto...
                            </div>
                        </div>
                    </div>

                    <!-- Formulário de Edição -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Editar Informações</h3>

                            <form @submit.prevent="submit">
                                <div class="mb-6">
                                    <label for="project_id" class="block text-sm font-medium text-gray-700 mb-2">
                                        Projeto *
                                    </label>
                                    <select id="project_id" v-model="form.project_id"
                                        class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                        :class="{ 'border-red-500': form.errors.project_id }">
                                        <option value="">Selecione um projeto</option>
                                        <option v-for="project in (projects || [])" :key="project.id || project.name"
                                            :value="project.id">
                                            {{ project.name }}
                                        </option>
                                    </select>
                                    <div v-if="form.errors.project_id" class="mt-1 text-sm text-red-600">
                                        {{ form.errors.project_id }}
                                    </div>
                                </div>

                                <div class="mb-6">
                                    <label for="caption" class="block text-sm font-medium text-gray-700 mb-2">
                                        Legenda
                                    </label>
                                    <textarea id="caption" v-model="form.caption" rows="3"
                                        class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                        :class="{ 'border-red-500': form.errors.caption }"
                                        placeholder="Digite uma legenda para a foto (opcional)"></textarea>
                                    <div v-if="form.errors.caption" class="mt-1 text-sm text-red-600">
                                        {{ form.errors.caption }}
                                    </div>
                                </div>

                                <div class="mb-6">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Substituir Foto (opcional)
                                    </label>
                                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md"
                                        :class="{ 'border-red-500': form.errors.photo }" @drop="handleDrop"
                                        @dragover.prevent @dragenter.prevent>
                                        <div class="space-y-1 text-center">
                                            <div v-if="!photoPreview">
                                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor"
                                                    fill="none" viewBox="0 0 48 48">
                                                    <path
                                                        d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                                        stroke-width="2" stroke-linecap="round"
                                                        stroke-linejoin="round" />
                                                </svg>
                                                <div class="flex text-sm text-gray-600">
                                                    <label for="photo"
                                                        class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                                        <span>Envie uma nova foto</span>
                                                        <input id="photo" type="file" class="sr-only" accept="image/*"
                                                            @change="handleFileSelect">
                                                    </label>
                                                    <p class="pl-1">ou arraste e solte</p>
                                                </div>
                                                <p class="text-xs text-gray-500">
                                                    PNG, JPG, GIF até 10MB
                                                </p>
                                            </div>
                                            <div v-else class="relative">
                                                <img :src="photoPreview" alt="Preview"
                                                    class="mx-auto h-32 w-auto rounded-lg">
                                                <button type="button" @click="removePhoto"
                                                    class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 hover:bg-red-600">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                    </svg>
                                                </button>
                                                <p class="mt-2 text-sm text-gray-600">{{ selectedFileName }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div v-if="form.errors.photo" class="mt-1 text-sm text-red-600">
                                        {{ form.errors.photo }}
                                    </div>
                                    <p class="mt-1 text-xs text-gray-500">
                                        Deixe em branco para manter a foto atual
                                    </p>
                                </div>

                                <div class="flex items-center justify-between">
                                    <Link :href="route('admin.photos.index')"
                                        class="inline-flex items-center justify-center w-10 h-10 bg-gray-300 border border-transparent rounded-md text-gray-700 hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150"
                                        title="Cancelar">
                                    <ArrowLeftIcon class="h-5 w-5" />
                                    </Link>

                                    <button type="submit"
                                        class="inline-flex items-center justify-center w-10 h-10 bg-blue-600 border border-transparent rounded-md text-white hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                                        :disabled="form.processing"
                                        :title="form.processing ? 'Salvando...' : 'Atualizar Foto'">
                                        <svg v-if="form.processing" class="animate-spin h-5 w-5 text-white"
                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                                stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor"
                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                            </path>
                                        </svg>
                                        <PhotoIcon v-if="!form.processing" class="h-5 w-5" />
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { ArrowLeftIcon, PhotoIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    photo: {
        type: Object,
        default: () => ({})
    },
    projects: {
        type: Array,
        default: () => []
    }
});

// Debug para identificar o problema
console.log('Props recebidas:', props);
console.log('Photo:', props.photo);
console.log('Projects:', props.projects);

const form = useForm({
    project_id: props.photo?.project_id || '',
    photo: null,
    caption: props.photo?.caption || ''
});

const photoPreview = ref(null);
const selectedFileName = ref('');

const handleFileSelect = (event) => {
    const file = event.target.files[0];
    if (file) {
        setPhoto(file);
    }
};

const handleDrop = (event) => {
    event.preventDefault();
    const file = event.dataTransfer.files[0];
    if (file && file.type.startsWith('image/')) {
        setPhoto(file);
    }
};

const setPhoto = (file) => {
    form.photo = file;
    selectedFileName.value = file.name;

    const reader = new FileReader();
    reader.onload = (e) => {
        photoPreview.value = e.target.result;
    };
    reader.readAsDataURL(file);
};

const removePhoto = () => {
    form.photo = null;
    photoPreview.value = null;
    selectedFileName.value = '';
    // Reset file input
    const fileInput = document.getElementById('photo');
    if (fileInput) {
        fileInput.value = '';
    }
};

const submit = () => {
    if (!props.photo?.id) {
        console.error('Photo ID is missing');
        return;
    }

    form.put(route('admin.photos.update', props.photo.id), {
        onSuccess: () => {
            // Form will be reset automatically on success
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