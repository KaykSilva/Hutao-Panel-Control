<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { computed, watch } from 'vue';

const props = defineProps({
    accounts: {
        type: Array,
        default: () => [],
    },
    groups: {
        type: Array,
        default: () => [],
    },
    users: {
        type: Array,
        default: () => [],
    },
    status: String,
});

const form = useForm({
    whatsapp_group_id: props.groups[0]?.id || '',
    responsible_user_id: '',
    title: '',
    description: '',
    total_amount: '',
    due_date: '',
    participant_ids: [],
});

const selectedGroup = computed(() =>
    props.groups.find((group) => Number(group.id) === Number(form.whatsapp_group_id)),
);

const selectedMembers = computed(() =>
    (selectedGroup.value?.members || []).filter((member) =>
        form.participant_ids.includes(member.id),
    ),
);

const sharePreview = computed(() => {
    const total = Number(String(form.total_amount).replace(',', '.'));
    const count = form.participant_ids.length;

    if (!total || !count) return '0.00';

    return (total / count).toFixed(2);
});

watch(
    () => form.whatsapp_group_id,
    () => {
        form.participant_ids = [];
    },
);

function toggleMember(memberId) {
    if (form.participant_ids.includes(memberId)) {
        form.participant_ids = form.participant_ids.filter((id) => id !== memberId);
        return;
    }

    form.participant_ids = [...form.participant_ids, memberId];
}

function submit() {
    form.post(route('accounts.store'), {
        preserveScroll: true,
        onSuccess: () => {
            form.title = '';
            form.description = '';
            form.total_amount = '';
            form.due_date = '';
            form.participant_ids = [];
        },
    });
}

function markPaid(participant) {
    router.put(route('account-participants.paid', participant.id), {}, { preserveScroll: true });
}

function markOpen(participant) {
    router.put(route('account-participants.open', participant.id), {}, { preserveScroll: true });
}
</script>

