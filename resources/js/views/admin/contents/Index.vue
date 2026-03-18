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
                                    <DataTable :config="contentStore.table" @get-data="contentStore.getData"
                                        @set-order="(order: string) => contentStore.setOrder(order)"
                                        @set-page="(page: number) => contentStore.setCurrentPage(page)"
                                        @set-search="(search: string) => contentStore.setSearch(search)"
                                        @set-show-per-page="(showPerPage: number) => contentStore.setShowPerPage(showPerPage)"
                                        @set-sort-by="(sortBy: string) => contentStore.setSortBy(sortBy)"
                                        :is-from-store="true">
                                        <template v-slot:body>
                                            <tr v-for="(context, index) in contentStore.table.data" :key="context.id">
                                                <td class="text-center">
                                                    {{ index + ((Number(contentStore.table.showPerPage) *
                                                        (Number(contentStore.table.currentPage) - 1))) + 1 }}
                                                </td>
                                                <td class="text-center">
                                                    <a v-if="context.google_drive_url" :href="context.google_drive_url" target="_blank" class="btn btn-sm btn-light">Lihat {{ context.type }}</a>
                                                    <span v-else class="text-muted">Proses...</span>
                                                </td>
                                                <td class="text-left">{{ context.title }}</td>
                                                <td class="text-left">{{ context.event?.name ?? '-' }}</td>
                                                <td class="text-center">
                                                    <span :class="`badge badge-light-${statusColor(context.status)}`">{{ context.status }}</span>
                                                </td>
                                                <td class="text-center">
                                                    <button @click="edit(context?.id)" class="btn btn-secondary btn-xs me-2"
                                                        type="button"
                                                        style="padding:5px 10px !important;color: #3E97FF;"
                                                        aria-expanded="false">
                                                        <svg width="14" height="14" viewBox="0 0 14 14" fill="none"
                                                            xmlns="http://www.w3.org/2000/svg">
                                                            <path
                                                                d="M11.9607 6.0805L6.38415 11.9762C6.21888 12.1509 5.98897 12.2499 5.74846 12.2499L3.20857 12.2499C2.72532 12.2499 2.33357 11.8582 2.33357 11.3749L2.33357 8.81264C2.33357 8.58536 2.422 8.367 2.58015 8.20378L8.21459 2.38837C8.55085 2.04131 9.1048 2.03255 9.45187 2.36882C9.45518 2.37202 9.45847 2.37526 9.46172 2.37852L11.9437 4.86051C12.2787 5.19552 12.2862 5.73631 11.9607 6.0805Z"
                                                                fill="currentColor" />
                                                        </svg>
                                                        &ensp; Edit
                                                    </button>
                                                    
                                                    <button @click="destroyData(context?.id)" class="btn btn-danger btn-xs"
                                                        type="button"
                                                        style="padding:5px 10px !important;"
                                                        aria-expanded="false">
                                                        Hapus
                                                    </button>
                                                </td>
                                            </tr>
                                        </template>
                                    </DataTable>
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
                    <select class="form-select" v-model="single.type">
                        <option value="" disabled>Pilih Tipe</option>
                        <option value="image">Gambar (Foto)</option>
                        <option value="video">Video</option>
                    </select>
                    <div v-if="v$.single.type.$error" class="text-danger"> Tipe tidak boleh kosong! </div>
                </div>

                <div class="mb-5" v-if="flag === 'insert'">
                    <label class="form-label fw-bolder fs-6" :class="v$.single.event_id.$error ? 'text-danger' : ''">Event</label>
                    <select class="form-select" v-model="single.event_id">
                        <option value="" disabled>Pilih Event</option>
                        <option v-for="evt in eventStore.table.data" :key="evt.id" :value="evt.id">{{ evt.name }}</option>
                    </select>
                    <div v-if="v$.single.event_id.$error" class="text-danger"> Event tidak boleh kosong! </div>
                </div>

                <div class="mb-5" v-if="flag === 'insert'">
                    <label class="form-label fw-bolder fs-6" :class="v$.single.files.$error ? 'text-danger' : ''">File Upload</label>
                    <input class="form-control" type="file" @change="handleFileUpload" multiple>
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
const flag = ref<'insert' | 'edit'>('insert');

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
            type: flag.value === 'insert' ? { required } : {},
            event_id: flag.value === 'insert' ? { required } : {},
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
    }
});

function handleFileUpload(event: Event) {
    const target = event.target as HTMLInputElement;
    if (target.files && target.files.length > 0) {
        single.files = Array.from(target.files);
    } else {
        single.files = [];
    }
}

function statusColor(status: string) {
    if (status === 'active') return 'success';
    if (status === 'pending') return 'warning';
    return 'danger';
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
                status: single.status,
                tags: single.tags
            };
            res = await contentStore.update(single.id, payload);
        }

        $('#modal-form').modal('hide');
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
}
</script>
