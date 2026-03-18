<template>
    <div>
        <div>
            <div id="main-content">
                <div class="post d-flex flex-column-fluid" id="kt_post">
                    <div id="kt_content_container" class="container-xxl">

                        <div class="card card-flush mt-5 mb-5 mb-xl-10" id="kt_profile_details_view">
                            <div class="card card-xl-stretch mb-5 mb-xl-8">
                                <div class="card-header border-0 pt-5 align-items-center">
                                    <h3 class="card-title align-items-start flex-column">
                                        <span class="card-label fw-bolder text-dark mb-2">Manajemen Konten</span>
                                        <span class="text-muted fs-6">Kelola data konten foto dan video</span>
                                    </h3>
                                    <button type="button" class="btn h-50 btn-primary text-white" @click="showModalAdd">
                                        Upload Konten
                                    </button>
                                </div>
                                <div class="card-body pt-5">
                                    <!-- Toolbar -->
                                    <div class="d-flex align-items-center flex-wrap gap-3 mb-6">
                                        <div class="d-flex align-items-center border rounded px-3 py-2 bg-white flex-grow-1" style="max-width:280px;">
                                            <i class="fas fa-search text-muted me-2 fs-7"></i>
                                            <input type="text" class="border-0 fs-7 w-100" style="outline:none;"
                                                placeholder="Cari konten..."
                                                :value="contentStore.table.search"
                                                @input="(e: any) => { contentStore.setSearch(e.target.value); contentStore.setCurrentPage(1); contentStore.getData(); }">
                                        </div>
                                        <select class="form-select form-select-sm w-auto" v-model="filterEventId" @change="applyFilter">
                                            <option value="">Semua Event</option>
                                            <option v-for="evt in eventStore.table.data" :key="evt.id" :value="evt.id">{{ evt.name }}</option>
                                        </select>
                                        <button v-if="filterEventId" class="btn btn-sm btn-light-danger" @click="clearFilter">
                                            <i class="fas fa-times me-1"></i> Reset
                                        </button>
                                        <select class="form-select form-select-sm w-auto ms-auto" :value="contentStore.table.showPerPage"
                                            @change="(e: any) => { contentStore.setShowPerPage(Number(e.target.value)); contentStore.setCurrentPage(1); contentStore.getData(); }">
                                            <option value="12">12 per halaman</option>
                                            <option value="24">24 per halaman</option>
                                            <option value="48">48 per halaman</option>
                                        </select>
                                    </div>

                                    <!-- Loading -->
                                    <div v-if="contentStore.table.loading" class="d-flex justify-content-center py-20">
                                        <div class="spinner-border text-primary" role="status"></div>
                                    </div>

                                    <!-- Empty -->
                                    <div v-else-if="contentStore.table.data.length === 0" class="text-center py-20 text-muted">
                                        <i class="fas fa-images fs-1 text-gray-300 d-block mb-3"></i>
                                        <span class="fs-6">Belum ada konten. Klik <b>Upload Konten</b> untuk menambahkan!</span>
                                    </div>

                                    <!-- Gallery Grid -->
                                    <div v-else class="admin-gallery-grid">
                                        <div v-for="item in contentStore.table.data" :key="item.id" class="admin-gallery-card">
                                            <div class="admin-gallery-thumb">
                                                <!-- Image -->
                                                <img v-if="item.type === 'image' && item.google_drive_url"
                                                    :src="item.google_drive_url"
                                                    class="admin-gallery-img"
                                                    :alt="item.title">
                                                <!-- Video placeholder -->
                                                <div v-else class="admin-gallery-video-ph">
                                                    <i class="fas fa-play-circle"></i>
                                                </div>
                                                <!-- Hover Overlay -->
                                                <div class="admin-gallery-overlay">
                                                    <div class="d-flex gap-2">
                                                        <button @click.stop="edit(item.id)" class="btn btn-sm btn-light" title="Edit">
                                                            <i class="fas fa-edit text-primary"></i>
                                                        </button>
                                                        <button @click.stop="destroyData(item.id)" class="btn btn-sm btn-danger" title="Hapus">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                                <!-- Badges -->
                                                <span class="admin-gallery-type" :class="item.type === 'image' ? 'bg-success' : 'bg-info'">
                                                    <i :class="item.type === 'image' ? 'fas fa-image' : 'fas fa-video'"></i>
                                                </span>
                                                <span class="admin-gallery-status" :class="`badge badge-light-${statusColor(item.status)}`">{{ item.status }}</span>
                                            </div>
                                            <div class="admin-gallery-meta">
                                                <div class="fw-bold fs-7 text-dark text-truncate" :title="item.title">{{ item.title }}</div>
                                                <div class="text-muted fs-8 text-truncate">{{ item.event?.name ?? '-' }}</div>
                                                <div class="d-flex flex-wrap gap-1 mt-1">
                                                    <span v-for="tag in (item.tags || [])" :key="tag.id" class="badge badge-light-primary" style="font-size:10px;">{{ tag.name }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Pagination -->
                                    <div class="d-flex justify-content-between align-items-center mt-6 flex-wrap gap-2">
                                        <span class="text-muted fs-7">
                                            {{ contentStore.table.totalData }} konten ditemukan
                                        </span>
                                        <div class="d-flex gap-1 align-items-center">
                                            <button class="btn btn-sm btn-light-primary px-3" :disabled="contentStore.table.currentPage <= 1"
                                                @click="contentStore.setCurrentPage(contentStore.table.currentPage - 1); contentStore.getData()">
                                                <i class="fas fa-chevron-left"></i>
                                            </button>
                                            <span class="text-muted fs-7 px-2">Halaman {{ contentStore.table.currentPage }}</span>
                                            <button class="btn btn-sm btn-light-primary px-3"
                                                :disabled="contentStore.table.currentPage * contentStore.table.showPerPage >= contentStore.table.totalData"
                                                @click="contentStore.setCurrentPage(contentStore.table.currentPage + 1); contentStore.getData()">
                                                <i class="fas fa-chevron-right"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <CustomModal ref="modalForm" :title="`${flag === 'insert' ? 'Upload Konten Baru' : 'Edit Data Konten'}`"
            :subtitle="`Silahkan lengkapi form berikut untuk ${flag === 'insert' ? 'mengunggah' : 'memperbarui'} data`" size="">
            <ModalBody>
                <div class="mb-5">
                    <label class="form-label fw-bolder fs-6" :class="v$.single.title.$error ? 'text-danger' : ''">Judul Konten</label>
                    <input class="form-control" type="text" placeholder="Contoh: Fancam Opening" autocomplete="off"
                        v-model="single.title">
                    <div v-if="v$.single.title.$error" class="text-danger"> Judul tidak boleh kosong! </div>
                </div>

                <div class="mb-5" v-if="flag === 'insert'">
                    <label class="form-label fw-bolder fs-6" :class="v$.single.type.$error ? 'text-danger' : ''">Tipe</label>
                    <select class="form-select" v-model="single.type" disabled>
                        <option value="">Akan terdeteksi otomatis dari file</option>
                        <option value="image">Gambar (Foto)</option>
                        <option value="video">Video</option>
                    </select>
                    <div class="text-muted fs-7 mt-1">Tipe ditentukan otomatis dari ekstensi file yang dipilih.</div>
                </div>

                <div class="mb-5">
                    <label class="form-label fw-bolder fs-6" :class="v$.single.event_id.$error ? 'text-danger' : ''">Event</label>
                    <select class="form-select" v-model="single.event_id">
                        <option value="" disabled>Pilih Event</option>
                        <option v-for="evt in eventStore.table.data" :key="evt.id" :value="evt.id">{{ evt.name }}</option>
                    </select>
                    <div v-if="v$.single.event_id.$error" class="text-danger"> Event tidak boleh kosong! </div>
                </div>

                <div class="mb-5">
                    <label class="form-label fw-bolder fs-6">Tags</label>
                    <div class="d-flex flex-wrap gap-2">
                        <div v-for="tag in tagStore.table.data" :key="tag.id" class="form-check form-check-custom form-check-solid">
                            <input class="form-check-input" type="checkbox" :value="tag.id" v-model="single.tags" :id="`tag-${tag.id}`">
                            <label class="form-check-label fs-7" :for="`tag-${tag.id}`">{{ tag.name }}</label>
                        </div>
                    </div>
                </div>

                <div class="mb-5" v-if="flag === 'insert'">
                    <label class="form-label fw-bolder fs-6" :class="v$.single.files.$error ? 'text-danger' : ''">File Upload</label>
                    <input ref="fileInput" class="form-control" type="file" @change="handleFileUpload" multiple>
                    <div v-if="v$.single.files.$error" class="text-danger"> File upload tidak boleh kosong! </div>
                    <div class="text-muted fs-7 mt-2">File akan diunggah ke Google Drive di latar belakang.</div>
                </div>

                <div class="mb-5" v-if="flag === 'edit'">
                    <label class="form-label fw-bolder fs-6" :class="v$.single.status.$error ? 'text-danger' : ''">Status Display</label>
                    <select class="form-select" v-model="single.status">
                        <option value="pending">Pending</option>
                        <option value="active">Active (Tampil di Publik)</option>
                        <option value="inactive">Inactive (Sembunyikan)</option>
                    </select>
                    <div v-if="v$.single.status.$error" class="text-danger"> Status tidak boleh kosong! </div>
                </div>

            </ModalBody>
            <ModalFooter>
                <button type="button" class="btn btn-light text-gray-700" data-bs-dismiss="modal">Batal</button>
                <button class="btn btn-primary" type="button" @click="saveData">
                    <span class="indicator-label text-white">Simpan</span>
                </button>
            </ModalFooter>
        </CustomModal>
    </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted, computed } from 'vue';
