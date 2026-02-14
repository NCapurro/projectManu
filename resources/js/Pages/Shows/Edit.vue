<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    show: Object,
    provinces: Array
});

// Inicializamos la provincia según la ciudad que ya tiene el show
const selectedProvinceId = ref(props.show.city.province_id);

const form = useForm({
    /** * TRUCO TÉCNICO: Laravel no procesa archivos en PATCH. 
     * Enviamos como POST y spoofeamos el método.
     */
    _method: 'patch', 
    titulo: props.show.titulo ?? '',
    lugar: props.show.lugar,
    direccion: props.show.direccion ?? '',
    city_id: props.show.city_id,
    // Formateo de fecha para el input datetime-local (YYYY-MM-DDThh:mm)
    fecha_hora: props.show.fecha_hora ? props.show.fecha_hora.substring(0, 16) : '',
    ticket_url: props.show.ticket_url,
    esta_publicado: Boolean(props.show.esta_publicado),
    sold_out: Boolean(props.show.sold_out),
    flyer: null, // Aquí irá el nuevo archivo si se selecciona
});

// Lógica para filtrar ciudades según la provincia seleccionada
const filteredCities = computed(() => {
    const province = props.provinces.find(p => p.id == selectedProvinceId.value);
    return province ? province.cities : [];
});

const submit = () => {
    // Usamos .post() por el manejo de archivos, el _method se encarga del resto
    form.post(route('shows.update', props.show.id), {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => {
            // Opcional: limpiar el input de archivo tras éxito
            form.flyer = null;
        }
    });
};
</script>

