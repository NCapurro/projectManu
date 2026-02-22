<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { useForm, Head, router } from '@inertiajs/vue3';

defineProps({ videos: Array });

const form = useForm({ 
    title: '', 
    url: '' 
});

const submit = () => {
    form.post(route('videos.store'), {
        onSuccess: () => form.reset()
    });
};

const deleteVideo = (id) => {
    if(confirm('¿Eliminar este video de la cartelera?')) router.delete(route('videos.destroy', id));
};
</script>

<template>
    <Head title="Gestión de Videos" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-black text-xl text-gray-800 uppercase italic tracking-tighter">Videos de YouTube</h2>
        </template>

        <div class="py-12 max-w-5xl mx-auto sm:px-6 lg:px-8">
            
            <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100 mb-8">
                <h3 class="font-bold mb-6 text-gray-700 uppercase tracking-widest text-sm">Agregar Nuevo Clip</h3>
                <form @submit.prevent="submit" class="flex flex-col md:flex-row gap-4">
                    <input v-model="form.title" type="text" placeholder="Título (Ej: Monólogo sobre el calor)" class="rounded-xl border-gray-300 flex-1 focus:ring-red-500 focus:border-red-500">
                    <input v-model="form.url" type="url" placeholder="Pegar link de YouTube acá..." required class="rounded-xl border-gray-300 flex-1 focus:ring-red-500 focus:border-red-500">
                    <button type="submit" :disabled="form.processing" class="bg-red-600 text-white px-8 py-3 rounded-xl font-black uppercase tracking-widest text-xs hover:bg-red-700 transition-all disabled:opacity-50">
                        {{ form.processing ? 'Guardando...' : 'Agregar Video' }}
                    </button>
                </form>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div v-for="video in videos" :key="video.id" class="bg-white p-4 rounded-3xl shadow-sm border border-gray-100 flex flex-col gap-4">
                    <div class="rounded-xl overflow-hidden aspect-video bg-black">
                        <iframe class="w-full h-full" :src="'https://www.youtube.com/embed/' + video.video_id" frameborder="0" allowfullscreen></iframe>
                    </div>
                    <div class="flex justify-between items-center px-2">
                        <span class="font-bold text-gray-700 truncate">{{ video.title || 'Sin título' }}</span>
                        <button @click="deleteVideo(video.id)" class="text-red-500 font-bold text-xs uppercase tracking-widest hover:text-red-700 bg-red-50 px-4 py-2 rounded-full transition-colors">Eliminar</button>
                    </div>
                </div>
            </div>

        </div>
    </AuthenticatedLayout>
</template>