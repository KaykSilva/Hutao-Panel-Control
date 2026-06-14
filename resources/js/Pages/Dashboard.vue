<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    user: {
        type: Object,
        required: true,
    },
    messages: {
        type: Array,
        default: () => [],
    },
});
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800"
            >
                Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
                <div class="grid gap-4 md:grid-cols-3">
                    <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
                        <div class="text-sm font-medium text-gray-500">Email</div>
                        <div class="mt-2 truncate text-lg font-semibold text-gray-900">{{ user.email }}</div>
                    </div>
                    <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
                        <div class="text-sm font-medium text-gray-500">Telefone</div>
                        <div class="mt-2 text-lg font-semibold text-gray-900">{{ user.phone || 'Nao informado' }}</div>
                    </div>
                    <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
                        <div class="text-sm font-medium text-gray-500">WhatsApp</div>
                        <div class="mt-2 text-lg font-semibold text-gray-900">{{ user.whatsapp_id || 'Nao vinculado' }}</div>
                    </div>
                </div>

                <section class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
                    <div class="border-b border-gray-200 px-6 py-4">
                        <h3 class="text-base font-semibold text-gray-900">Ultimas mensagens no bot</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Data</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Direcao</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Mensagem</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                <tr v-for="message in messages" :key="message.id">
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-600">{{ new Date(message.created_at).toLocaleString('pt-BR') }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-700">{{ message.direction }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-900">{{ message.content }}</td>
                                </tr>
                                <tr v-if="messages.length === 0">
                                    <td colspan="3" class="px-6 py-6 text-sm text-gray-500">Nenhuma mensagem encontrada.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </section>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