<template>
    <Head title="Contas" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Contas por grupo
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
                <div
                    v-if="status"
                    class="rounded-md border border-teal-200 bg-teal-50 px-4 py-3 text-sm font-medium text-teal-900"
                >
                    {{ status }}
                </div>

                <section class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
                    <div class="mb-6">
                        <h3 class="text-base font-semibold text-gray-900">Nova conta compartilhada</h3>
                        <p class="mt-1 text-sm text-gray-600">
                            Selecione um grupo sincronizado pelo bot e marque quem participa da divisao.
                        </p>
                    </div>

                    <div
                        v-if="form.hasErrors"
                        class="mb-6 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800"
                    >
                        Confira os campos destacados antes de criar a conta.
                    </div>

                    <form class="grid gap-6 lg:grid-cols-[1fr_1.2fr]" @submit.prevent="submit">
                        <div class="space-y-4">
                            <div>
                                <InputLabel for="group" value="Grupo" />
                                <select
                                    id="group"
                                    v-model="form.whatsapp_group_id"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-teal-700 focus:ring-teal-700"
                                >
                                    <option value="" disabled>Selecione</option>
                                    <option v-for="group in groups" :key="group.id" :value="group.id">
                                        {{ group.name }}
                                    </option>
                                </select>
                                <InputError class="mt-2" :message="form.errors.whatsapp_group_id" />
                            </div>

                            <div>
                                <InputLabel for="responsible" value="Responsavel pelo recebimento" />
                                <select
                                    id="responsible"
                                    v-model="form.responsible_user_id"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-teal-700 focus:ring-teal-700"
                                >
                                    <option value="">Usuario atual</option>
                                    <option v-for="user in users" :key="user.id" :value="user.id">
                                        {{ user.name }} - {{ user.email }}
                                    </option>
                                </select>
                                <InputError class="mt-2" :message="form.errors.responsible_user_id" />
                            </div>

                            <div>
                                <InputLabel for="title" value="Nome da conta" />
                                <TextInput id="title" v-model="form.title" class="mt-1 block w-full" required />
                                <InputError class="mt-2" :message="form.errors.title" />
                            </div>

                            <div>
                                <InputLabel for="amount" value="Valor total" />
                                <TextInput id="amount" v-model="form.total_amount" class="mt-1 block w-full" placeholder="120,00" required />
                                <InputError class="mt-2" :message="form.errors.total_amount" />
                            </div>

                            <div>
                                <InputLabel for="due_date" value="Vencimento" />
                                <TextInput id="due_date" v-model="form.due_date" class="mt-1 block w-full" type="date" />
                                <InputError class="mt-2" :message="form.errors.due_date" />
                            </div>

                            <div>
                                <InputLabel for="description" value="Observacao" />
                                <textarea
                                    id="description"
                                    v-model="form.description"
                                    class="mt-1 block min-h-24 w-full rounded-md border-gray-300 shadow-sm focus:border-teal-700 focus:ring-teal-700"
                                />
                                <InputError class="mt-2" :message="form.errors.description" />
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div class="rounded-md border border-gray-200">
                                <div class="border-b border-gray-200 px-4 py-3">
                                    <div class="text-sm font-semibold text-gray-900">Participantes</div>
                                    <div class="mt-1 text-xs text-gray-500">
                                        {{ selectedMembers.length }} selecionado(s), R$ {{ sharePreview }} por pessoa
                                    </div>
                                </div>

                                <div class="max-h-96 divide-y divide-gray-100 overflow-y-auto">
                                    <button
                                        v-for="member in selectedGroup?.members || []"
                                        :key="member.id"
                                        type="button"
                                        class="flex w-full items-center justify-between gap-3 px-4 py-3 text-left hover:bg-gray-50"
                                        @click="toggleMember(member.id)"
                                    >
                                        <span>
                                            <span class="block text-sm font-medium text-gray-900">
                                                {{ member.name || member.phone || member.whatsapp_id }}
                                            </span>
                                            <span class="block text-xs text-gray-500">
                                                {{ member.phone || 'sem telefone' }} | ID {{ member.whatsapp_id }}
                                            </span>
                                        </span>
                                        <span
                                            class="rounded-full px-2 py-1 text-xs font-semibold"
                                            :class="form.participant_ids.includes(member.id) ? 'bg-teal-100 text-teal-800' : 'bg-gray-100 text-gray-600'"
                                        >
                                            {{ form.participant_ids.includes(member.id) ? 'incluido' : 'fora' }}
                                        </span>
                                    </button>
                                    <div v-if="!selectedGroup?.members?.length" class="px-4 py-6 text-sm text-gray-500">
                                        Nenhum membro sincronizado nesse grupo ainda.
                                    </div>
                                </div>
                            </div>

                            <InputError :message="form.errors.participant_ids" />

                            <div
                                v-if="form.participant_ids.length === 0"
                                class="rounded-md border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800"
                            >
                                Selecione pelo menos uma pessoa para liberar a criacao da conta.
                            </div>

                            <PrimaryButton type="submit" :disabled="form.processing || form.participant_ids.length === 0">
                                {{ form.processing ? 'Criando...' : 'Criar e dividir' }}
                            </PrimaryButton>
                        </div>
                    </form>
                </section>

                <section class="space-y-4">
                    <article
                        v-for="account in accounts"
                        :key="account.id"
                        class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm"
                    >
                        <div class="flex flex-col gap-3 border-b border-gray-200 px-6 py-4 sm:flex-row sm:items-start sm:justify-between">
                            <div>
                                <h3 class="text-base font-semibold text-gray-900">{{ account.title }}</h3>
                                <p class="mt-1 text-sm text-gray-600">
                                    {{ account.group.name }} | Total R$ {{ account.total_amount }} | Cota base R$ {{ account.share_amount }}
                                </p>
                                <p v-if="account.description" class="mt-1 text-sm text-gray-500">{{ account.description }}</p>
                            </div>
                            <span
                                class="inline-flex rounded-full px-3 py-1 text-xs font-semibold"
                                :class="account.status === 'paid' ? 'bg-teal-100 text-teal-800' : 'bg-amber-100 text-amber-800'"
                            >
                                {{ account.status === 'paid' ? 'paga' : 'aberta' }}
                            </span>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Pessoa</th>
                                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Valor</th>
                                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Status</th>
                                        <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500">Acao</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 bg-white">
                                    <tr v-for="participant in account.participants" :key="participant.id">
                                        <td class="px-6 py-4 text-sm text-gray-900">
                                            <div class="font-medium">{{ participant.member.name || participant.member.phone || participant.member.whatsapp_id }}</div>
                                            <div class="text-xs text-gray-500">{{ participant.member.phone }} | ID {{ participant.member.whatsapp_id }}</div>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-700">R$ {{ participant.amount }}</td>
                                        <td class="px-6 py-4 text-sm">
                                            <span v-if="participant.paid_at" class="font-medium text-teal-700">Pago</span>
                                            <span v-else class="font-medium text-amber-700">Pendente</span>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <button
                                                v-if="!participant.paid_at"
                                                type="button"
                                                class="rounded-md bg-teal-700 px-3 py-2 text-sm font-semibold text-white hover:bg-teal-800"
                                                @click="markPaid(participant)"
                                            >
                                                Dar baixa
                                            </button>
                                            <button
                                                v-else
                                                type="button"
                                                class="rounded-md bg-gray-100 px-3 py-2 text-sm font-semibold text-gray-800 hover:bg-gray-200"
                                                @click="markOpen(participant)"
                                            >
                                                Reabrir
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </article>

                    <div v-if="accounts.length === 0" class="rounded-lg border border-gray-200 bg-white px-6 py-8 text-sm text-gray-500 shadow-sm">
                        Nenhuma conta criada ainda. Sincronize um grupo pelo bot e crie a primeira divisao.
                    </div>
                </section>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