import { useAuthorizationStore } from "@stores/authorization";
import { useContent } from "@stores/fch-content";
import { useEvent } from "@stores/fch-event";
import { useTag } from "@stores/fch-tag";
import { useVuelidate } from '@vuelidate/core';
import { required } from '@vuelidate/validators';
import Swal from 'sweetalert2';
import { CustomModal, DataTable, ModalBody, ModalFooter } from '@/components/main';
import { axiosHandleError, initializeAppPlugins, loaderHide, loaderShow } from '@/plugins/global';
import type { IContentDetail } from '@/types/fch-content';

const authorizationStore = useAuthorizationStore();
const contentStore = useContent();
const eventStore = useEvent();
const tagStore = useTag();

const modalForm = ref<InstanceType<typeof CustomModal> | null>(null);
const fileInput = ref<HTMLInputElement | null>(null);
const flag = ref<'insert' | 'edit'>('insert');

const filterEventId = computed({
    get: () => contentStore.eventIdFilter,
    set: (val: string) => { contentStore.eventIdFilter = val; }
});

const single = reactive({
    id: '' as string,
    title: '',
    type: '',
    event_id: '',
    files: [] as File[],
    status: 'pending',
    tags: [] as string[]
});

const rules = computed(() => {
    return {
        single: {
            title: { required },
            type: {},
            event_id: { required },
            files: flag.value === 'insert' ? { required } : {},
            status: flag.value === 'edit' ? { required } : {}
        }
    };
});

