<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch, computed, onMounted, onUnmounted } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue'; 

const props = defineProps({
    shows: Object,
    provinces: Array,
    filters: Object,
    carouselImages: Array,
    videos: Array,
    hero_title: String,
    hero_subtitle: String,
    
});




const currentBgIndex = ref(0);
let bgInterval = null;

const defaultImages = ['/img/header-bg-1.jpg']; // Tu foto por defecto

// Computada: Si hay imágenes en la DB, usalas. Si no, usá la default.
const backgroundImages = computed(() => {
    if (props.carouselImages && props.carouselImages.length > 0) {
        return props.carouselImages.map(img => '/storage/' + img.image_path);
    }
    return defaultImages;
});

// El resto del onMounted sigue igual, usando backgroundImages.value
onMounted(() => {
    bgInterval = setInterval(() => {
        // Importante: usar .value al ser computed
        if (backgroundImages.value.length > 1) {
            currentBgIndex.value = (currentBgIndex.value + 1) % backgroundImages.value.length;
        }
    }, 5000);
});

onUnmounted(() => {
    clearInterval(bgInterval); // Limpieza de memoria
});

// --- ESTADOS ---
const selectedProvinceId = ref(props.filters.province || '');
const selectedCityId = ref(props.filters.city || '');
const selectedDate = ref(props.filters.date || '');
const isResetting = ref(false);
const isMobileMenuOpen = ref(false); // Estado para el menú hamburguesa

// --- LOGICA DE FILTROS ---
const filteredCities = computed(() => {
    const province = props.provinces.find(p => p.id == selectedProvinceId.value);
    return province ? province.cities : [];
});

const applyFilters = () => {
    if (isResetting.value) return; 
    router.get('/', {
        province: selectedProvinceId.value,
        city: selectedCityId.value,
        date: selectedDate.value
    }, { preserveState: true, replace: true, preserveScroll: true });
};

watch(selectedProvinceId, () => {
    if (isResetting.value) return;
    selectedCityId.value = ''; 
    applyFilters();
});
watch(selectedCityId, applyFilters);
watch(selectedDate, applyFilters);

const clearFilters = () => {
    isResetting.value = true; 
    selectedProvinceId.value = '';
    selectedCityId.value = '';
    selectedDate.value = '';
    router.get('/', {}, { onFinish: () => isResetting.value = false });
};

// --- UTILS ---
const toggleMobileMenu = () => {
    isMobileMenuOpen.value = !isMobileMenuOpen.value;
};

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('es-AR', { weekday: 'long', day: 'numeric', month: 'long' });
};

const formatTime = (dateString) => {
    return new Date(dateString).toLocaleTimeString('es-AR', { hour: '2-digit', minute: '2-digit', hour12: false });
};
</script>

<template>
    <Head title="Manu Horazzi - Próximos Shows" />

    <div class="min-h-screen bg-black text-white selection:bg-yellow-500 font-sans">
        
        <nav class="border-b border-white/10 backdrop-blur-md sticky top-0 z-50 bg-black/80">
    <div class="p-6 flex justify-between items-center relative z-50 bg-transparent">
        
        <div class="flex items-center gap-3 shrink-0 relative z-50">
            <ApplicationLogo class="h-8 w-auto fill-current text-white" />
            
            <h1 class="text-xl font-black tracking-tighter italic uppercase text-white">
                MANU HORAZZI
            </h1>
        </div>
        
        <div class="hidden md:flex items-center gap-6">
    <div class="flex items-center gap-6 border-r border-white/10 pr-6 mr-2">
        <a v-for="red in $page.props.social_networks" :key="red.id" :href="red.url" target="_blank"
           class="flex items-center gap-2 text-[14px] font-bold text-gray-400 hover:text-yellow-500 uppercase tracking-widest transition-colors group">
            
            <img v-if="red.icon" :src="'/storage/' + red.icon" 
                 class="w-8 h-8 object-contain opacity-70 group-hover:opacity-100 transition-opacity" 
                 :alt="red.name">
            
            {{ red.name }}
        </a>
    </div>
            <Link v-if="$page.props.auth.user" :href="route('dashboard')" 
                  class="bg-white/10 hover:bg-white/20 text-white px-5 py-2 rounded-full text-[10px] font-bold uppercase tracking-widest transition-all border border-white/10 flex items-center gap-2">
                <span>⚙️ Panel</span>
            </Link>
        </div>

        <button @click="toggleMobileMenu" class="md:hidden text-white focus:outline-none relative z-50 p-2">
            <div v-if="!isMobileMenuOpen">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </div>
            <div v-else>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </div>
        </button>
    </div>

    <div v-show="isMobileMenuOpen" class="md:hidden absolute top-0 left-0 w-full bg-neutral-900 border-b border-white/10 shadow-2xl pt-24 pb-10 px-6 flex flex-col gap-6 z-40">
        <p class="text-xs font-bold text-gray-500 uppercase tracking-widest border-b border-white/5 pb-2">Redes Sociales</p>
       <div class="flex flex-col gap-4">
    <a v-for="red in $page.props.social_networks" :key="red.id" :href="red.url" target="_blank"
       class="text-xl font-black text-white hover:text-yellow-500 uppercase italic tracking-tighter transition-colors flex items-center justify-between group">
        
        <div class="flex items-center gap-3">
            <img v-if="red.icon" :src="'/storage/' + red.icon" 
                 class="w-8 h-8 object-contain" 
                 :alt="red.name">
            {{ red.name }}
        </div>

        <span class="text-gray-600 group-hover:text-yellow-500 text-sm">↗</span>
    </a>
