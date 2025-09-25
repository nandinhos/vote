<script setup>
defineProps({
    value: {
        type: String,
    },
});
</script>

<template>
    <label class="block font-semibold text-gray-700 text-sm">
        <span v-if="value">{{ value }}</span>
        <span v-else><slot /></span>
    </label>
</template>
