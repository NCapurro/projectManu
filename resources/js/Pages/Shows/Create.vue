<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    provinces: Array
});

const selectedProvinceId = ref('');

const form = useForm({
    titulo: '',
    lugar: '',
    direccion: '',
    city_id: '', 
    fecha_hora: '',
    ticket_url: '',
    esta_publicado: true, // Por defecto visible
    flyer: null,
});

const filteredCities = computed(() => {
    const province = props.provinces.find(p => p.id == selectedProvinceId.value);
    return province ? province.cities : [];
});

const submit = () => {
    form.post(route('shows.store'), {
        forceFormData: true,
        onSuccess: () => form.reset(),
    });
};
</script>

<template>
    <Head title="Nuevo Show" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Cargar Nuevo Show</h2>
        </template>

        <div class="py-12">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-3xl p-8 border border-gray-100">
                    
                    <form @submit.prevent="submit" class="space-y-6">
                        <div>
                            <label class="block font-bold text-xs uppercase tracking-widest text-gray-700">Título del Show (Opcional)</label>
                            <input v-model="form.titulo" type="text" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Ej: Especial de Verano">
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block font-bold text-xs uppercase tracking-widest text-gray-700">Teatro / Bar</label>
                                <input v-model="form.lugar" type="text" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            
                            <div>
                                <label class="block font-bold text-xs uppercase tracking-widest text-gray-700">Fecha y Hora</label>
                                <input v-model="form.fecha_hora" type="datetime-local" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block font-bold text-xs uppercase tracking-widest text-gray-700">Provincia</label>
                                <select v-model="selectedProvinceId" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Seleccione una provincia</option>
                                    <option v-for="prov in provinces" :key="prov.id" :value="prov.id">{{ prov.name }}</option>
                                </select>
                            </div>

                            <div>
                                <label class="block font-bold text-xs uppercase tracking-widest text-gray-700">Ciudad</label>
                                <select v-model="form.city_id" :disabled="!selectedProvinceId" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 disabled:opacity-50">
                                    <option value="">Seleccione una ciudad</option>
                                    <option v-for="city in filteredCities" :key="city.id" :value="city.id">{{ city.name }}</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block font-bold text-xs uppercase tracking-widest text-gray-700">Link a Ticketera (Venta de entradas)</label>
                            <input v-model="form.ticket_url" type="url" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="https://...">
                        </div>

                        <div class="border-2 border-dashed border-gray-200 p-6 rounded-2xl bg-gray-50/50">
                            <label class="block font-black text-xs text-gray-700 mb-2 uppercase tracking-widest">
                                Flyer del Show (Imagen)
                            </label>
                            
                            <input type="file" 
                                   @input="form.flyer = $event.target.files[0]" 
                                   accept="image/*"
                                   class="block w-full text-sm text-gray-500 
                                          file:mr-4 file:py-2 file:px-4 
                                          file:rounded-full file:border-0 
                                          file:text-xs file:font-black file:uppercase
                                          file:bg-black file:text-white 
                                          hover:file:bg-gray-800 cursor-pointer transition-all" />
                            
                            <div v-if="form.progress" class="mt-4 w-full bg-gray-200 rounded-full h-1.5">
                                <div class="bg-indigo-600 h-1.5 rounded-full transition-all duration-300" 
                                     :style="{ width: form.progress.percentage + '%' }"></div>
                            </div>
                        </div>

                        <div class="bg-indigo-50/50 p-4 rounded-xl border border-indigo-100">
                            <label class="flex items-center cursor-pointer group">
                                <input type="checkbox" 
                                       v-model="form.esta_publicado" 
                                       class="w-5 h-5 rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 transition-all">
                                <span class="ms-3 text-sm font-bold uppercase tracking-tight text-gray-700 group-hover:text-indigo-700">
                                    Visible en la Web
                                </span>
                            </label>
                            <p class="text-[10px] text-gray-400 mt-1 ms-8 uppercase font-medium">
                                Si está desactivado, el show se guardará pero el público no podrá verlo aún.
                            </p>
                        </div>

                        <div class="flex items-center justify-end mt-4 pt-4 border-t border-gray-100">
                            <Link :href="route('dashboard')" class="text-sm font-bold text-gray-500 uppercase tracking-widest hover:text-gray-900 mr-6 transition-colors">
                                Cancelar
                            </Link>
                            <button type="submit" 
                                    :disabled="form.processing" 
                                    class="inline-flex items-center px-8 py-3 bg-black border border-transparent rounded-full font-black text-xs text-white uppercase tracking-widest hover:bg-gray-800 transition-all shadow-lg active:scale-95 disabled:opacity-70">
                                {{ form.processing ? 'Guardando...' : 'Guardar Show' }}
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>