<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    shows: Array
});

// --- LÓGICA DEL BUSCADOR ---
const search = ref('');

const filteredShows = computed(() => {
    return props.shows.filter(show => {
        const term = search.value.toLowerCase();
        return show.lugar.toLowerCase().includes(term) || 
               show.city.name.toLowerCase().includes(term) ||
               (show.titulo && show.titulo.toLowerCase().includes(term));
    });
});

// --- ACCIONES RÁPIDAS ---
const toggleSoldOut = (id) => {
    router.patch(route('shows.toggle-sold-out', id), {}, {
        preserveScroll: true, // Evita que la pantalla salte arriba al hacer click
    });
};

const deleteShow = (id) => {
    if (confirm('¿Borrar este show?')) {
        router.delete(route('shows.destroy', id));
    }
};
</script>

<template>
    <Head title="Panel de Control" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Gestión de Shows</h2>
                
                <div class="relative w-full md:w-96">
                    <input v-model="search" type="text" placeholder="Buscar por teatro o ciudad..." 
                           class="w-full border-gray-300 rounded-full px-4 py-2 focus:ring-indigo-500 shadow-sm text-sm">
                    <span class="absolute right-4 top-2 text-gray-400">🔍</span>
                </div>

                <Link :href="route('shows.create')" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition font-bold text-sm">
                    + Nuevo Show
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div v-if="$page.props.flash?.message" class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg border border-green-200">
                    {{ $page.props.flash.message }}
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Fecha</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Lugar / Ciudad</th>
                                <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase">Estado</th>
                                <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="show in filteredShows" :key="show.id" class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ new Date(show.fecha_hora).toLocaleDateString('es-AR') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <div class="font-bold text-gray-900">{{ show.lugar }}</div>
                                    <div class="text-gray-500 text-xs">{{ show.city.name }}, {{ show.city.province.name }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span v-if="show.sold_out" class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-xs font-bold uppercase">Agotado</span>
                                    <span v-else class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-bold uppercase">Disponible</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <button @click="toggleSoldOut(show.id)" 
                                            :class="show.sold_out ? 'bg-orange-100 text-orange-600 hover:bg-orange-200' : 'bg-red-100 text-red-600 hover:bg-red-200'"
                                            class="px-3 py-1 rounded mr-4 transition-colors font-bold text-xs uppercase shadow-sm border border-transparent">
                                        {{ show.sold_out ? 'Habilitar' : 'Marcar Agotado' }}
                                    </button>

                                    <Link :href="route('shows.edit', show.id)" class="text-indigo-600 hover:text-indigo-900 mr-4 font-bold uppercase text-xs">Editar</Link>
                                    
                                    <button @click="deleteShow(show.id)" class="text-gray-400 hover:text-red-600 font-bold uppercase text-xs">Eliminar</button>
                                </td>
                            </tr>
                            <tr v-if="filteredShows.length === 0">
                                <td colspan="4" class="px-6 py-10 text-center text-gray-500 italic">No se encontraron shows que coincidan con la búsqueda.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>