<template>
    <Head title="Editar Show" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-black text-xl text-gray-800 leading-tight uppercase italic">
                    Editando Show: <span class="text-indigo-600">{{ show.lugar }}</span>
                </h2>
                <Link :href="route('dashboard')" class="text-xs font-bold text-gray-500 uppercase hover:text-black transition-colors">
                    ← Volver al listado
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-[2rem] p-8 border border-gray-100">
                    
                    <form @submit.prevent="submit" class="space-y-8">
                        
                        <div class="grid grid-cols-1 gap-6">
                            <div>
                                <label class="block font-bold text-xs uppercase tracking-[0.2em] text-gray-400 mb-2">Título del Evento (Opcional)</label>
                                <input v-model="form.titulo" type="text" class="w-full border-gray-200 rounded-xl shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-3" placeholder="Ej: Especial de Stand Up">
                                <div v-if="form.errors.titulo" class="text-red-500 text-xs mt-1 font-bold">{{ form.errors.titulo }}</div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block font-bold text-xs uppercase tracking-[0.2em] text-gray-400 mb-2">Teatro / Bar / Estadio</label>
                                <input v-model="form.lugar" type="text" required class="w-full border-gray-200 rounded-xl shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-3">
                                <div v-if="form.errors.lugar" class="text-red-500 text-xs mt-1 font-bold">{{ form.errors.lugar }}</div>
                            </div>
                            
                            <div>
                                <label class="block font-bold text-xs uppercase tracking-[0.2em] text-gray-400 mb-2">Fecha y Hora</label>
                                <input v-model="form.fecha_hora" type="datetime-local" required class="w-full border-gray-200 rounded-xl shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-3 text-gray-700">
                                <div v-if="form.errors.fecha_hora" class="text-red-500 text-xs mt-1 font-bold">{{ form.errors.fecha_hora }}</div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block font-bold text-xs uppercase tracking-[0.2em] text-gray-400 mb-2">Provincia</label>
                                <select v-model="selectedProvinceId" class="w-full border-gray-200 rounded-xl shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-3">
                                    <option v-for="prov in provinces" :key="prov.id" :value="prov.id">{{ prov.name }}</option>
                                </select>
                            </div>

                            <div>
                                <label class="block font-bold text-xs uppercase tracking-[0.2em] text-gray-400 mb-2">Ciudad</label>
                                <select v-model="form.city_id" required class="w-full border-gray-200 rounded-xl shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-3">
                                    <option v-for="city in filteredCities" :key="city.id" :value="city.id">{{ city.name }}</option>
                                </select>
                                <div v-if="form.errors.city_id" class="text-red-500 text-xs mt-1 font-bold">{{ form.errors.city_id }}</div>
                            </div>
                        </div>

                        <div>
                            <label class="block font-bold text-xs uppercase tracking-[0.2em] text-gray-400 mb-2">URL de Venta (Ticketek, PlateaVía, etc)</label>
                            <input v-model="form.ticket_url" type="url" required class="w-full border-gray-200 rounded-xl shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-3" placeholder="https://...">
                            <div v-if="form.errors.ticket_url" class="text-red-500 text-xs mt-1 font-bold">{{ form.errors.ticket_url }}</div>
                        </div>

                        <div class="border-2 border-dashed border-gray-200 p-8 rounded-[2rem] bg-gray-50/50">
                            <label class="block font-black text-xs text-gray-500 mb-4 uppercase tracking-[0.2em]">Flyer del Show</label>
                            
                            <div class="flex flex-col md:flex-row gap-8 items-center">
                                <div v-if="show.flyer_path && !form.flyer" class="shrink-0">
                                    <p class="text-[10px] text-gray-400 mb-2 font-bold uppercase">Imagen Actual:</p>
                                    <img :src="'/storage/' + show.flyer_path" class="w-28 h-40 object-cover rounded-xl shadow-md border-2 border-white" />
                                </div>

                                <div class="flex-grow w-full">
                                    <p class="text-xs text-gray-500 mb-3 font-medium">Subí una nueva imagen para reemplazar la anterior:</p>
                                    <input type="file" 
                                           @input="form.flyer = $event.target.files[0]" 
                                           accept="image/*"
                                           class="block w-full text-sm text-gray-500 
                                                  file:mr-4 file:py-2 file:px-6 
                                                  file:rounded-full file:border-0 
                                                  file:text-xs file:font-black file:uppercase
                                                  file:bg-indigo-600 file:text-white 
                                                  hover:file:bg-indigo-700 cursor-pointer transition-all shadow-md" />
                                    
                                    <div v-if="form.progress" class="mt-4 w-full bg-gray-200 rounded-full h-1.5 overflow-hidden">
                                        <div class="bg-indigo-600 h-1.5 rounded-full transition-all duration-300" 
                                             :style="{ width: form.progress.percentage + '%' }"></div>
                                    </div>
                                    <p class="text-[9px] text-gray-400 mt-3 uppercase font-bold italic tracking-tighter">Recomendado: 1080x1350px (vertical). Máx 2MB.</p>
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-wrap items-center gap-6 bg-gray-50 p-6 rounded-2xl border border-gray-100">
                            <label class="flex items-center cursor-pointer group">
                                <input type="checkbox" v-model="form.esta_publicado" class="w-5 h-5 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 shadow-sm transition-all">
                                <span class="ms-3 text-sm text-gray-700 font-bold uppercase tracking-tight group-hover:text-black">Visible en la Web</span>
                            </label>

                            <label class="flex items-center cursor-pointer group">
                                <input type="checkbox" v-model="form.sold_out" class="w-5 h-5 rounded border-gray-300 text-red-600 focus:ring-red-500 shadow-sm transition-all">
                                <span class="ms-3 text-sm text-red-600 font-bold uppercase tracking-tight group-hover:text-red-700 italic">¡Entradas Agotadas!</span>
                            </label>
                        </div>

                        <div class="flex items-center justify-end gap-4 pt-6 border-t border-gray-100">
                            <Link :href="route('dashboard')" class="text-xs font-bold text-gray-400 uppercase tracking-widest hover:text-gray-900 transition-colors">
                                Cancelar
                            </Link>
                            <button type="submit" 
                                    :disabled="form.processing" 
                                    class="inline-flex items-center px-10 py-4 bg-black border border-transparent rounded-full font-black text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition-all shadow-xl active:scale-95 disabled:opacity-50">
                                {{ form.processing ? 'Guardando...' : 'Actualizar Show' }}
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>