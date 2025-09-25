<script setup>
defineProps({
    type: {
        type: String,
        default: 'submit',
    },
});
</script>

<template>
    <button
        :type="type"
        class="inline-flex items-center justify-center px-5 py-3 bg-gradient-to-r from-purple-600 to-indigo-600 border border-transparent rounded-lg font-medium text-sm text-white tracking-wide hover:from-purple-700 hover:to-indigo-700 focus:from-purple-700 focus:to-indigo-700 active:from-purple-800 active:to-indigo-800 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition-all duration-200 shadow-md hover:shadow-lg transform hover:-translate-y-0.5"
    >
        <slot />
    </button>
</template>
