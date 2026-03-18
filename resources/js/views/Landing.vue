<template>
    <div class="landing-page">
        <!-- Transparent Header -->
        <header class="navbar">
            <div class="container d-flex justify-content-between align-items-center py-6">
                <div class="logo">
                    <router-link to="/" class="text-white fw-bolder fs-1 text-decoration-none">
                        Fan<span class="text-primary">Content</span>Hub
                    </router-link>
                </div>
                <nav class="d-none d-md-flex gap-8 align-items-center">
                    <router-link to="/fan-content-hub" class="nav-link text-white opacity-75 opacity-100-hover">Galeri</router-link>
                    <!-- <router-link to="/login" class="btn btn-outline-primary text-white border-white border-opacity-25 glass-hover px-8 py-3">
                        Login Admin
                    </router-link> -->
                </nav>
            </div>
        </header>

        <!-- Hero Section -->
        <section class="hero position-relative d-flex align-items-center overflow-hidden">
            <div class="hero-bg-overlay"></div>
            <div class="container position-relative z-index-2">
                <div class="row align-items-center min-vh-100 py-20">
                    <div class="col-lg-6">
                        <h1 class="display-3 fw-bolder text-white mb-6 animate__animated animate__fadeInUp">
                            Abadikan Setiap <span class="text-primary-gradient">Momen</span> Idol Favoritmu.
                        </h1>
                        <p class="fs-4 text-white opacity-75 mb-10 animate__animated animate__fadeInUp animate__delay-1s">
                            Jelajahi koleksi foto dan video berkualitas tinggi hasil tangkapan penggemar dari berbagai acara terbaru. Hub utama Anda untuk konten idol.
                        </p>
                        <div class="d-flex gap-4 animate__animated animate__fadeInUp animate__delay-2s">
                            <router-link to="/fan-content-hub" class="btn btn-primary btn-lg px-10 py-4 fs-4 fw-bold shadow-lg">
                                Jelajahi Galeri
                            </router-link>
                            <a href="#featured" class="btn btn-outline-light btn-lg px-10 py-4 fs-4 fw-bold border-opacity-25 glass-hover">
                                Lihat Tren
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Featured Section Preview (Now Events) -->
        <section id="featured" class="featured py-20 bg-dark-deep">
            <div class="container">
                <div class="text-center mb-16">
                    <h2 class="display-5 fw-bolder text-white mb-4">Momen Berdasarkan Acara</h2>
                    <p class="text-gray-500 fs-5 text-balance">Pilih acara untuk melihat seluruh koleksi foto dan video dari momen tersebut.</p>
                </div>

                <div v-if="loading" class="text-center py-20">
                    <div class="spinner-border text-primary" role="status"></div>
                </div>

                <div v-else class="row g-8">
                    <div class="col-md-6 col-lg-4" v-for="(event, index) in events" :key="event.id">
                        <EventCard :event="event" :delay="(index * 0.1) + 's'" />
                    </div>
                </div>

                <div class="text-center mt-16" v-if="!loading && events.length > 0">
                    <router-link to="/fan-content-hub" class="btn btn-link text-primary fs-5 fw-bold text-decoration-none p-0">
                        Lihat Semua Koleksi <i class="fas fa-arrow-right ms-2 small"></i>
                    </router-link>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="footer py-10 bg-black">
            <div class="container text-center">
                <p class="text-gray-600 mb-0">© 2024 Fan Content Hub Platform. Seluruh hak cipta dilindungi undang-undang.</p>
            </div>
        </footer>
    </div>
</template>

<script setup lang="ts">
import { onMounted, ref } from 'vue';
import { initializeAppPlugins } from '@/plugins/global';
import api from '@services/api';
import EventCard from '@/components/public/EventCard.vue';
import type { IEvent } from '@/types/fch-event';

const events = ref<IEvent[]>([]);
const loading = ref(true);

onMounted(async () => {
    initializeAppPlugins();
    try {
        const res = await api().get('/api/public/events?limit=6');
        events.value = res.data.data.data;
    } catch (err) {
        console.error('Failed to fetch events:', err);
    } finally {
        loading.value = false;
    }
});
</script>

<style scoped>
.landing-page {
    background-color: #0d0d12;
    font-family: 'Inter', sans-serif;
}

.navbar {
    position: absolute;
    top: 0;
    width: 100%;
    z-index: 100;
}

.hero {
    background-image: url("https://images.unsplash.com/photo-1501281668745-f7f109244f50?q=80&w=2070&auto=format&fit=crop");
    background-size: cover;
    background-position: center;
}

.hero-bg-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, #0d0d12 30%, rgba(13, 13, 18, 0.4) 100%);
    z-index: 1;
}

.text-primary-gradient {
    background: linear-gradient(135deg, #3699ff 0%, #7239ea 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

.glass-hover:hover {
    background: rgba(255, 255, 255, 0.05);
}

.bg-dark-deep {
    background-color: #0f1016;
}

.hover-lift {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.hover-lift:hover {
    transform: translateY(-10px);
    box-shadow: 0 1rem 3rem rgba(0, 0, 0, 0.5) !important;
}

.z-index-2 {
    z-index: 2;
}
</style>
