<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';

const props = defineProps({ images: Array });

const form = useForm({ image: null });

const uploadImage = () => {
    form.post(route('carousel.store'), {
        onSuccess: () => { form.reset(); document.getElementById('fileInput').value = '' },
    });
};

const toggleImage = (id) => router.patch(route('carousel.toggle', id));
const deleteImage = (id) => {
    if(confirm('¿Borrar imagen?')) router.delete(route('carousel.destroy', id));
};
</script>

<template>
    <Head title="Gestor de Portada" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-black text-xl text-gray-800 uppercase italic tracking-tighter">Gestor de Portada</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
                
                <div class="bg-white p-8 rounded-[2rem] shadow-lg border border-gray-100">
                    <h3 class="font-bold text-lg mb-4 text-gray-700">Subir Nueva Foto</h3>
                    <div class="flex gap-4 items-center">
                        <input type="file" id="fileInput" @input="form.image = $event.target.files[0]" accept="image/*" 
                               class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-black file:text-white hover:file:bg-gray-800"/>
                        <button @click="uploadImage" :disabled="form.processing || !form.image" 
                                class="bg-indigo-600 text-white px-6 py-2 rounded-full font-bold uppercase text-xs tracking-widest hover:bg-indigo-700 disabled:opacity-50">
                            {{ form.processing ? 'Subiendo...' : 'Subir' }}
                        </button>
                    </div>
                    <p class="text-xs text-gray-400 mt-2">Recomendado: Imágenes horizontales de alta calidad (1920x1080).</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                    <div v-for="img in images" :key="img.id" class="group relative bg-white rounded-2xl shadow-md overflow-hidden border-2 transition-all"
                         :class="img.is_active ? 'border-green-400' : 'border-gray-200 opacity-60 grayscale'">
                        
                        <img :src="'/storage/' + img.image_path" class="w-full h-48 object-cover">
                        
                        <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-4">
                            <button @click="toggleImage(img.id)" class="bg-white text-black p-2 rounded-full font-bold text-xs uppercase hover:bg-green-400">
                                {{ img.is_active ? 'Ocultar' : 'Mostrar' }}
                            </button>
                            <button @click="deleteImage(img.id)" class="bg-red-600 text-white p-2 rounded-full font-bold text-xs uppercase hover:bg-red-700">
                                Borrar
                            </button>
                        </div>

                        <div class="absolute top-2 right-2 bg-white/80 px-2 py-1 rounded text-[10px] font-bold uppercase">
                            {{ img.is_active ? '✅ Activa' : '🚫 Inactiva' }}
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>