const v$ = useVuelidate(rules, { single });

onMounted(() => {
    initializeAppPlugins();
    contentStore.resetTable();
    eventStore.resetTable();
    eventStore.setShowPerPage(100); 

    if (authorizationStore.data.authorized) {
        contentStore.getData();
        eventStore.getData();
        tagStore.getData();
    }
});

function openLink(url: string) {
    window.open(url, '_blank');
}

function handleFileUpload(event: Event) {
    const target = event.target as HTMLInputElement;
    if (target.files && target.files.length > 0) {
        single.files = Array.from(target.files);
        // Auto-detect type from the first file's extension
        const firstFile = single.files[0];
        const ext = firstFile.name.split('.').pop()?.toLowerCase() ?? '';
        const videoExtensions = ['mp4', 'mov', 'avi', 'mkv', 'webm', 'flv'];
        single.type = videoExtensions.includes(ext) ? 'video' : 'image';
    } else {
        single.files = [];
        single.type = '';
    }
}

function statusColor(status: string) {
    if (status === 'active') return 'success';
    if (status === 'pending') return 'warning';
    return 'danger';
}

function applyFilter() {
    contentStore.setCurrentPage(1);
    contentStore.getData();
}

function clearFilter() {
    filterEventId.value = '';
    applyFilter();
}

