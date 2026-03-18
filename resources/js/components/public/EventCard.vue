<template>
    <div class="event-card-container animate__animated animate__fadeInUp" :style="{ animationDelay: delay }">
        <div class="event-album-card overflow-hidden h-100 position-relative group shadow-lg" @click="navigateToGallery">
            <!-- Album Thumbnail Background -->
            <div class="album-thumbnail-wrapper position-absolute inset-0">
                <img v-if="event.thumbnailUrl" :src="event.thumbnailUrl" class="album-img transition-transform duration-500" :alt="event.name">
                <div v-else class="placeholder-bg d-flex align-items-center justify-content-center">
                    <i class="fas fa-images fs-1 text-white opacity-25"></i>
                </div>
                <div class="album-overlay position-absolute inset-0"></div>
            </div>

            <!-- Content Count Badge -->
            <div class="position-absolute top-4 right-4 z-index-2">
                <span class="badge bg-blur-white text-white rounded-pill px-4 py-2 fs-8 fw-bold">
                    <i class="fas fa-layer-group me-2"></i> {{ event.contentCount || 0 }} Item
                </span>
            </div>

            <!-- Event Details (Bottom Overlay) -->
            <div class="album-details position-absolute bottom-0 left-0 w-100 p-8 z-index-2">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span v-if="event.city" class="text-primary-light fw-bolder text-uppercase fs-8 tracking-widest">{{ event.city }}</span>
                    <span class="text-white opacity-50 fs-8">•</span>
                    <span class="text-white opacity-75 fs-8 fw-medium">{{ eventYear }}</span>
                </div>
                <h3 class="text-white fw-extra-bold mb-4 album-title">
                    {{ event.name }}
                </h3>
                
                <div class="d-flex align-items-center justify-content-between pt-4 border-top border-white border-opacity-10 mt-auto">
                    <div class="d-flex align-items-center gap-2 text-white text-opacity-75 fs-8">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>{{ event.location }}</span>
                    </div>
                    <div class="album-arrow transition-all">
                        <i class="fas fa-arrow-right text-white"></i>
                    </div>
                </div>
            </div>

            <!-- Hover "Open" Indicator -->
            <div class="album-hover-indicator position-absolute inset-0 d-flex align-items-center justify-content-center opacity-0 group-hover-opacity-100 z-index-3">
                <div class="btn btn-primary-glass rounded-circle btn-lg p-6">
                    <i class="fas fa-expand fs-3"></i>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { useRouter } from 'vue-router';
import type { IEvent } from '@/types/fch-event';

const props = defineProps<{
    event: IEvent;
    delay?: string;
}>();

const router = useRouter();

const eventDate = computed(() => {
    return props.event.date ? new Date(props.event.date) : new Date();
});

const eventYear = computed(() => eventDate.value.getFullYear());

function navigateToGallery() {
    router.push({
        path: '/fan-content-hub',
        query: { event: props.event.slug }
    });
}
</script>

<style scoped>
.event-album-card {
    border-radius: 32px;
    cursor: pointer;
    background: #1a1b26;
    height: 380px !important;
    transition: all 0.6s cubic-bezier(0.16, 1, 0.3, 1);
}

.album-thumbnail-wrapper {
    width: 100%;
    height: 100%;
}

.album-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.event-album-card:hover .album-img {
    transform: scale(1.1);
}

.placeholder-bg {
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, #232531 0%, #111218 100%);
}

.album-overlay {
    background: linear-gradient(0deg, rgba(8, 9, 13, 0.95) 0%, rgba(8, 9, 13, 0.4) 50%, rgba(8, 9, 13, 0.1) 100%);
    transition: background 0.4s ease;
}

.event-album-card:hover .album-overlay {
    background: linear-gradient(0deg, rgba(8, 9, 13, 0.98) 0%, rgba(8, 9, 13, 0.6) 50%, rgba(54, 153, 255, 0.2) 100%);
}

.bg-blur-white {
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(8px);
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.album-title {
    font-size: 1.5rem;
    line-height: 1.2;
    letter-spacing: -0.02em;
    text-shadow: 0 4px 12px rgba(0,0,0,0.5);
}

.text-primary-light {
    color: #89c4ff;
}

.album-arrow {
    background: rgba(255, 255, 255, 0.1);
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.event-album-card:hover .album-arrow {
    background: #3699ff;
    transform: rotate(-45deg);
}

.btn-primary-glass {
    background: rgba(54, 153, 255, 0.25);
    backdrop-filter: blur(12px);
    border: 1px solid rgba(54, 153, 255, 0.3);
    color: white;
}

.inset-0 { top: 0; left: 0; right: 0; bottom: 0; }
.z-index-2 { z-index: 2; }
.z-index-3 { z-index: 3; }
.duration-500 { transition-duration: 500ms; }
.tracking-widest { letter-spacing: 0.1em; }
.fw-extra-bold { font-weight: 900; }
.opacity-0 { opacity: 0; }
.group-hover-opacity-100:hover .opacity-0,
.group:hover .group-hover-opacity-100 { opacity: 1; }
</style>