</div>
        <div v-if="$page.props.auth.user" class="mt-4 pt-6 border-t border-white/10">
            <Link :href="route('dashboard')" class="block w-full bg-white text-black py-4 rounded-xl text-center font-black uppercase tracking-widest text-sm hover:bg-yellow-500 transition-all">
                ⚙️ Ir al Panel Admin
            </Link>
        </div>
    </div>
</nav>

        <header class="relative py-32 px-6 text-center overflow-hidden group">
    <div class="absolute inset-0 z-0">
        <TransitionGroup name="fade">
            <div v-for="(img, index) in backgroundImages" 
                 :key="img"
                 v-show="index === currentBgIndex"
                 class="absolute inset-0 w-full h-full">
                
                <img :src="img" 
                     class="w-full h-full object-cover opacity-50 transition-transform duration-[10s] ease-out scale-100 group-hover:scale-110" 
                     alt="Fondo Gira">
                     
            </div>
        </TransitionGroup>
        
        <div class="absolute inset-0 bg-gradient-to-b from-black/60 via-transparent to-black z-10"></div>
    </div>

    <div class="relative z-20">
        <h2 class="text-7xl md:text-9xl font-black uppercase italic mb-4 tracking-tighter text-white drop-shadow-2xl">
            {{ hero_title }}
        </h2>
        <p class="text-xl text-yellow-500 font-medium tracking-widest uppercase drop-shadow-lg bg-black/30 inline-block px-4 py-1 rounded-full backdrop-blur-sm border border-white/10">
            {{ hero_subtitle }}
        </p>
    </div>
