<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { useForm, Head, router } from '@inertiajs/vue3';

defineProps({ networks: Array });

const form = useForm({ name: '', url: '' });

const submit = () => {
    form.post(route('redes.store'), {
        onSuccess: () => form.reset()
    });
};

const deleteNet = (id) => {
    if(confirm('¿Eliminar esta red?')) router.delete(route('redes.destroy', id));
};
</script>

<template>
    <AuthenticatedLayout>
        <div class="py-12 max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 rounded-lg shadow mb-6">
                <h3 class="font-bold mb-4">Agregar Nueva Red</h3>
                <form @submit.prevent="submit" class="flex gap-4">
                    <input v-model="form.name" type="text" placeholder="Instagram" required class="rounded-md border-gray-300 flex-1">
                    <input v-model="form.url" type="url" placeholder="https://..." required class="rounded-md border-gray-300 flex-1">
                    <button class="bg-black text-white px-6 py-2 rounded-md font-bold">Agregar</button>
                </form>
            </div>

            <div class="bg-white shadow rounded-lg overflow-hidden">
                <table class="w-full">
                    <tr v-for="net in networks" :key="net.id" class="border-b">
                        <td class="p-4 font-bold">{{ net.name }}</td>
                        <td class="p-4 text-gray-500">{{ net.url }}</td>
                        <td class="p-4 text-right">
                            <button @click="deleteNet(net.id)" class="text-red-500 font-bold">Eliminar</button>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </AuthenticatedLayout>
</template>