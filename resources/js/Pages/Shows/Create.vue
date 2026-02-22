<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

// IMPORTANTE: Ahora recibimos 'countries', y asumimos que adentro traen sus 'provinces', 
// y esas 'provinces' traen sus 'cities'.
const props = defineProps({
    countries: Array
});

// --- Estados para alternar entre Select e Input ---
const isAddingCountry = ref(false);
const isAddingProvince = ref(false);
const isAddingCity = ref(false);

const selectedCountryId = ref('');
const selectedProvinceId = ref('');

const form = useForm({
    titulo: '',
    lugar: '',
    direccion: '',
    country_id: '',      // Agregado
    new_country_name: '',// Agregado
    province_id: '',
    new_province_name: '',
    city_id: '', 
    new_city_name: '',
    fecha_hora: '',
    ticket_url: '',
    esta_publicado: true,
    flyer: null,
});

// --- Lógica de Países ---
const handleCountryChange = () => {
    if (selectedCountryId.value === 'new') {
        isAddingCountry.value = true;
        isAddingProvince.value = true; // Si el país es nuevo, la provincia también
        isAddingCity.value = true;     // y la ciudad también
        
        selectedCountryId.value = '';
        form.country_id = '';
        selectedProvinceId.value = '';
        form.province_id = '';
        form.city_id = '';
    } else {
        form.country_id = selectedCountryId.value;
        // Reseteamos cascada hacia abajo
        selectedProvinceId.value = '';
        form.province_id = '';
        form.city_id = '';
        isAddingProvince.value = false;
        form.new_province_name = '';
        isAddingCity.value = false;
        form.new_city_name = '';
    }
};

const cancelNewCountry = () => {
    isAddingCountry.value = false;
    form.new_country_name = '';
    selectedCountryId.value = '';
    form.country_id = '';
};

// --- Lógica de Provincias ---
const filteredProvinces = computed(() => {
    const country = props.countries?.find(c => c.id == selectedCountryId.value);
    return country ? country.provinces : [];
});

const handleProvinceChange = () => {
    if (selectedProvinceId.value === 'new') {
        isAddingProvince.value = true;
        isAddingCity.value = true; // Si la provincia es nueva, la ciudad lógicamente también
        
        selectedProvinceId.value = '';
        form.province_id = '';
        form.city_id = '';
    } else {
        form.province_id = selectedProvinceId.value;
        // Reseteamos la ciudad al cambiar de provincia
        form.city_id = '';
        isAddingCity.value = false;
        form.new_city_name = '';
    }
};

const cancelNewProvince = () => {
    isAddingProvince.value = false;
    form.new_province_name = '';
    selectedProvinceId.value = '';
    form.province_id = '';
};

// --- Lógica de Ciudades ---
const filteredCities = computed(() => {
    const province = filteredProvinces.value.find(p => p.id == selectedProvinceId.value);
    return province ? province.cities : [];
});

const handleCityChange = () => {
    if (form.city_id === 'new') {
        isAddingCity.value = true;
        form.city_id = '';
    }
};

const cancelNewCity = () => {
    isAddingCity.value = false;
    form.new_city_name = '';
    form.city_id = '';
};

const submit = () => {
    form.post(route('shows.store'), {
        forceFormData: true,
        onSuccess: () => {
            form.reset();
            selectedCountryId.value = '';
            selectedProvinceId.value = '';
            isAddingCountry.value = false;
            isAddingProvince.value = false;
            isAddingCity.value = false;
        },
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
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
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
                                <label class="block font-bold text-xs uppercase tracking-widest text-gray-700">Dirección</label>
                                <input v-model="form.direccion" type="text" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            
                            <div>
                                <label class="block font-bold text-xs uppercase tracking-widest text-gray-700">Fecha y Hora</label>
                                <input v-model="form.fecha_hora" type="datetime-local" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 bg-gray-50/50 p-4 rounded-xl border border-gray-200 border-dashed">
                            
                            <div>
                                <label class="block font-bold text-xs uppercase tracking-widest text-gray-700">País</label>
                                
                                <div v-if="!isAddingCountry">
                                    <select v-model="selectedCountryId" @change="handleCountryChange" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 cursor-pointer">
                                        <option value="">Seleccione país</option>
                                        <option v-for="country in countries" :key="country.id" :value="country.id">{{ country.name }}</option>
                                        <option value="new" class="font-bold text-indigo-600 bg-indigo-50">+ Agregar país...</option>
                                    </select>
                                </div>
                                
                                <div v-else class="mt-1 flex gap-2">
                                    <input v-model="form.new_country_name" type="text" placeholder="Nuevo país" class="block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                    <button type="button" @click="cancelNewCountry" class="px-3 bg-red-100 text-red-600 rounded-md hover:bg-red-200 transition-colors text-sm font-bold" title="Cancelar">✕</button>
                                </div>
                            </div>

                            <div>
                                <label class="block font-bold text-xs uppercase tracking-widest text-gray-700">Provincia / Estado</label>
                                
                                <div v-if="!isAddingProvince">
                                    <select v-model="selectedProvinceId" @change="handleProvinceChange" :disabled="!selectedCountryId" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 disabled:opacity-50 cursor-pointer">
                                        <option value="">Seleccione provincia</option>
                                        <option v-for="prov in filteredProvinces" :key="prov.id" :value="prov.id">{{ prov.name }}</option>
                                        <option v-if="selectedCountryId" value="new" class="font-bold text-indigo-600 bg-indigo-50">+ Agregar provincia...</option>
                                    </select>
                                </div>
                                
                                <div v-else class="mt-1 flex gap-2">
                                    <input v-model="form.new_province_name" type="text" placeholder="Nueva provincia" class="block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                    <button type="button" @click="cancelNewProvince" :disabled="isAddingCountry" class="px-3 bg-red-100 text-red-600 rounded-md hover:bg-red-200 transition-colors text-sm font-bold disabled:opacity-50" title="Cancelar">✕</button>
                                </div>
                            </div>

                            <div>
                                <label class="block font-bold text-xs uppercase tracking-widest text-gray-700">Ciudad</label>
                                
                                <div v-if="!isAddingCity">
                                    <select v-model="form.city_id" @change="handleCityChange" :disabled="!selectedProvinceId" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 disabled:opacity-50 cursor-pointer">
                                        <option value="">Seleccione ciudad</option>
                                        <option v-for="city in filteredCities" :key="city.id" :value="city.id">{{ city.name }}</option>
                                        <option v-if="selectedProvinceId" value="new" class="font-bold text-indigo-600 bg-indigo-50">+ Agregar ciudad...</option>
                                    </select>
                                </div>

                                <div v-else class="mt-1 flex gap-2">
                                    <input v-model="form.new_city_name" type="text" placeholder="Nueva ciudad" class="block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                    <button type="button" @click="cancelNewCity" :disabled="isAddingProvince" class="px-3 bg-red-100 text-red-600 rounded-md hover:bg-red-200 transition-colors text-sm font-bold disabled:opacity-50" title="Cancelar">✕</button>
                                </div>
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