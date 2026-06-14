<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps({
    settings: {
        type: Object,
        required: true,
    },
    status: String,
});

const form = useForm({
    api_token: props.settings.api_token || '',
    regenerate_token: false,
    bot_name: props.settings.bot_name || 'Hutao Bot',
    welcome_message: props.settings.welcome_message || '',
    support_phone: props.settings.support_phone || '',
});
</script>

<template>
    <Head title="Configurar bot" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Bot</h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
                <div v-if="status" class="rounded-md border border-teal-200 bg-teal-50 px-4 py-3 text-sm font-medium text-teal-900">
                    {{ status }}
                </div>

                <section class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
                    <form class="space-y-6" @submit.prevent="form.put(route('admin.bot.update'))">
                        <div>
                            <InputLabel for="api_token" value="Token da API" />
                            <TextInput id="api_token" v-model="form.api_token" type="text" class="mt-1 block w-full" autocomplete="off" />
                            <InputError class="mt-2" :message="form.errors.api_token" />
                        </div>

                        <label class="flex items-center gap-3 text-sm font-medium text-gray-700">
                            <input v-model="form.regenerate_token" type="checkbox" class="rounded border-gray-300 text-teal-700 shadow-sm focus:ring-teal-700">
                            Gerar novo token ao salvar
                        </label>

                        <div>
                            <InputLabel for="bot_name" value="Nome do bot" />
                            <TextInput id="bot_name" v-model="form.bot_name" type="text" class="mt-1 block w-full" required />
                            <InputError class="mt-2" :message="form.errors.bot_name" />
                        </div>

                        <div>
                            <InputLabel for="welcome_message" value="Mensagem inicial" />
                            <textarea id="welcome_message" v-model="form.welcome_message" class="mt-1 block min-h-32 w-full rounded-md border-gray-300 shadow-sm focus:border-teal-700 focus:ring-teal-700" required />
                            <InputError class="mt-2" :message="form.errors.welcome_message" />
                        </div>

                        <div>
                            <InputLabel for="support_phone" value="Telefone de suporte" />
                            <TextInput id="support_phone" v-model="form.support_phone" type="text" class="mt-1 block w-full" />
                            <InputError class="mt-2" :message="form.errors.support_phone" />
                        </div>

                        <PrimaryButton :disabled="form.processing">Salvar configuracoes</PrimaryButton>
                    </form>
                </section>

                <section class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
                    <div class="border-b border-gray-200 px-6 py-4">
                        <h3 class="text-base font-semibold text-gray-900">Endpoints do bot</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <tbody class="divide-y divide-gray-200">
                                <tr><th class="w-28 bg-gray-50 px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">GET</th><td class="px-6 py-3 text-sm text-gray-900">/api/bot/settings</td></tr>
                                <tr><th class="bg-gray-50 px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">GET</th><td class="px-6 py-3 text-sm text-gray-900">/api/bot/user?phone=5585999999999</td></tr>
                                <tr><th class="bg-gray-50 px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">POST</th><td class="px-6 py-3 text-sm text-gray-900">/api/bot/users/link</td></tr>
                                <tr><th class="bg-gray-50 px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">POST</th><td class="px-6 py-3 text-sm text-gray-900">/api/bot/messages</td></tr>
                                <tr><th class="bg-gray-50 px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">POST</th><td class="px-6 py-3 text-sm text-gray-900">/api/webhook/whatsapp</td></tr>
                            </tbody>
                        </table>
                    </div>
                </section>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
