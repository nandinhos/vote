<script setup>
import { computed, onMounted, onUnmounted, ref, nextTick } from 'vue';

const props = defineProps({
    align: {
        type: String,
        default: 'right',
    },
    width: {
        type: String,
        default: '48',
    },
    contentClasses: {
        type: String,
        default: 'py-1 bg-white',
    },
    direction: {
        type: String,
        default: 'auto', // 'up', 'down', 'auto'
    },
});

const closeOnEscape = (e) => {
    if (open.value && e.key === 'Escape') {
        open.value = false;
    }
};

onMounted(() => document.addEventListener('keydown', closeOnEscape));
onUnmounted(() => document.removeEventListener('keydown', closeOnEscape));

const open = ref(false);
const dropdownRef = ref(null);
const shouldOpenUpward = ref(false);

const checkPosition = async () => {
    if (props.direction !== 'auto') {
        shouldOpenUpward.value = props.direction === 'up';
        return;
    }

    await nextTick();
    
    if (dropdownRef.value) {
        const rect = dropdownRef.value.getBoundingClientRect();
        const viewportHeight = window.innerHeight;
        const spaceBelow = viewportHeight - rect.bottom;
        const spaceAbove = rect.top;
        
        // Se há mais espaço acima e pouco espaço abaixo, abre para cima
        shouldOpenUpward.value = spaceAbove > spaceBelow && spaceBelow < 200;
    }
};

const toggleDropdown = async () => {
    if (!open.value) {
        await checkPosition();
    }
    open.value = !open.value;
};

const widthClass = computed(() => {
    return {
        48: 'w-48',
    }[props.width.toString()];
});

const alignmentClasses = computed(() => {
    const baseAlignment = shouldOpenUpward.value ? 'bottom' : 'top';
    
    if (props.align === 'left') {
        return `ltr:origin-${baseAlignment}-left rtl:origin-${baseAlignment}-right start-0`;
    } else if (props.align === 'right') {
        return `ltr:origin-${baseAlignment}-right rtl:origin-${baseAlignment}-left end-0`;
    } else {
        return `origin-${baseAlignment}`;
    }
});

const positionClasses = computed(() => {
    if (shouldOpenUpward.value) {
        return 'bottom-full mb-2';
    } else {
        return 'top-full mt-2';
    }
});
</script>

<template>
    <div class="relative" ref="dropdownRef">
        <div @click="toggleDropdown">
            <slot name="trigger" />
        </div>

        <!-- Full Screen Overlay -->
        <div v-show="open" class="fixed inset-0 z-40" @click="open = false"></div>

        <Transition
            enter-active-class="transition ease-out duration-200"
            enter-from-class="transform opacity-0 scale-95"
            enter-to-class="transform opacity-100 scale-100"
            leave-active-class="transition ease-in duration-75"
            leave-from-class="transform opacity-100 scale-100"
            leave-to-class="transform opacity-0 scale-95"
        >
            <div
                v-show="open"
                class="absolute rounded-md shadow-lg"
                :class="[widthClass, alignmentClasses, positionClasses]"
                style="z-index: 9999;"
                @click="open = false"
            >
                <div class="rounded-md ring-1 ring-black ring-opacity-5" :class="contentClasses">
                    <slot name="content" />
                </div>
            </div>
        </Transition>
    </div>
</template>
