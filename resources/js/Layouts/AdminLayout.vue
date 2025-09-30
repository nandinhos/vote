<template>
    <div class="flex h-screen bg-gray-900">
        <!-- Sidebar -->
        <div 
            :class="[
                'fixed inset-y-0 left-0 z-50 flex flex-col bg-white shadow-lg transition-all duration-300 ease-in-out',
                sidebarCollapsed ? 'w-16' : 'w-64'
            ]"
        >
            <!-- Logo Section -->
            <div class="flex items-center justify-between h-16 px-4 border-b border-gray-200">
                <Link :href="route('admin.dashboard')" class="flex items-center">
                    <ApplicationLogo class="h-8 w-8 text-indigo-600" />
                    <span 
                        v-show="!sidebarCollapsed" 
                        class="ml-3 text-xl font-semibold text-gray-800 transition-opacity duration-300"
                    >
                        Admin
                    </span>
                </Link>
                
                <!-- Toggle Button -->
                <button
                    @click="toggleSidebar"
                    class="p-2 rounded-lg text-gray-500 hover:text-gray-700 hover:bg-gray-100 transition-colors"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path 
                            stroke-linecap="round" 
                            stroke-linejoin="round" 
                            stroke-width="2" 
                            :d="sidebarCollapsed ? 'M13 5l7 7-7 7M5 5l7 7-7 7' : 'M11 19l-7-7 7-7M19 19l-7-7 7-7'"
                        />
                    </svg>
                </button>
            </div>

            <!-- Navigation Links -->
            <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
                <SidebarNavLink
                    :href="route('admin.dashboard')"
                    :active="route().current('admin.dashboard')"
                    :collapsed="sidebarCollapsed"
                    icon="dashboard"
                >
                    Dashboard
                </SidebarNavLink>
                
                <SidebarNavLink
                    :href="route('admin.projects.index')"
                    :active="route().current('admin.projects.*')"
                    :collapsed="sidebarCollapsed"
                    icon="projects"
                >
                    Projetos
                </SidebarNavLink>
                
                <SidebarNavLink
                    :href="route('admin.photos.index')"
                    :active="route().current('admin.photos.*')"
                    :collapsed="sidebarCollapsed"
                    icon="photos"
                >
                    Fotos
                </SidebarNavLink>
                
                <SidebarNavLink
                    :href="route('admin.users.index')"
                    :active="route().current('admin.users.*')"
                    :collapsed="sidebarCollapsed"
                    icon="users"
                >
                    Gerenciar Usuários
                </SidebarNavLink>
            </nav>

            <!-- User Profile Section -->
            <div class="border-t border-gray-200 p-4">
                <div class="relative">
                    <Dropdown align="right" width="48">
                        <template #trigger>
                            <button
                                class="flex items-center w-full p-2 text-left rounded-lg hover:bg-gray-100 transition-colors"
                            >
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-indigo-600 rounded-full flex items-center justify-center">
                                        <span class="text-white text-sm font-medium">
                                            {{ $page.props.auth.user.name.charAt(0).toUpperCase() }}
                                        </span>
                                    </div>
                                </div>
                                
                                <div 
                                    v-show="!sidebarCollapsed" 
                                    class="ml-3 flex-1 min-w-0 transition-opacity duration-300"
                                >
                                    <p class="text-sm font-medium text-gray-900 truncate">
                                        {{ $page.props.auth.user.name }}
                                    </p>
                                    <p class="text-xs text-gray-500 truncate">
                                        {{ $page.props.auth.user.email }}
                                    </p>
                                </div>
                                
                                <svg 
                                    v-show="!sidebarCollapsed"
                                    class="ml-2 h-4 w-4 text-gray-400 transition-opacity duration-300" 
                                    fill="none" 
                                    stroke="currentColor" 
                                    viewBox="0 0 24 24"
                                >
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                        </template>

                        <template #content>
                            <DropdownLink :href="route('profile.edit')">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    Perfil
                                </div>
                            </DropdownLink>
                            <DropdownLink :href="route('logout')" method="post" as="button">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                    </svg>
                                    Sair
                                </div>
                            </DropdownLink>
                        </template>
                    </Dropdown>
                </div>
            </div>
        </div>

        <!-- Main Content Area -->
        <div 
            :class="[
                'flex-1 flex flex-col transition-all duration-300 ease-in-out',
                sidebarCollapsed ? 'ml-16' : 'ml-64'
            ]"
        >
            <!-- Top Header -->
            <header class="bg-white shadow-sm border-b border-gray-200">
                <div class="flex items-center justify-between h-16 px-6">
                    <!-- Page Title -->
                    <div v-if="$slots.header">
                        <slot name="header" />
                    </div>
                    
                    <!-- Mobile Menu Button -->
                    <button
                        @click="showMobileMenu = !showMobileMenu"
                        class="md:hidden p-2 rounded-lg text-gray-500 hover:text-gray-700 hover:bg-gray-100"
                    >
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto bg-gray-100">
                <slot />
            </main>
        </div>

        <!-- Mobile Overlay -->
        <div 
            v-show="showMobileMenu"
            @click="showMobileMenu = false"
            class="fixed inset-0 z-40 bg-black bg-opacity-50 md:hidden"
        ></div>

        <!-- Mobile Sidebar -->
        <div 
            :class="[
                'fixed inset-y-0 left-0 z-50 w-64 bg-white shadow-lg transform transition-transform duration-300 ease-in-out md:hidden',
                showMobileMenu ? 'translate-x-0' : '-translate-x-full'
            ]"
        >
            <!-- Mobile Logo Section -->
            <div class="flex items-center justify-between h-16 px-4 border-b border-gray-200">
                <Link :href="route('admin.dashboard')" class="flex items-center">
                    <ApplicationLogo class="h-8 w-8 text-indigo-600" />
                    <span class="ml-3 text-xl font-semibold text-gray-800">Admin</span>
                </Link>
                
                <button
                    @click="showMobileMenu = false"
                    class="p-2 rounded-lg text-gray-500 hover:text-gray-700 hover:bg-gray-100"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Mobile Navigation -->
            <nav class="flex-1 px-4 py-6 space-y-2">
                <SidebarNavLink
                    :href="route('admin.dashboard')"
                    :active="route().current('admin.dashboard')"
                    :collapsed="false"
                    icon="dashboard"
                    @click="showMobileMenu = false"
                >
                    Dashboard
                </SidebarNavLink>
                
                <SidebarNavLink
                    :href="route('admin.projects.index')"
                    :active="route().current('admin.projects.*')"
                    :collapsed="false"
                    icon="projects"
                    @click="showMobileMenu = false"
                >
                    Projetos
                </SidebarNavLink>
                
                <SidebarNavLink
                    :href="route('admin.photos.index')"
                    :active="route().current('admin.photos.*')"
                    :collapsed="false"
                    icon="photos"
                    @click="showMobileMenu = false"
                >
                    Fotos
                </SidebarNavLink>
                
                <SidebarNavLink
                    :href="route('admin.users.index')"
                    :active="route().current('admin.users.*')"
                    :collapsed="false"
                    icon="users"
                    @click="showMobileMenu = false"
                >
                    Gerenciar Usuários
                </SidebarNavLink>
            </nav>

            <!-- Mobile User Profile -->
            <div class="border-t border-gray-200 p-4">
                <div class="flex items-center mb-4">
                    <div class="w-10 h-10 bg-indigo-600 rounded-full flex items-center justify-center">
                        <span class="text-white font-medium">
                            {{ $page.props.auth.user.name.charAt(0).toUpperCase() }}
                        </span>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-900">{{ $page.props.auth.user.name }}</p>
                        <p class="text-xs text-gray-500">{{ $page.props.auth.user.email }}</p>
                    </div>
                </div>
                
                <div class="space-y-1">
                    <Link 
                        :href="route('profile.edit')"
                        @click="showMobileMenu = false"
                        class="flex items-center px-3 py-2 text-sm text-gray-700 rounded-lg hover:bg-gray-100"
                    >
                        <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        Perfil
                    </Link>
                    
                    <Link 
                        :href="route('logout')" 
                        method="post" 
                        as="button"
                        @click="showMobileMenu = false"
                        class="flex items-center w-full px-3 py-2 text-sm text-gray-700 rounded-lg hover:bg-gray-100 text-left"
                    >
                        <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        Sair
                    </Link>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import SidebarNavLink from '@/Components/SidebarNavLink.vue';
import { Link } from '@inertiajs/vue3';

// Sidebar state management
const sidebarCollapsed = ref(false);
const showMobileMenu = ref(false);

// Toggle sidebar function
const toggleSidebar = () => {
    sidebarCollapsed.value = !sidebarCollapsed.value;
};
</script>