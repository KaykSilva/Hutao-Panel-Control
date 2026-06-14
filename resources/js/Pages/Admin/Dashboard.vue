<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    usersCount: Number,
    contactsCount: Number,
    messagesCount: Number,
    latestMessages: {
        type: Array,
        default: () => [],
    },
});
</script>

<template>
    <Head title="Admin" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">Admin</h2>
                <Link :href="route('admin.bot.edit')" class="inline-flex items-center justify-center rounded-md bg-teal-700 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-teal-800">
                    Configurar bot
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
                <div class="grid gap-4 md:grid-cols-3">
                    <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
                        <div class="text-sm font-medium text-gray-500">Usuarios</div>
                        <div class="mt-2 text-3xl font-bold text-gray-900">{{ usersCount }}</div>
                    </div>
                    <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
                        <div class="text-sm font-medium text-gray-500">Contatos do bot</div>
                        <div class="mt-2 text-3xl font-bold text-gray-900">{{ contactsCount }}</div>
                    </div>
                    <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
                        <div class="text-sm font-medium text-gray-500">Mensagens</div>
                        <div class="mt-2 text-3xl font-bold text-gray-900">{{ messagesCount }}</div>
                    </div>
                </div>

                <section class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
                    <div class="border-b border-gray-200 px-6 py-4">
                        <h3 class="text-base font-semibold text-gray-900">Atividade recente</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Data</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Contato</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Direcao</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Mensagem</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                <tr v-for="message in latestMessages" :key="message.id">
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-600">{{ new Date(message.created_at).toLocaleString('pt-BR') }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-700">{{ message.contact?.phone }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-700">{{ message.direction }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-900">{{ message.content }}</td>
                                </tr>
                                <tr v-if="latestMessages.length === 0">
                                    <td colspan="4" class="px-6 py-6 text-sm text-gray-500">Nenhuma mensagem registrada.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </section>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