</header>

       <main class="max-w-7xl mx-auto px-6 pb-20">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
        
        <div class="lg:col-span-8">
            <div class="flex flex-col gap-6 mb-12 pb-8 border-b border-white/10">
                <div class="flex items-center justify-between">
                    <h3 class="text-2xl font-black italic uppercase tracking-tighter text-yellow-500">Cartelera</h3>
                    <button v-if="selectedProvinceId || selectedDate" @click="clearFilters" class="text-xs uppercase font-bold text-gray-500 hover:text-yellow-500 transition-colors">
                        ✕ Limpiar Filtros
                    </button>
                </div>
                
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 w-full">
                    <select v-model="selectedProvinceId" class="bg-white/5 border-2 border-white/10 text-white rounded-full px-6 py-4 text-sm font-bold focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 focus:bg-black transition-all cursor-pointer">
                        <option value="">Seleccionar Provincia</option>
                        <option v-for="p in provinces" :key="p.id" :value="p.id" class="bg-black text-white">{{ p.name }}</option>
                    </select>
                    <select v-model="selectedCityId" :disabled="!selectedProvinceId" class="bg-white/5 border-2 border-white/10 text-white rounded-full px-6 py-4 text-sm font-bold focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 focus:bg-black disabled:opacity-20 transition-all cursor-pointer">
                        <option value="">Seleccionar Ciudad</option>
                        <option v-for="c in filteredCities" :key="c.id" :value="c.id" class="bg-black text-white">{{ c.name }}</option>
                    </select>
                    <input type="date" v-model="selectedDate" class="bg-white/5 border-2 border-white/10 text-white rounded-full px-6 py-4 text-sm font-bold focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 focus:bg-black appearance-none transition-all cursor-pointer">
                </div>
            </div>

            <div v-if="shows.data.length > 0">
                <div class="divide-y divide-white/10">
                    <div v-for="show in shows.data" :key="show.id" class="py-10 group flex flex-col md:flex-row md:items-center justify-between gap-6 transition-all border-b border-white/5">
                        <div class="flex flex-col md:flex-row gap-6 items-center flex-grow">
                            <div v-if="show.flyer_path" class="w-full md:w-32 h-44 overflow-hidden rounded-lg shadow-2xl border border-white/10 shrink-0">
                                <img :src="'/storage/' + show.flyer_path" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" alt="Flyer">
                            </div>
                            <div class="flex flex-col text-center md:text-left">
                                <span class="text-yellow-500 font-bold uppercase text-sm tracking-widest mb-1">{{ formatDate(show.fecha_hora) }} — {{ formatTime(show.fecha_hora) }} HS</span>
                                <h3 class="text-3xl md:text-5xl font-black group-hover:text-yellow-500 transition-colors uppercase italic text-white leading-tight">{{ show.lugar }}</h3>
                                <p class="text-gray-400 font-medium italic mt-1 text-lg">{{ show.direccion }}</p>
                                <p class="text-gray-400 font-medium italic mt-0 text-lg">{{ show.city.name }}, {{ show.city.province.name }}</p>
                            </div>
                        </div>
                        <div class="flex items-center justify-center md:justify-end shrink-0">
                            <div v-if="show.sold_out" class="border-4 border-red-600 text-red-600 px-8 py-3 rounded-md font-black uppercase rotate-[-5deg] text-xl shadow-lg shadow-red-600/20 whitespace-nowrap">Agotado</div>
                            <a v-else :href="show.ticket_url" target="_blank" class="bg-white text-black hover:bg-yellow-500 transition-all px-10 py-5 rounded-full font-black uppercase tracking-tight flex items-center gap-2 text-md shadow-[0_10px_30px_rgba(255,255,255,0.1)] hover:-translate-y-1 whitespace-nowrap shrink-0">
                                Conseguir Entradas
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="flex justify-center flex-wrap gap-3 mt-16">
                    <template v-for="link in shows.links" :key="link.label">
                        <Link v-if="link.url" :href="link.url" v-html="link.label" class="px-6 py-3 rounded-full transition-all text-sm font-black uppercase tracking-widest" :class="{ 'bg-yellow-500 text-black shadow-[0_0_20px_rgba(234,179,8,0.5)]': link.active, 'text-gray-500 hover:text-white border border-white/10 hover:border-white/30': !link.active }" />
                        <span v-else v-html="link.label" class="px-6 py-3 rounded-full text-gray-800 text-sm font-bold uppercase tracking-widest cursor-not-allowed opacity-30 border border-white/5"></span>
                    </template>
                </div>
            </div>

            <div v-else class="text-center py-32 border-4 border-dashed border-white/5 rounded-[3rem]">
                <p class="text-gray-600 text-2xl font-bold italic">No hay funciones programadas para esta selección.</p>
                <button @click="clearFilters" class="mt-6 bg-yellow-500/10 text-yellow-500 px-8 py-3 rounded-full font-black uppercase text-sm tracking-widest hover:bg-yellow-500 hover:text-black transition-all">Ver todos los shows</button>
            </div>
        </div>

        <div class="lg:col-span-4">
            <div class="sticky top-32 bg-neutral-900/50 p-6 rounded-3xl border border-white/10">
                <div class="flex items-center gap-3 mb-6">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-red-600" fill="currentColor" viewBox="0 0 24 24"><path d="M19.615 3.184c-3.604-.246-11.631-.245-15.23 0-3.897.266-4.356 2.62-4.385 8.816.029 6.185.484 8.549 4.385 8.816 3.6.245 11.626.246 15.23 0 3.897-.266 4.356-2.62 4.385-8.816-.029-6.185-.484-8.549-4.385-8.816zm-10.615 12.816v-8l8 3.993-8 4.007z"/></svg>
                    <h3 class="text-xl font-black italic uppercase tracking-tighter text-white">Stand Up Clips</h3>
                </div>
                
                <div class="flex flex-col gap-6">
    
    <div v-for="video in videos" :key="video.id" class="rounded-xl overflow-hidden border border-white/10 aspect-video bg-black shadow-lg">
        <iframe class="w-full h-full" 
                :src="'https://www.youtube.com/embed/' + video.video_id + '?controls=1'" 
                :title="video.title || 'Video de YouTube'" 
                frameborder="0" 
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                allowfullscreen>
        </iframe>
    </div>

    <div v-if="!videos || videos.length === 0" class="text-center py-8 border-2 border-dashed border-white/10 rounded-xl">
        <p class="text-gray-500 text-sm font-bold uppercase tracking-widest">Próximamente nuevos clips</p>
    </div>

</div>

                <a href="https://youtube.com/@manuhorazzi" target="_blank" class="mt-6 block w-full text-center bg-red-600 hover:bg-red-700 text-white py-3 rounded-full font-bold uppercase tracking-widest text-xs transition-colors">
                    Ir al canal
                </a>
            </div>
        </div>

    </div>
</main>

        <footer class="p-16 border-t border-white/10 text-center bg-neutral-950">
            <div class="flex justify-center flex-wrap gap-12 mb-8">
                <a v-for="red in $page.props.social_networks" :key="red.id" :href="red.url" target="_blank" class="text-gray-500 hover:text-yellow-500 transition-all uppercase tracking-[0.2em] text-sm font-black">
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
/* Transición de fundido */
.fade-enter-active,
.fade-leave-active {
    transition: opacity 1.5s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>