<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const props = defineProps({
    shows: Object, // Ahora es un objeto de paginación de Laravel
    filters: Object
});

// --- LÓGICA DEL BUSCADOR (Ahora por Backend para soportar paginación) ---
const search = ref(props.filters?.search || '');

watch(search, (value) => {
    router.get(route('dashboard'), { search: value }, {
        preserveState: true,
        replace: true,
        preserveScroll: true
    });
});

// --- ACCIONES RÁPIDAS ---
const toggleSoldOut = (id) => {
    router.patch(route('shows.toggle-sold-out', id), {}, {
        preserveScroll: true,
    });
};

const toggleVisibility = (id) => {
    router.patch(route('shows.toggle-visibility', id), {}, {
        preserveScroll: true,
    });
};

const deleteShow = (id) => {
    if (confirm('¿Estás seguro de borrar este show? Se eliminará también el flyer del servidor.')) {
        router.delete(route('shows.destroy', id));
    }
};

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('es-AR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    });
};
</script>

<template>
    <Head title="Gestión de Shows - Admin" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                <h2 class="font-black text-xl text-gray-800 leading-tight uppercase italic tracking-tighter">
                    Gestión de Cartelera
                </h2>
                
                <div class="relative w-full md:w-96">
                    <input v-model="search" type="text" placeholder="Buscar teatro, ciudad o título..." 
                           class="w-full border-gray-300 rounded-full px-5 py-2 focus:ring-indigo-500 shadow-sm text-sm border-2">
                    <span class="absolute right-4 top-2.5 text-gray-400">🔍</span>
                </div>

                <Link :href="route('shows.create')" class="bg-black text-white px-6 py-2 rounded-full hover:bg-indigo-600 transition-all font-black text-xs uppercase tracking-widest shadow-lg">
                    + Nuevo Show
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                
                <div v-if="$page.props.flash?.message" class="mb-6 p-4 bg-indigo-600 text-white rounded-2xl shadow-lg font-bold text-sm flex justify-between items-center animate-pulse">
                    <span>✨ {{ $page.props.flash.message }}</span>
                </div>

                <div class="bg-white overflow-hidden shadow-xl sm:rounded-[2rem] border border-gray-100">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Fecha</th>
                                    <th class="px-6 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Info del Evento</th>
                                    <th class="px-6 py-4 text-center text-[10px] font-black text-gray-400 uppercase tracking-widest">Estados</th>
                                    <th class="px-6 py-4 text-right text-[10px] font-black text-gray-400 uppercase tracking-widest">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                                <tr v-for="show in shows.data" :key="show.id" class="hover:bg-gray-50/50 transition-colors group">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-600">
                                        {{ formatDate(show.fecha_hora) }}
                                    </td>

                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div v-if="show.flyer_path" class="w-10 h-12 rounded shadow-sm border border-gray-200 overflow-hidden shrink-0 hidden sm:block">
                                                <img :src="'/storage/' + show.flyer_path" class="w-full h-full object-cover">
                                            </div>
                                            <div class="min-w-0">
                                                <div class="font-black text-gray-900 uppercase italic truncate">{{ show.lugar }}</div>
                                                <div class="text-gray-500 text-xs font-medium">{{ show.city.name }}, {{ show.city.province.name }}</div>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <div class="flex flex-col items-center gap-1.5">
                                            <span v-if="!show.esta_publicado" class="bg-gray-100 text-gray-500 border border-gray-200 px-3 py-0.5 rounded-full text-[9px] font-black uppercase italic">
                                                Borrador
                                            </span>
                                            <span v-else class="bg-blue-100 text-blue-700 px-3 py-0.5 rounded-full text-[9px] font-black uppercase shadow-sm">
                                                En línea
                                            </span>

                                            <span v-if="show.sold_out" class="bg-red-100 text-red-700 px-3 py-0.5 rounded-full text-[9px] font-black uppercase">
                                                Agotado
                                            </span>
                                            <span v-else class="bg-green-100 text-green-700 px-3 py-0.5 rounded-full text-[9px] font-black uppercase">
                                                Cupos OK
                                            </span>
                                        </div>
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <button @click="toggleVisibility(show.id)" 
                                                    class="text-[10px] font-black uppercase tracking-tighter transition-colors px-2 py-1 rounded"
                                                    :class="show.esta_publicado ? 'text-gray-400 hover:text-gray-600' : 'text-blue-600 hover:text-blue-800 underline'">
                                                {{ show.esta_publicado ? 'Ocultar' : 'Publicar' }}
                                            </button>

                                            <button @click="toggleSoldOut(show.id)" 
                                                    :class="show.sold_out ? 'bg-orange-100 text-orange-600 hover:bg-orange-200' : 'bg-red-600 text-white hover:bg-red-700'"
                                                    class="px-3 py-1.5 rounded-lg transition-all font-black text-[9px] uppercase shadow-sm whitespace-nowrap shrink-0">
                                                {{ show.sold_out ? 'Habilitar' : '¡Agotar!' }}
                                            </button>

                                            <Link :href="route('shows.edit', show.id)" class="bg-gray-100 text-gray-600 hover:bg-black hover:text-white p-2 rounded-lg transition-all shadow-sm">
                                                ✏️
                                            </Link>
                                            
                                            <button @click="deleteShow(show.id)" class="bg-gray-50 text-gray-300 hover:bg-red-50 hover:text-red-600 p-2 rounded-lg transition-all">
                                                🗑️
                                            </button>
                                        </div>
                                    </td>
                                </tr>

                                <tr v-if="shows.data.length === 0">
                                    <td colspan="4" class="px-6 py-20 text-center text-gray-400 font-bold uppercase tracking-widest text-xs italic">
                                        No se encontraron shows... 🎭
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-center gap-2">
                        <template v-for="link in shows.links" :key="link.label">
                            <Link 
                                v-if="link.url"
                                :href="link.url"
                                v-html="link.label"
                                class="px-4 py-2 rounded-xl text-xs font-black uppercase tracking-widest transition-all"
                                :class="{ 
                                    'bg-black text-white shadow-lg': link.active,
                                    'text-gray-400 hover:bg-white hover:text-black border border-transparent hover:border-gray-200': !link.active
                                }"
                            />
                            <span v-else v-html="link.label" class="px-4 py-2 text-xs font-bold text-gray-300 uppercase italic opacity-50"></span>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>