<template>
    <div class="event-card-container animate__animated animate__fadeInUp" :style="{ animationDelay: delay }">
        <div class="event-card bg-glass-dark border-0 overflow-hidden h-100 position-relative group" @click="navigateToGallery">
            <!-- Event Image Overlay/Placeholder -->
            <div class="event-visual position-relative overflow-hidden">
                <div class="visual-overlay"></div>
                <!-- Simple placeholder icon -->
                <div class="visual-placeholder d-flex align-items-center justify-content-center">
                    <span class="placeholder-icon">
                        <i class="fas fa-calendar-alt"></i>
                    </span>
                </div>
                <!-- Date Badge -->
                <div class="date-badge">
                    <span class="day">{{ eventDay }}</span>
                    <span class="month">{{ eventMonth }}</span>
                </div>
            </div>

            <!-- Event Content -->
            <div class="event-details p-8">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <span class="badge badge-glow-primary">Acara Seru</span>
                    <span class="text-gray-600 fs-8 fw-bold text-uppercase">{{ eventYear }}</span>
                </div>
                <h3 class="text-white fw-bolder mb-3 group-hover-text-primary transition">
                    {{ event.name }}
                </h3>
                <div class="d-flex align-items-center gap-3 text-gray-500 mb-6">
                    <i class="fas fa-map-marker-alt fs-7"></i>
                    <span class="fs-7 fw-medium">{{ event.location || 'Lokasi Belum Ditentukan' }}</span>
                </div>
                
                <div class="mt-auto pt-6 border-top border-white border-opacity-5 d-flex justify-content-between align-items-center">
                    <span class="text-primary-gradient fw-bold fs-7">Lihat Galeri</span>
                    <div class="btn-circle-arrow">
                        <i class="fas fa-chevron-right"></i>
                    </div>
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

const eventDay = computed(() => eventDate.value.getDate());
const eventMonth = computed(() => {
    const months = ['JAN', 'FEB', 'MAR', 'APR', 'MEI', 'JUN', 'JUL', 'AGU', 'SEP', 'OKT', 'NOV', 'DES'];
    return months[eventDate.value.getMonth()];
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
.bg-glass-dark {
    background: rgba(255, 255, 255, 0.03);
    backdrop-filter: blur(12px);
    border: 1px solid rgba(255, 255, 255, 0.05);
    border-radius: 28px;
}

.event-card {
    cursor: pointer;
    transition: all 0.5s cubic-bezier(0.165, 0.84, 0.44, 1);
}

.event-card:hover {
    transform: translateY(-12px);
    background: rgba(255, 255, 255, 0.06);
    box-shadow: 0 25px 50px -12px rgba(54, 153, 255, 0.25);
    border-color: rgba(54, 153, 255, 0.3);
}

.event-visual {
    height: 180px;
    background: #1a1b26;
}

.visual-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(0deg, rgba(8, 9, 13, 0.8) 0%, transparent 100%);
    z-index: 1;
}

.visual-placeholder {
    height: 100%;
}

.placeholder-icon {
    font-size: 3.5rem;
    color: rgba(255, 255, 255, 0.05);
    transition: all 0.5s ease;
}

.event-card:hover .placeholder-icon {
    color: rgba(54, 153, 255, 0.15);
    transform: scale(1.1) rotate(-5deg);
}

.date-badge {
    position: absolute;
    top: 20px;
    left: 20px;
    z-index: 2;
    background: #ffffff;
    width: 55px;
    height: 65px;
    border-radius: 12px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    box-shadow: 0 10px 20px rgba(0,0,0,0.3);
}

.date-badge .day {
    font-size: 1.5rem;
    font-weight: 800;
    color: #0d0d12;
    line-height: 1;
}

.date-badge .month {
    font-size: 0.7rem;
    font-weight: 700;
    color: #3699ff;
    letter-spacing: 1px;
}

.badge-glow-primary {
    background: rgba(54, 153, 255, 0.1);
    color: #3699ff;
    border: 1px solid rgba(54, 153, 255, 0.2);
    font-size: 0.65rem;
    font-weight: 800;
    letter-spacing: 0.5px;
    padding: 4px 10px;
}

.group-hover-text-primary {
    transition: color 0.3s ease;
}

.event-card:hover .group-hover-text-primary {
    color: #3699ff !important;
}

.btn-circle-arrow {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: rgba(54, 153, 255, 0.1);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #3699ff;
    transition: all 0.3s ease;
}

.event-card:hover .btn-circle-arrow {
    background: #3699ff;
    color: white;
    transform: translateX(4px);
}

.text-primary-gradient {
    background: linear-gradient(135deg, #3699ff 0%, #7239ea 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

.transition {
    transition: all 0.3s ease;
}
</style>
