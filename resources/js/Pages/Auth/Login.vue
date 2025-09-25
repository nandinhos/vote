<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    saram: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout>

        <Head title="Entrar" />

        <div v-if="status" class="mb-4 text-sm font-medium text-green-600">
            {{ status }}
        </div>

        <form @submit.prevent="submit" class="space-y-4">
            <div>
                <InputLabel for="saram" value="Saram" />
                <TextInput 
                    id="saram" 
                    type="text" 
                    class="mt-1" 
                    v-model="form.saram" 
                    required 
                    autofocus
                    autocomplete="username" 
                    placeholder="Digite seu saram"
                    icon="saram"
                />
                <InputError class="mt-1" :message="form.errors.saram" />
            </div>

            <div>
                <InputLabel for="password" value="Password" />
                <TextInput 
                    id="password" 
                    type="password" 
                    class="mt-1" 
                    v-model="form.password" 
                    required
                    autocomplete="current-password" 
                    placeholder="••••••••"
                    icon="password"
                />
                <InputError class="mt-1" :message="form.errors.password" />
            </div>

            <div class="flex items-center py-1">
                <label class="flex items-center">
                    <Checkbox name="remember" v-model:checked="form.remember" />
                    <span class="ms-2 text-sm text-gray-600">Lembrar de mim</span>
                </label>
            </div>

            <div class="pt-1">
                <PrimaryButton class="w-full" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013 3v1"></path>
                    </svg>
                    Entrar
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>
