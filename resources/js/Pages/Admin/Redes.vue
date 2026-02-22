<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { useForm, Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';

// Agregamos availableIcons (el backend nos mandará este array con las rutas de las fotos)
const props = defineProps({ 
    networks: Array,
    availableIcons: Array 
});

// Referencia al input de archivo para poder limpiarlo visualmente
const fileInput = ref(null);

// Actualizamos el form para manejar archivo nuevo o ruta existente
const form = useForm({ 
    name: '', 
    url: '',
    icon_file: null,          // Para cuando sube una foto nueva
    selected_icon_path: ''    // Para cuando elige una del pool
});

// --- Lógica del Pool de Íconos ---
const selectIconFromPool = (path) => {
    form.selected_icon_path = path;
    form.icon_file = null; // Limpiamos la foto nueva si elige una del pool
    if (fileInput.value) fileInput.value.value = ''; // Reseteamos el input visual
};

const handleFileUpload = (e) => {
    form.icon_file = e.target.files[0];
    form.selected_icon_path = ''; // Deseleccionamos el pool si sube una foto nueva
};

const submit = () => {
    form.post(route('redes.store'), {
        forceFormData: true, // OBLIGATORIO para enviar archivos por Inertia
        onSuccess: () => {
            form.reset();
            if (fileInput.value) fileInput.value.value = '';
        }
    });
};

const deleteNet = (id) => {
    if(confirm('¿Eliminar esta red?')) router.delete(route('redes.destroy', id));
};
</script>

<template>
    <Head title="Redes Sociales" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-black text-xl text-gray-800 uppercase italic tracking-tighter">Redes Sociales</h2>
        </template>

        <div class="py-12 max-w-5xl mx-auto sm:px-6 lg:px-8">
            
            <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100 mb-8">
                <h3 class="font-bold mb-6 text-gray-700 uppercase tracking-widest text-sm">Agregar Nueva Red</h3>
                
                <form @submit.prevent="submit" class="flex flex-col gap-6">
                    <div class="flex flex-col md:flex-row gap-4">
                        <input v-model="form.name" type="text" placeholder="Ej: Instagram" required class="rounded-xl border-gray-300 flex-1 focus:ring-yellow-500 focus:border-yellow-500">
                        <input v-model="form.url" type="url" placeholder="https://instagram.com/..." required class="rounded-xl border-gray-300 flex-1 focus:ring-yellow-500 focus:border-yellow-500">
                    </div>

                    <div class="border-t border-gray-100 pt-6">
                        <label class="block font-bold text-xs uppercase tracking-widest text-gray-700 mb-4">Ícono de la red</label>
                        
                        <div class="flex flex-col md:flex-row gap-8 items-start">
                            
                            <div class="flex-1 bg-gray-50 p-4 rounded-2xl border-2 border-dashed border-gray-200 w-full">
                                <p class="text-xs text-gray-500 mb-2 font-bold uppercase">Subir icono nuevo:</p>
                                <input type="file" ref="fileInput" @change="handleFileUpload" accept="image/*"
                                       class="block w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:font-bold file:bg-black file:text-white hover:file:bg-gray-800 cursor-pointer">
                            </div>

                            <div v-if="availableIcons && availableIcons.length > 0" class="flex-1 w-full">
                                <p class="text-xs text-gray-500 mb-2 font-bold uppercase">O elegir uno ya subido:</p>
                                <div class="flex flex-wrap gap-3 bg-gray-50 p-4 rounded-2xl border border-gray-100 max-h-40 overflow-y-auto">
                                    <button type="button" v-for="icon in availableIcons" :key="icon"
                                            @click="selectIconFromPool(icon)"
                                            class="relative p-2 rounded-xl transition-all hover:scale-105"
                                            :class="form.selected_icon_path === icon ? 'bg-yellow-100 ring-2 ring-yellow-500 shadow-md' : 'bg-white shadow-sm border border-gray-200'">
                                        
                                        <img :src="'/storage/' + icon" class="w-8 h-8 object-contain" alt="Icono">
                                        
                                        <div v-if="form.selected_icon_path === icon" class="absolute -top-2 -right-2 bg-yellow-500 text-black rounded-full w-5 h-5 flex items-center justify-center text-xs font-black shadow-sm">
                                            ✓
                                        </div>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end pt-4">
                        <button type="submit" :disabled="form.processing" class="bg-black text-white px-8 py-3 rounded-full font-black uppercase tracking-widest text-xs hover:bg-gray-800 transition-all disabled:opacity-50">
                            {{ form.processing ? 'Guardando...' : 'Guardar Red' }}
                        </button>
                    </div>
                </form>
            </div>

            <div class="bg-white shadow-sm rounded-3xl overflow-hidden border border-gray-100">
                <table class="w-full text-left">
                    <thead class="bg-gray-50 border-b border-gray-100 text-xs uppercase tracking-widest text-gray-500">
                        <tr>
                            <th class="p-4 font-bold">Red Social</th>
                            <th class="p-4 font-bold">Enlace</th>
                            <th class="p-4 font-bold text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="net in networks" :key="net.id" class="border-b border-gray-50 hover:bg-gray-50/50 transition-colors">
                            <td class="p-4 font-black flex items-center gap-3">
                                <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center p-1 border border-gray-200">
                                    <img v-if="net.icon" :src="'/storage/' + net.icon" class="w-full h-full object-contain">
                                </div>
                                {{ net.name }}
                            </td>
                            <td class="p-4 text-gray-500 text-sm"><a :href="net.url" target="_blank" class="hover:text-yellow-600 underline decoration-gray-300 underline-offset-4">{{ net.url }}</a></td>
                            <td class="p-4 text-right">
                                <button @click="deleteNet(net.id)" class="text-red-500 font-bold text-xs uppercase tracking-widest hover:text-red-700 bg-red-50 px-4 py-2 rounded-full transition-colors">Eliminar</button>
                            </td>
                        </tr>
                        <tr v-if="networks.length === 0">
                            <td colspan="3" class="p-8 text-center text-gray-400 italic font-medium">No hay redes sociales cargadas aún.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AuthenticatedLayout>
</template>