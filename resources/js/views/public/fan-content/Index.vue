<template>
    <div class="d-flex flex-column flex-root bg-dark-page min-vh-100">
        <!-- Header -->
        <header class="navbar-glass py-6 sticky-top">
            <div class="container-xxl d-flex align-items-center justify-content-between">
                <router-link to="/" class="text-decoration-none d-flex align-items-center">
                    <h1 class="text-white fw-bolder mb-0 fs-2">
                        Fan<span class="text-primary-gradient">Content</span>Hub
                    </h1>
                </router-link>
                <div class="d-none d-md-flex align-items-center gap-6">
                    <router-link to="/" class="nav-link-modern">Beranda</router-link>
                    <!-- <router-link to="/login" class="btn btn-sm btn-outline-primary-glass px-5">Login Admin</router-link> -->
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-grow-1 py-12">
            <div class="container-xxl">
                
                <!-- Hero/Intro Section -->
                <div class="mb-15 text-center text-lg-start">
                    <h2 class="display-6 fw-bolder text-white mb-3">Jelajahi Koleksi</h2>
                    <p class="text-gray-500 fs-5 mb-0">Temukan foto dan video berkualitas tinggi dari acara idol terbaru.</p>
                </div>

                <!-- Search & Filters -->
                <div class="filters-container glass-card p-6 p-lg-8 mb-12 animate__animated animate__fadeInDown">
                    <div class="row g-6 align-items-end">
                        <div class="col-lg-5">
                            <label class="form-label fw-bold text-gray-400 mb-3">Apa yang Anda cari?</label>
                            <div class="position-relative">
                                <span class="position-absolute top-50 translate-middle-y ms-4">
                                    <i class="fas fa-search text-gray-500 fs-5"></i>
                                </span>
                                <input type="text" class="form-control form-control-dark ps-12 py-4" 
                                    v-model="publicStore.table.search" 
                                    placeholder="Cari idol, artis, atau acara..." 
                                    @keyup.enter="handleSearch">
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <label class="form-label fw-bold text-gray-400 mb-3">Tahun Acara</label>
                            <select class="form-select form-select-dark py-4" v-model="publicStore.filters.year" @change="handleSearch">
                                <option value="">Semua Tahun</option>
                                <option value="2024">2024</option>
                                <option value="2023">2023</option>
                                <option value="2022">2022</option>
                            </select>
                        </div>
                        <div class="col-lg-2 col-md-6">
                            <button type="button" class="btn btn-primary w-100 py-4 fw-bold" @click="handleSearch">
                                Temukan Konten
                            </button>
                        </div>
                        <div class="col-lg-2">
                            <button type="button" class="btn btn-link text-gray-500 w-100 py-4 text-decoration-none" @click="resetFilters">
                                Reset
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Gallery Grid -->
                <div class="position-relative">
                    <!-- Loading Overlay -->
                    <div v-if="publicStore.table.loading" class="grid-loading">
                        <div class="spinner-border text-primary" role="status"></div>
                    </div>

                    <!-- Content Area -->
                    <div v-if="publicStore.table.data.length === 0 && !publicStore.table.loading" class="row">
                        <!-- Empty State -->
                        <div class="col-12 py-20 text-center animate__animated animate__fadeIn">
                            <div class="mb-10 fs-1 opacity-25">📁</div>
                            <h3 class="text-white fw-bold mb-3">Konten tidak ditemukan</h3>
                            <p class="text-gray-500 mb-8">Kami tidak dapat menemukan hasil yang sesuai dengan kriteria pencarian Anda.</p>
                            <button class="btn btn-outline-primary px-8" @click="resetFilters">Tampilkan Semua Konten</button>
                        </div>
                    </div>

                    <div class="row g-1" v-else>
                        <!-- Dense Content Grid -->
                        <div class="col-6 col-md-4 col-lg-3 col-xl-2 animate__animated animate__fadeIn" 
                            v-for="(item, index) in publicStore.table.data" :key="item.id"
                            :style="{ animationDelay: (index * 0.02) + 's' }">
                            <div class="photo-item position-relative overflow-hidden group cursor-pointer" @click="openLink(item.googleDriveUrl)">
                                <!-- Real Image -->
                                <div class="photo-wrapper aspect-ratio-square">
                                    <img v-if="item.type === 'image' && item.googleDriveUrl" 
                                        :src="item.googleDriveUrl" 
                                        class="w-100 h-100 object-fit-cover photo-img transition-transform duration-500" 
                                        :alt="item.title"
                                        loading="lazy">
                                    
                                    <div v-else class="video-placeholder d-flex align-items-center justify-content-center h-100 bg-dark-deep">
                                        <i class="fas fa-play fs-1 text-white opacity-25"></i>
                                    </div>
                                </div>

                                <!-- Hover Overlay (Google Photos Style) -->
                                <div class="photo-overlay position-absolute inset-0 opacity-0 group-hover-opacity-100 d-flex flex-column justify-content-end p-4 transition-opacity">
                                    <div class="photo-info overflow-hidden">
                                        <div class="text-white fw-bold fs-8 text-truncate mb-1">{{ item.title }}</div>
                                        <div class="d-flex align-items-center gap-1 overflow-hidden">
                                            <span v-for="tag in item.tags?.slice(0, 2)" :key="tag.id" class="text-primary-light fs-10 fw-medium">
                                                #{{ tag.name }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Badge -->
                                <div class="photo-badge position-absolute top-2 right-2">
                                    <span v-if="item.type === 'video'" class="badge bg-blur-dark rounded-circle p-2">
                                        <i class="fas fa-video fs-9 text-white"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pagination -->
                <div class="mt-20 py-10 border-top border-white border-opacity-5 d-flex flex-column flex-md-row justify-content-between align-items-center gap-6" v-if="publicStore.table.totalData > 0">
                    <div class="text-gray-500 fs-6 fw-medium">
                        Menampilkan <span class="text-white">{{ publicStore.table.data.length }}</span> dari <span class="text-white">{{ publicStore.table.totalData }}</span> konten
                    </div>
                    <nav aria-label="Gallery pagination">
                        <ul class="pagination pagination-modern mb-0">
                            <li class="page-item" :class="{ disabled: publicStore.table.currentPage === 1 }">
                                <a class="page-link" href="#" @click.prevent="changePage(publicStore.table.currentPage - 1)">
                                    <i class="fas fa-chevron-left"></i>
                                </a>
                            </li>
                            <li class="page-item active">
                                <span class="page-link">{{ publicStore.table.currentPage }}</span>
                            </li>
                            <li class="page-item" :class="{ disabled: publicStore.table.data.length < publicStore.table.showPerPage }">
                                <a class="page-link" href="#" @click.prevent="changePage(publicStore.table.currentPage + 1)">
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                            </li>
                        </ul>
                    </nav>
                </div>

            </div>
        </main>

        <!-- Footer -->
        <footer class="py-10 bg-darker border-top border-white border-opacity-5">
            <div class="container-xxl text-center">
                <p class="text-gray-600 mb-0 fs-7">© 2024 Fan Content Hub. Seluruh momen diabadikan atas dasar kecintaan pada seni.</p>
            </div>
        </footer>
    </div>