function showModalAdd() {
    reset();
    modalForm.value?.show();
}

async function saveData() {
    await v$.value.$validate();
    if (v$.value.$error) return;
    
    loaderShow()
    try {
        let res;
        if (flag.value === 'insert' && single.files.length > 0) {
            const payload = {
                title: single.title,
                type: single.type,
                event_id: single.event_id,
                files: single.files,
                tags: single.tags
            };
            res = await contentStore.create(payload);
        } else {
            const payload = {
                title: single.title,
                event_id: single.event_id,
                status: single.status,
                tags: single.tags
            };
            res = await contentStore.update(single.id, payload);
        }

        modalForm.value?.hide();
        reset();
        Swal.fire({ icon: 'success', title: 'Berhasil!', text: res?.data?.meta?.message });

        contentStore.setCurrentPage(1);
        contentStore.getData()
    } catch (error) {
        axiosHandleError(error)
    } finally {
        loaderHide()
    }
}

async function edit(id: string) {
    try {
        loaderShow()
        const res = await contentStore.show(id);
        const data = res.data.data as IContentDetail

        reset();
        flag.value = 'edit';
        single.id = data.id;
        single.title = data.title;
        single.status = data.status;
        single.event_id = data.event?.id || '';
        single.tags = (data.tags as any[])?.map(t => t.id) || [];
        modalForm.value?.show();
    } catch (error) {
        axiosHandleError(error)
    } finally {
        loaderHide()
    }
}

async function destroyData(id: string) {
    Swal.fire({
        title: 'Apakah anda yakin?',
        text: "Anda tidak akan dapat mengembalikan ini!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal'
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                loaderShow()
                await contentStore.destroy(id);
                Swal.fire('Terhapus!', 'Data konten telah dihapus.', 'success');
                contentStore.getData()
            } catch (error) {
                axiosHandleError(error)
            } finally {
                loaderHide()
            }
        }
    })
}

function reset() {
    v$.value.$reset();
    flag.value = 'insert';
    single.id = '';
    single.title = '';
    single.type = '';
    single.event_id = '';
    single.files = [];
    single.status = 'pending';
    single.tags = [];
    // Clear file input
    if (fileInput.value) {
        fileInput.value.value = '';
    }
}
</script>

<style scoped>
.admin-gallery-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
    gap: 16px;
}

.admin-gallery-card {
    border-radius: 10px;
    overflow: hidden;
    background: #fff;
    box-shadow: 0 2px 8px rgba(0,0,0,0.07);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.admin-gallery-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 20px rgba(0,0,0,0.13);
}

.admin-gallery-thumb {
    position: relative;
    width: 100%;
    padding-top: 75%; /* 4:3 aspect ratio */
    background: #1a1a2e;
    overflow: hidden;
}

.admin-gallery-img {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}
.admin-gallery-card:hover .admin-gallery-img {
    transform: scale(1.05);
}

.admin-gallery-video-ph {
    position: absolute;
    inset: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #1a1a2e, #16213e);
    font-size: 2.5rem;
    color: rgba(255,255,255,0.7);
}

.admin-gallery-overlay {
    position: absolute;
    inset: 0;
    background: rgba(0,0,0,0.55);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.25s ease;
}
.admin-gallery-card:hover .admin-gallery-overlay {
    opacity: 1;
}

.admin-gallery-type {
    position: absolute;
    top: 8px;
    left: 8px;
    width: 26px;
    height: 26px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-size: 11px;
}

.admin-gallery-status {
    position: absolute;
    bottom: 8px;
    right: 8px;
    font-size: 10px;
    border-radius: 20px;
    padding: 2px 8px;
}

.admin-gallery-meta {
    padding: 10px 12px 12px;
}

@media (max-width: 576px) {
    .admin-gallery-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}
</style>
