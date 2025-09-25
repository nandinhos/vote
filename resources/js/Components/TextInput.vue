<script setup>
import { onMounted, ref } from 'vue';

defineEmits(['update:modelValue']);

defineProps({
    modelValue: {
        type: String,
        required: true,
    },
    type: {
        type: String,
        default: 'text',
    },
    icon: {
        type: String,
        default: null,
    },
});

const input = ref(null);

defineExpose({ focus: () => input.value.focus() });

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});
</script>

<template>
    <div class="relative">
        <!-- Ícone -->
        <div v-if="icon" class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <svg v-if="icon === 'email'" class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
            </svg>
            <svg v-else-if="icon === 'saram'" class="h-4 w-4 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                <text x="12" y="16" text-anchor="middle" font-family="Arial, sans-serif" font-size="12" font-weight="bold">#</text>
            </svg>
            <svg v-else-if="icon === 'password'" class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
            </svg>
        </div>
        
        <input
            :class="[
                'w-full border border-gray-200 focus:border-purple-400 focus:ring-purple-400 rounded-lg shadow-sm bg-gray-50 py-3 text-sm text-gray-900 placeholder-gray-400 transition-all duration-200 focus:bg-white focus:shadow-md',
                icon ? 'pl-10 pr-3' : 'px-3'
            ]"
            :type="type"
            :value="modelValue"
            @input="$emit('update:modelValue', $event.target.value)"
            ref="input"
        />
    </div>
</template>