</template>

<script setup lang="ts">
import { onMounted } from 'vue';
import { useRoute } from 'vue-router';
import { usePublicContent } from '@stores/fch-public';

const publicStore = usePublicContent();
const route = useRoute();

onMounted(() => {
    publicStore.resetFilters();
    
    // Check for event filter from landing page
    if (route.query.event) {
        publicStore.filters.event = route.query.event as string;
    }
    
    publicStore.getData();
});

function handleSearch() {
    publicStore.setPage(1);
    publicStore.getData();
}

function resetFilters() {
    publicStore.resetFilters();
    publicStore.getData();
}

function changePage(page: number) {
    if (page < 1) return;
    publicStore.setPage(page);
    publicStore.getData();
}

function openLink(url: string | null) {
    if (url) window.open(url, '_blank');
}
</script>

<style scoped>
.bg-dark-page {
    background-color: #08090d;
}

.bg-darker {
    background-color: #050508;
}

.text-primary-gradient {
    background: linear-gradient(135deg, #3699ff 0%, #7239ea 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

.navbar-glass {
    background: rgba(8, 9, 13, 0.8);
    backdrop-filter: blur(12px);
    border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    z-index: 1000;
}

.nav-link-modern {
    color: #a1a5b7;
    font-weight: 600;
    text-decoration: none;
    transition: color 0.3s ease;
}

.nav-link-modern:hover {
    color: #3699ff;
}

.glass-card {
    background: rgba(255, 255, 255, 0.03);
    border: 1px solid rgba(255, 255, 255, 0.05);
    border-radius: 20px;
    backdrop-filter: blur(8px);
}

/* Form Dark Controls */
.form-control-dark, .form-select-dark {
    background-color: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.08);
    color: white;
    border-radius: 12px;
}

.form-control-dark:focus, .form-select-dark:focus {
    background-color: rgba(255, 255, 255, 0.08);
    border-color: #3699ff;
    color: white;
    box-shadow: 0 0 0 0.25rem rgba(54, 153, 255, 0.15);
}

.form-control-dark::placeholder {
    color: #4b4b5e;
}

.form-select-dark option {
    background-color: #12131a;
    color: white;
}

/* Content Cards */
.content-card {
    perspective: 1000px;
}

.card-inner {
    border-radius: 24px;
    transition: transform 0.4s cubic-bezier(0.165, 0.84, 0.44, 1), box-shadow 0.4s ease;
    box-shadow: 0 10px 30px rgba(0,0,0,0.2);
}

.bg-card-dark {
    background-color: #12131a;
}

.content-card:hover .card-inner {
    transform: translateY(-8px);
    box-shadow: 0 20px 40px rgba(54, 153, 255, 0.15);
}

.media-preview {
    height: 220px;
    background: #1a1b26;
    border-radius: 0;
}

.media-placeholder {
    height: 100%;
    width: 100%;
}

.media-icon {
    font-size: 3rem;
    color: rgba(255, 255, 255, 0.05);
    transition: color 0.3s ease;
}

.content-card:hover .media-icon {
    color: rgba(54, 153, 255, 0.2);
}

.media-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(180deg, rgba(54, 153, 255, 0) 50%, rgba(54, 153, 255, 0.05) 100%);
    transition: opacity 0.3s ease;
    opacity: 0;
}

