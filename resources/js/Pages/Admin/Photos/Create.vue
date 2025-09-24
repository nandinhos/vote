<template>
    <Head title="Nova Foto" />

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
                    Nova Foto
                </h2>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <form @submit.prevent="submit">
                            <div class="mb-6">
                                <label for="project_id" class="block text-sm font-medium text-gray-700 mb-2">
                                    Projeto *
                                </label>
                                <select
                                    id="project_id"
                                    v-model="form.project_id"
                                    class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                    :class="{ 'border-red-500': form.errors.project_id }"
                                >
                                    <option value="">Selecione um projeto</option>
                                    <option v-for="project in projects" :key="project.id" :value="project.id">
                                        {{ project.name }}
                                    </option>
                                </select>
                                <div v-if="form.errors.project_id" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.project_id }}
                                </div>
                            </div>

                            <div class="mb-6">
                                <label for="photo" class="block text-sm font-medium text-gray-700 mb-2">
                                    Foto *
                                </label>
                                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md"
                                     :class="{ 'border-red-500': form.errors.photo }"
                                     @drop="handleDrop"
                                     @dragover.prevent
                                     @dragenter.prevent>
                                    <div class="space-y-1 text-center">
                                        <div v-if="photoPreviews.length === 0">
                                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                            <div class="flex text-sm text-gray-600">
                                                <label for="photo" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                                    <span>Envie uma ou múltiplas fotos</span>
                                                    <input
                                                        id="photo"
                                                        type="file"
                                                        class="sr-only"
                                                        accept="image/*"
                                                        multiple
                                                        @change="handleFileSelect"
                                                    >
                                                </label>
                                                <p class="pl-1">ou arraste e solte</p>
                                            </div>
                                            <p class="text-xs text-gray-500">
                                                PNG, JPG, GIF até 50MB cada. Selecione múltiplas fotos para upload em lote.
                                            </p>
                                        </div>
                                        <div v-else>
                                            <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-4">
                                                <div v-for="(preview, index) in photoPreviews" :key="index" class="relative">
                                                    <img :src="preview.url" :alt="preview.name" class="w-full h-24 object-cover rounded-lg">
                                                    <button
                                                        type="button"
                                                        @click="removePhoto(index)"
                                                        class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 hover:bg-red-600"
                                                    >
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                        </svg>
                                                    </button>
                                                    <p class="mt-1 text-xs text-gray-600 truncate">{{ preview.name }}</p>
                                                </div>
                                            </div>
                                            <div class="text-center">
                                                <p class="text-sm text-gray-600 mb-2">{{ photoPreviews.length }} foto(s) selecionada(s)</p>
                                                <button
                                                    type="button"
                                                    @click="removePhotos"
                                                    class="text-red-600 hover:text-red-800 text-sm font-medium"
                                                >
                                                    Remover todas
                                                </button>
                                                <span class="mx-2 text-gray-400">|</span>
                                                <label for="photo" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium cursor-pointer">
                                                    Adicionar mais fotos
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div v-if="form.errors.photo" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.photo }}
                                </div>
                            </div>

                            <div class="mb-6">
                                <label for="caption" class="block text-sm font-medium text-gray-700 mb-2">
                                    Legenda
                                </label>
                                <textarea
                                    id="caption"
                                    v-model="form.caption"
                                    rows="3"
                                    class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                    :class="{ 'border-red-500': form.errors.caption }"
                                    placeholder="Digite uma legenda para a foto (opcional)"
                                ></textarea>
                                <div v-if="form.errors.caption" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.caption }}
                                </div>
                            </div>

                            <div class="flex items-center justify-between">
                                <Link
                                    :href="route('admin.photos.index')"
                                    class="inline-flex items-center justify-center w-10 h-10 bg-gray-300 border border-transparent rounded-md text-gray-700 hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150"
                                    title="Cancelar"
                                >
                                    <ArrowLeftIcon class="h-5 w-5" />
                                </Link>

                                <button
                                    type="submit"
                                    class="inline-flex items-center justify-center w-10 h-10 bg-blue-600 border border-transparent rounded-md text-white hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                                    :disabled="form.processing"
                                    :title="form.processing ? 'Salvando...' : 'Salvar Foto'"
                                >
                                    <svg v-if="form.processing" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <PhotoIcon v-if="!form.processing" class="h-5 w-5" />
                                </button>
                            </div>
                        </form>
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
    projects: Array
});

const form = useForm({
    project_id: '',
    photos: [],
    caption: ''
});

const photoPreviews = ref([]);
const selectedFiles = ref([]);

const handleFileSelect = (event) => {
    const files = Array.from(event.target.files);
    if (files.length > 0) {
        if (form.photos.length > 0) {
            addPhotos(files);
        } else {
            setPhotos(files);
        }
    }
    // Reset input para permitir selecionar os mesmos arquivos novamente
    event.target.value = '';
};

const handleDrop = (event) => {
    event.preventDefault();
    const files = Array.from(event.dataTransfer.files).filter(file => file.type.startsWith('image/'));
    if (files.length > 0) {
        setPhotos(files);
    }
};

const setPhotos = (files) => {
    form.photos = files;
    selectedFiles.value = files;
    photoPreviews.value = [];
    
    files.forEach((file, index) => {
        const reader = new FileReader();
        reader.onload = (e) => {
            photoPreviews.value.push({
                url: e.target.result,
                name: file.name,
                size: file.size
            });
        };
        reader.readAsDataURL(file);
    });
};

const addPhotos = (newFiles) => {
    // Adicionar novos arquivos aos existentes
    const currentFiles = Array.from(form.photos);
    const allFiles = [...currentFiles, ...newFiles];
    
    form.photos = allFiles;
    selectedFiles.value = allFiles;
    
    // Adicionar previews dos novos arquivos
    newFiles.forEach((file) => {
        const reader = new FileReader();
        reader.onload = (e) => {
            photoPreviews.value.push({
                url: e.target.result,
                name: file.name,
                size: file.size
            });
        };
        reader.readAsDataURL(file);
    });
};

const removePhotos = () => {
    form.photos = [];
    photoPreviews.value = [];
    selectedFiles.value = [];
    // Reset file input
    const fileInput = document.getElementById('photo');
    if (fileInput) {
        fileInput.value = '';
    }
};

const removePhoto = (index) => {
    form.photos.splice(index, 1);
    photoPreviews.value.splice(index, 1);
    selectedFiles.value.splice(index, 1);
};

const goBack = () => {
    history.back();
};

const submit = () => {
    form.post(route('admin.photos.store'), {
        onSuccess: () => {
            // Form will be reset automatically on success
        }
    });
};
</script>