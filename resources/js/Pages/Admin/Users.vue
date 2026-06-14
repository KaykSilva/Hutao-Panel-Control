<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    users: {
        type: Object,
        required: true,
    },
    status: String,
});

const forms = new Map();

const roleForm = (user) => {
    if (!forms.has(user.id)) {
        forms.set(user.id, useForm({ role: user.role }));
    }

    return forms.get(user.id);
};
</script>

<template>
    <Head title="Usuarios" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Usuarios</h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
                <div v-if="status" class="rounded-md border border-teal-200 bg-teal-50 px-4 py-3 text-sm font-medium text-teal-900">
                    {{ status }}
                </div>

                <section class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Nome</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Email</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Telefone</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">WhatsApp</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Papel</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Criado em</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                <tr v-for="user in users.data" :key="user.id">
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ user.name }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-700">{{ user.email }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-700">{{ user.phone }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-700">{{ user.whatsapp_id }}</td>
                                    <td class="px-6 py-4">
                                        <form class="flex items-center gap-2" @submit.prevent="roleForm(user).put(route('admin.users.role', user.id), { preserveScroll: true })">
                                            <select v-model="roleForm(user).role" class="rounded-md border-gray-300 text-sm shadow-sm focus:border-teal-700 focus:ring-teal-700">
                                                <option value="user">user</option>
                                                <option value="admin">admin</option>
                                            </select>
                                            <button class="rounded-md bg-gray-100 px-3 py-2 text-sm font-semibold text-gray-800 hover:bg-gray-200" type="submit">
                                                Salvar
                                            </button>
                                        </form>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">{{ new Date(user.created_at).toLocaleDateString('pt-BR') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                <div class="flex flex-wrap gap-2">
                    <Link
                        v-for="link in users.links"
                        :key="link.label"
                        :href="link.url || ''"
                        class="rounded-md border px-3 py-2 text-sm"
                        :class="link.active ? 'border-teal-700 bg-teal-700 text-white' : 'border-gray-300 bg-white text-gray-700'"
                        v-html="link.label"
                    />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
