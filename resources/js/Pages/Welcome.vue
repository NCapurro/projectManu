<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch, computed } from 'vue';

const props = defineProps({
    shows: Object,
    provinces: Array,
    filters: Object,
});

// Estados locales vinculados a los filtros
const selectedProvinceId = ref(props.filters.province || '');
const selectedCityId = ref(props.filters.city || '');
const selectedDate = ref(props.filters.date || '');
const isResetting = ref(false); 

// Lógica para ciudades dependientes de la provincia elegida
const filteredCities = computed(() => {
    const province = props.provinces.find(p => p.id == selectedProvinceId.value);
    return province ? province.cities : [];
});

// Función centralizada para aplicar filtros en la URL
const applyFilters = () => {
    if (isResetting.value) return; 

    router.get('/', {
        province: selectedProvinceId.value,
        city: selectedCityId.value,
        date: selectedDate.value
    }, { 
        preserveState: true, 
        replace: true,
        preserveScroll: true 
    });
};

// Observadores para reaccionar a los cambios de los inputs
watch(selectedProvinceId, (newVal) => {
    if (isResetting.value) return;
    selectedCityId.value = ''; // Limpiar ciudad al cambiar provincia
    applyFilters();
});

watch(selectedCityId, () => {
    applyFilters();
});

watch(selectedDate, () => {
    applyFilters();
});

// Función para limpiar filtros y volver a la URL base /
const clearFilters = () => {
    isResetting.value = true; 

    selectedProvinceId.value = '';
    selectedCityId.value = '';
    selectedDate.value = '';

    // Navegamos al index vacío
    router.get('/', {}, {
        onFinish: () => {
            isResetting.value = false; 
        }
    });
};

// Formateadores de fecha y hora
const formatDate = (dateString) => {
    const date = new Date(dateString);
    return date.toLocaleDateString('es-AR', {
        weekday: 'long', day: 'numeric', month: 'long',
    });
};

const formatTime = (dateString) => {
    return new Date(dateString).toLocaleTimeString('es-AR', {
        hour: '2-digit',
        minute: '2-digit',
        hour12: false // <--- Esto elimina el AM/PM y fuerza las 24hs
    });
};
</script>

<template>
    <Head title="Manu Horazzi - Próximos Shows" />

    <div class="min-h-screen bg-black text-white selection:bg-yellow-500 font-sans">
        <nav class="p-6 flex justify-between items-center border-b border-white/10 backdrop-blur-md sticky top-0 z-50 bg-black/50">
            <h1 class="text-xl font-black tracking-tighter italic uppercase text-white">MANU HORAZZI</h1>
        </nav>

        <header class="py-20 px-6 text-center bg-gradient-to-b from-yellow-900/20 to-black">
            <h2 class="text-7xl md:text-9xl font-black uppercase italic mb-4 tracking-tighter text-white">Stand Up</h2>
            <p class="text-xl text-yellow-500 font-medium tracking-widest uppercase">Gira 2026</p>
        </header>

        <main class="max-w-5xl mx-auto px-6 pb-20">
            <div class="flex flex-col gap-6 mb-12 pb-8 border-b border-white/10">
                <div class="flex items-center justify-between">
                    <h3 class="text-2xl font-black italic uppercase tracking-tighter text-yellow-500">Cartelera</h3>
                    <button v-if="selectedProvinceId || selectedDate" 
                            @click="clearFilters" 
                            class="text-xs uppercase font-bold text-gray-500 hover:text-yellow-500 transition-colors">
                        ✕ Limpiar Filtros
                    </button>
                </div>
                
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 w-full">
                    <select v-model="selectedProvinceId" 
                            class="bg-white/5 border-2 border-white/10 text-white rounded-full px-6 py-4 text-sm font-bold focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 focus:bg-black transition-all cursor-pointer">
                        <option value="">Seleccionar Provincia</option>
                        <option v-for="p in provinces" :key="p.id" :value="p.id" class="bg-black text-white">{{ p.name }}</option>
                    </select>

                    <select v-model="selectedCityId" 
                            :disabled="!selectedProvinceId" 
                            class="bg-white/5 border-2 border-white/10 text-white rounded-full px-6 py-4 text-sm font-bold focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 focus:bg-black disabled:opacity-20 transition-all cursor-pointer">
                        <option value="">Seleccionar Ciudad</option>
                        <option v-for="c in filteredCities" :key="c.id" :value="c.id" class="bg-black text-white">{{ c.name }}</option>
                    </select>

                    <input type="date" v-model="selectedDate" 
                           class="bg-white/5 border-2 border-white/10 text-white rounded-full px-6 py-4 text-sm font-bold focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 focus:bg-black appearance-none transition-all cursor-pointer">
                </div>
            </div>

            <div v-if="shows.data.length > 0">
                <div class="divide-y divide-white/10">
                    <div v-for="show in shows.data" :key="show.id" 
                         class="py-10 group flex flex-col md:flex-row md:items-center justify-between gap-6 transition-all border-b border-white/5">
                        
                        <div class="flex flex-col md:flex-row gap-6 items-center flex-grow">
                            <div v-if="show.flyer_path" class="w-full md:w-32 h-44 overflow-hidden rounded-lg shadow-2xl border border-white/10 shrink-0">
                                <img :src="'/storage/' + show.flyer_path" 
                                     class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" 
                                     alt="Flyer">
                            </div>

                            <div class="flex flex-col">
                                <span class="text-yellow-500 font-bold uppercase text-sm tracking-widest mb-1 text-center md:text-left">
    {{ formatDate(show.fecha_hora) }} — {{ formatTime(show.fecha_hora) }} HS
