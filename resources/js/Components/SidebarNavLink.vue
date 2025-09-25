<template>
    <div class="relative group">
        <Link
            :href="href"
            :class="[
                'flex items-center rounded-lg text-sm font-medium transition-all duration-200 relative',
                collapsed ? 'px-3 py-3 justify-center' : 'px-3 py-2',
                active
                    ? 'bg-indigo-100 text-indigo-700 border-r-2 border-indigo-500'
                    : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900'
            ]"
            @click="$emit('click')"
        >
            <!-- Icon -->
            <div class="flex-shrink-0">
                <component :is="iconComponent" class="w-5 h-5" />
            </div>
            
            <!-- Text -->
            <span 
                v-show="!collapsed" 
                class="ml-3 transition-opacity duration-300"
            >
                <slot />
            </span>
        </Link>
        
        <!-- Tooltip for collapsed state -->
        <div 
            v-if="collapsed"
            class="absolute left-full ml-2 top-1/2 transform -translate-y-1/2 bg-gray-900 text-white px-2 py-1 rounded text-xs opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none z-50 whitespace-nowrap"
        >
            <slot />
            <!-- Arrow -->
            <div class="absolute right-full top-1/2 transform -translate-y-1/2 border-4 border-transparent border-r-gray-900"></div>
        </div>
    </div>
</template>

<script setup>
import { computed, h } from 'vue';
import { Link } from '@inertiajs/vue3';

// Props
const props = defineProps({
    href: {
        type: String,
        required: true,
    },
    active: {
        type: Boolean,
        default: false,
    },
    collapsed: {
        type: Boolean,
        default: false,
    },
    icon: {
        type: String,
        required: true,
    },
});

// Emits
defineEmits(['click']);

// Icon components mapping
const iconComponents = {
    dashboard: {
        render() {
            return h('svg', {
                fill: 'none',
                stroke: 'currentColor',
                viewBox: '0 0 24 24',
                xmlns: 'http://www.w3.org/2000/svg'
            }, [
                h('path', {
                    'stroke-linecap': 'round',
                    'stroke-linejoin': 'round',
                    'stroke-width': '2',
                    d: 'M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z'
                }),
                h('path', {
                    'stroke-linecap': 'round',
                    'stroke-linejoin': 'round',
                    'stroke-width': '2',
                    d: 'M8 5a2 2 0 012-2h4a2 2 0 012 2v6H8V5z'
                })
            ]);
        }
    },
    projects: {
        render() {
            return h('svg', {
                fill: 'none',
                stroke: 'currentColor',
                viewBox: '0 0 24 24',
                xmlns: 'http://www.w3.org/2000/svg'
            }, [
                h('path', {
                    'stroke-linecap': 'round',
                    'stroke-linejoin': 'round',
                    'stroke-width': '2',
                    d: 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10'
                })
            ]);
        }
    },
    photos: {
        render() {
            return h('svg', {
                fill: 'none',
                stroke: 'currentColor',
                viewBox: '0 0 24 24',
                xmlns: 'http://www.w3.org/2000/svg'
            }, [
                h('path', {
                    'stroke-linecap': 'round',
                    'stroke-linejoin': 'round',
                    'stroke-width': '2',
                    d: 'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z'
                })
            ]);
        }
    }
};

// Computed icon component
const iconComponent = computed(() => {
    return iconComponents[props.icon] || iconComponents.dashboard;
});
</script>

<style scoped>
.group:hover .group-hover\:opacity-100 {
    opacity: 1;
}
</style>