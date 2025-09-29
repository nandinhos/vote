<template>
    <Transition
        enter-active-class="transition ease-out duration-300"
        enter-from-class="opacity-0 transform translate-y-2"
        enter-to-class="opacity-100 transform translate-y-0"
        leave-active-class="transition ease-in duration-200"
        leave-from-class="opacity-100 transform translate-y-0"
        leave-to-class="opacity-0 transform translate-y-2"
    >
        <div
            v-if="show"
            :class="[
                'fixed top-4 right-4 z-50 max-w-sm w-full bg-white border border-gray-200 rounded-lg shadow-lg p-4',
                type === 'success' ? 'border-green-200' : 'border-red-200'
            ]"
        >
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <!-- Success Icon -->
                    <svg
                        v-if="type === 'success'"
                        class="h-5 w-5 text-green-400"
                        fill="currentColor"
                        viewBox="0 0 20 20"
                    >
                        <path
                            fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                            clip-rule="evenodd"
                        />
                    </svg>
                    <!-- Error Icon -->
                    <svg
                        v-else
                        class="h-5 w-5 text-red-400"
                        fill="currentColor"
                        viewBox="0 0 20 20"
                    >
                        <path
                            fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                            clip-rule="evenodd"
                        />
                    </svg>
                </div>
                <div class="ml-3 flex-1">
                    <p :class="[
                        'text-sm font-medium',
                        type === 'success' ? 'text-green-800' : 'text-red-800'
                    ]">
                        {{ message }}
                    </p>
                </div>
                <div class="ml-4 flex-shrink-0">
                    <button
                        @click="$emit('close')"
                        :class="[
                            'inline-flex text-gray-400 hover:text-gray-600 focus:outline-none focus:text-gray-600',
                            type === 'success' ? 'hover:text-green-600' : 'hover:text-red-600'
                        ]"
                    >
                        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                            <path
                                fill-rule="evenodd"
                                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                clip-rule="evenodd"
                            />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </Transition>
</template>

<script setup>
defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    message: {
        type: String,
        required: true,
    },
    type: {
        type: String,
        default: 'success',
        validator: (value) => ['success', 'error'].includes(value),
    },
});

defineEmits(['close']);
</script>