</span>
                                <h3 class="text-3xl md:text-5xl font-black group-hover:text-yellow-500 transition-colors uppercase italic text-white leading-tight text-center md:text-left">
                                    {{ show.lugar }}
                                </h3>
                                <p class="text-gray-400 font-medium italic mt-2 text-lg text-center md:text-left">
                                    {{ show.city.name }}, {{ show.city.province.name }}
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center justify-center md:justify-end shrink-0">
                            <div v-if="show.sold_out" class="border-4 border-red-600 text-red-600 px-8 py-3 rounded-md font-black uppercase rotate-[-5deg] text-xl shadow-lg shadow-red-600/20 whitespace-nowrap">
                                Agotado
                            </div>
                            <a v-else :href="show.ticket_url" target="_blank" 
                               class="bg-white text-black hover:bg-yellow-500 transition-all px-10 py-5 rounded-full font-black uppercase tracking-tight flex items-center gap-2 text-md shadow-[0_10px_30px_rgba(255,255,255,0.1)] hover:-translate-y-1 whitespace-nowrap shrink-0">
                                Conseguir Entradas
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="flex justify-center flex-wrap gap-3 mt-16">
                    <template v-for="link in shows.links" :key="link.label">
                        <Link 
                            v-if="link.url"
                            :href="link.url"
                            v-html="link.label"
                            class="px-6 py-3 rounded-full transition-all text-sm font-black uppercase tracking-widest"
                            :class="{ 
                                'bg-yellow-500 text-black shadow-[0_0_20px_rgba(234,179,8,0.5)]': link.active,
                                'text-gray-500 hover:text-white border border-white/10 hover:border-white/30': !link.active
                            }"
                        />
                        <span 
                            v-else
                            v-html="link.label"
                            class="px-6 py-3 rounded-full text-gray-800 text-sm font-bold uppercase tracking-widest cursor-not-allowed opacity-30 border border-white/5"
                        ></span>
                    </template>
                </div>
            </div>

            <div v-else class="text-center py-32 border-4 border-dashed border-white/5 rounded-[3rem]">
                <p class="text-gray-600 text-2xl font-bold italic">No hay funciones programadas para esta selección.</p>
                <button @click="clearFilters" class="mt-6 bg-yellow-500/10 text-yellow-500 px-8 py-3 rounded-full font-black uppercase text-sm tracking-widest hover:bg-yellow-500 hover:text-black transition-all">Ver todos los shows</button>
            </div>
        </main>

        <footer class="p-16 border-t border-white/10 text-center bg-neutral-950">
            <div class="flex justify-center flex-wrap gap-12 mb-8">
                <a v-for="red in $page.props.social_networks" 
                   :key="red.id" 
                   :href="red.url" 
                   target="_blank" 
                   class="text-gray-500 hover:text-yellow-500 transition-all uppercase tracking-[0.2em] text-sm font-black">
                    {{ red.name }}
                </a>
            </div>
            <p class="text-[11px] text-gray-700 uppercase tracking-[0.4em] font-medium">Desarrollado por NC SOFTWARE — 2026</p>
        </footer>
    </div>
</template>

<style scoped>
input[type="date"]::-webkit-calendar-picker-indicator {
    filter: invert(1);
    cursor: pointer;
    scale: 1.5;
}
</style>