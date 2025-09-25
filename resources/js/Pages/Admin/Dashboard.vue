<template>

    <Head title="Área do Administrador" />

    <AdminLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Área do Administrador
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Layout Principal: Lado Esquerdo (Cards + Projetos) e Lado Direito (Hall das Fotos) -->
                <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
                    <!-- Lado Esquerdo: Cards e Projetos Recentes (Compacto) -->
                    <div class="lg:col-span-2 space-y-4">
                        <!-- Primeira linha: Total de Projetos e Projetos Ativos -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <Link :href="route('admin.projects.index')"
                                class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition-shadow duration-200 cursor-pointer block">
                            <div class="p-3">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <div class="w-6 h-6 bg-blue-500 rounded-full flex items-center justify-center">
                                            <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="ml-2">
                                        <div class="text-xs font-medium text-gray-500">Total de Projetos</div>
                                        <div class="text-lg font-bold text-gray-900">{{ stats.total_projects }}</div>
                                    </div>
                                    <div class="ml-auto">
                                        <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            </Link>

                            <Link :href="route('admin.projects.index')"
                                class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition-shadow duration-200 cursor-pointer block">
                            <div class="p-3">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <div class="w-6 h-6 bg-green-500 rounded-full flex items-center justify-center">
                                            <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                    clip-rule="evenodd"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="ml-2">
                                        <div class="text-xs font-medium text-gray-500">Projetos Ativos</div>
                                        <div class="text-lg font-bold text-gray-900">{{ stats.active_projects }}</div>
                                    </div>
                                    <div class="ml-auto">
                                        <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            </Link>
                        </div>

                        <!-- Segunda linha: Total de Fotos e Total de Votos -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <Link :href="route('admin.photos.index')"
                                class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition-shadow duration-200 cursor-pointer block">
                            <div class="p-3">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <div
                                            class="w-6 h-6 bg-purple-500 rounded-full flex items-center justify-center">
                                            <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z"
                                                    clip-rule="evenodd"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="ml-2">
                                        <div class="text-xs font-medium text-gray-500">Total de Fotos</div>
                                        <div class="text-lg font-bold text-gray-900">{{ stats.total_photos }}</div>
                                    </div>
                                    <div class="ml-auto">
                                        <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            </Link>

                            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                                <div class="p-3">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0">
                                            <div
                                                class="w-6 h-6 bg-red-500 rounded-full flex items-center justify-center">
                                                <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                    <path
                                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                                                    </path>
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="ml-2">
                                            <div class="text-xs font-medium text-gray-500">Total de Votos</div>
                                            <div class="text-lg font-bold text-gray-900">{{ stats.total_votes }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Terceira linha: Total de Usuários (card mais largo) -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-3">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <div
                                            class="w-6 h-6 bg-indigo-500 rounded-full flex items-center justify-center">
                                            <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="ml-2">
                                        <div class="text-xs font-medium text-gray-500">Total de Usuários</div>
                                        <div class="text-lg font-bold text-gray-900">{{ stats.total_users }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Quarta linha: Votantes e Admins -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <Link :href="route('admin.users.index')"
                                class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition-shadow duration-200 cursor-pointer block">
                            <div class="p-3">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <div
                                            class="w-6 h-6 bg-yellow-500 rounded-full flex items-center justify-center">
                                            <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                <path
                                                    d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z">
                                                </path>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="ml-2">
                                        <div class="text-xs font-medium text-gray-500">Votantes</div>
                                        <div class="text-lg font-bold text-gray-900">{{ stats.voters }}</div>
                                    </div>
                                    <div class="ml-auto">
                                        <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            </Link>

                            <Link :href="route('admin.users.index')"
                                class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition-shadow duration-200 cursor-pointer block">
                            <div class="p-3">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <div class="w-6 h-6 bg-gray-500 rounded-full flex items-center justify-center">
                                            <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-6-3a2 2 0 11-4 0 2 2 0 014 0zm-2 4a5 5 0 00-4.546 2.916A5.986 5.986 0 0010 16a5.986 5.986 0 004.546-2.084A5 5 0 0010 11z"
                                                    clip-rule="evenodd"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="ml-2">
                                        <div class="text-xs font-medium text-gray-500">Administradores</div>
                                        <div class="text-lg font-bold text-gray-900">{{ stats.admins }}</div>
                                    </div>
                                    <div class="ml-auto">
                                        <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            </Link>
                        </div>

                        <!-- Projetos Recentes (Compacto) -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-3">
                                <h3 class="text-sm font-medium text-gray-900 mb-3">Projetos Recentes</h3>
                                <div class="space-y-2">
                                    <Link v-for="project in recentProjects" :key="project.id"
                                        :href="route('admin.projects.show', project.id)"
                                        class="flex items-center justify-between p-2 rounded-lg hover:bg-gray-50 transition-colors duration-200 cursor-pointer group">
                                    <div>
                                        <div class="text-xs font-medium text-gray-900 group-hover:text-blue-600">{{
                                            project.name
                                            }}</div>
                                        <div class="text-xs text-gray-500">{{ formatDate(project.created_at) }} • {{
                                            project.photos_count }} fotos • {{ project.votes_count }} votos</div>
                                    </div>
                                    <div class="flex items-center space-x-1">
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium"
                                            :class="project.is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'">
                                            {{ project.is_active ? 'Ativo' : 'Inativo' }}
                                        </span>
                                        <svg class="w-3 h-3 text-gray-400 group-hover:text-blue-600" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </div>
                                    </Link>
                                    <div v-if="recentProjects.length === 0"
                                        class="text-xs text-gray-500 text-center py-3">
                                        Nenhum projeto encontrado
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Lado Direito: Hall das 10 Fotos Mais Votadas -->
                    <div class="lg:col-span-3">
                        <div
                            class="bg-gradient-to-br from-yellow-50 to-orange-50 border-2 border-yellow-200 overflow-hidden shadow-lg sm:rounded-lg relative h-full">
                            <!-- Badge de destaque -->
                            <div
                                class="absolute top-0 right-0 bg-gradient-to-r from-yellow-400 to-orange-400 text-white px-3 py-1 rounded-bl-lg text-xs font-bold z-10">
                                DESTAQUE
                            </div>
                            <div class="p-5 h-full">
                                <div class="flex items-center mb-6">
                                    <div
                                        class="w-10 h-10 bg-gradient-to-r from-yellow-400 to-orange-400 rounded-full flex items-center justify-center mr-3">
                                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path
                                                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                                            </path>
                                        </svg>
                                    </div>
                                    <h3 class="text-xl font-bold text-gray-900">🏆 Hall das 10 Mais Votadas</h3>
                                </div>
                                <div class="space-y-3">
                                    <Link v-for="(photo, index) in topPhotos" :key="photo.id"
                                        :href="route('admin.photos.show', photo.id)"
                                        class="block p-4 rounded-xl bg-white/80 hover:bg-white transition-all duration-200 cursor-pointer group shadow-sm hover:shadow-md">
                                    <div class="flex items-center space-x-4 ml-3">
                                        <!-- Posição e Imagem -->
                                        <div class="flex-shrink-0 relative">
                                            <img :src="`/storage/${photo.file_path}`" :alt="photo.caption"
                                                class="w-20 h-16 object-cover rounded-lg">
                                            <div class="absolute -top-2 -left-2 bg-yellow-500 text-white rounded-full w-8 h-8 flex items-center justify-center font-bold text-sm shadow-lg">
                                                {{ index + 1 }}
                                            </div>
                                        </div>
                                        
                                        <!-- Informações -->
                                        <div class="flex-1 min-w-0">
                                            <h4 class="font-semibold text-gray-900 text-sm line-clamp-1 group-hover:text-blue-600 transition-colors"
                                                v-html="photo.caption || 'Sem legenda'"></h4>
                                            <p class="text-xs text-gray-600 mt-1">{{ photo.project.name }}</p>
                                            <div class="flex items-center mt-2">
                                                <svg class="w-4 h-4 text-yellow-500 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                </svg>
                                                <span class="text-sm font-medium text-gray-700">{{ photo.votes_count }}</span>
                                            </div>
                                        </div>
                                        
                                        <!-- Sinalização de Item Clicável -->
                                        <div class="flex-shrink-0">
                                            <div class="w-8 h-8 bg-yellow-50 group-hover:bg-yellow-100 rounded-full flex items-center justify-center transition-all duration-200">
                                                <svg class="w-5 h-5 text-yellow-600 group-hover:text-yellow-700 transition-all duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                                </svg>
                                            </div>
                                        </div>
                                    </div>
                                    </Link>
                                    <div v-if="topPhotos.length === 0"
                                        class="text-sm text-gray-600 text-center py-8 bg-white/50 rounded-xl">
                                        Nenhuma foto encontrada
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    stats: Object,
    recentProjects: Array,
    topPhotos: Array
});

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('pt-BR');
};
</script>

<style scoped>
/* Estilos específicos para o dashboard */
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Garantir que o Hall das fotos ocupe toda a altura disponível */
.h-full {
    min-height: 100%;
}

/* Melhorar a responsividade em telas menores */
@media (max-width: 1024px) {
    .lg\:col-span-2 {
        grid-column: span 1;
    }

    .lg\:col-span-1 {
        grid-column: span 1;
    }
}
</style>