.content-card:hover .media-overlay {
    opacity: 1;
}

.media-badge {
    position: absolute;
    top: 15px;
    right: 15px;
    z-index: 2;
}

.badge-image {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(4px);
    color: #fff;
    font-size: 0.65rem;
    font-weight: 800;
    letter-spacing: 0.5px;
    padding: 6px 12px;
}

.badge-video {
    background: #e42855;
    font-size: 0.65rem;
    font-weight: 800;
    letter-spacing: 0.5px;
    padding: 6px 12px;
}

.badge-tag {
    font-size: 0.75rem;
    color: #3699ff;
    background: rgba(54, 153, 255, 0.1);
    padding: 4px 10px;
    border-radius: 6px;
    font-weight: 600;
    transition: background 0.2s ease;
}

.badge-tag:hover {
    background: rgba(54, 153, 255, 0.2);
}

.hover-primary:hover {
    color: #3699ff !important;
}

.transition {
    transition: all 0.3s ease;
}

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Pagination Modern */
.pagination-modern .page-link {
    background-color: rgba(255, 255, 255, 0.05);
    border: none;
    color: #a1a5b7;
    margin: 0 4px;
    border-radius: 10px !important;
    padding: 10px 18px;
    transition: all 0.2s ease;
}

.pagination-modern .page-item.active .page-link {
    background-color: #3699ff;
    color: white;
}

.pagination-modern .page-link:hover {
    background-color: rgba(255, 255, 255, 0.1);
}

/* Helper Classes */
.grid-loading {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(8, 9, 13, 0.6);
    backdrop-filter: blur(4px);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 10;
    border-radius: 20px;
}

.btn-outline-primary-glass {
    border-color: rgba(54, 153, 255, 0.3);
    color: #3699ff;
}

.btn-outline-primary-glass:hover {
    background: #3699ff;
    color: white;
}

.italic {
    font-style: italic;
}

.photo-item {
    background: #0d0d12;
    transition: all 0.3s ease;
}

.photo-wrapper {
    position: relative;
    width: 100%;
    overflow: hidden;
}

.aspect-ratio-square {
    aspect-ratio: 1 / 1;
}

.photo-img {
    transition: transform 0.6s cubic-bezier(0.165, 0.84, 0.44, 1);
}

.photo-item:hover .photo-img {
    transform: scale(1.1);
}

.photo-overlay {
    background: linear-gradient(to top, rgba(0,0,0,0.8) 0%, rgba(0,0,0,0.2) 50%, transparent 100%);
    z-index: 2;
}

.photo-badge {
    z-index: 3;
}

.bg-blur-dark {
    background: rgba(0, 0, 0, 0.5);
    backdrop-filter: blur(4px);
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.text-primary-light {
    color: #3699ff;
}

.fs-9 { font-size: 0.65rem; }
.fs-10 { font-size: 0.55rem; }

.inset-0 { top: 0; left: 0; right: 0; bottom: 0; }
.top-2 { top: 0.5rem; }
.right-2 { right: 0.5rem; }

.duration-500 { transition-duration: 500ms; }
</style>
