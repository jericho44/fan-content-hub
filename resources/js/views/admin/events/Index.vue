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
                                        <span class="card-label fw-bolder text-dark mb-2">Manajemen Events</span>
                                        <span class="text-muted fs-6">Kelola data event untuk Fan Content Hub</span>
                                    </h3>
                                    <button type="button" class="btn h-50 btn-primary text-white" @click="showModalAdd">
                                        Tambah Data
                                    </button>
                                </div>
                                <div class="card-body pt-5">
                                    <DataTable :config="eventStore.table" @get-data="eventStore.getData"
                                        @set-order="(order: string) => eventStore.setOrder(order)"
                                        @set-page="(page: number) => eventStore.setCurrentPage(page)"
                                        @set-search="(search: string) => eventStore.setSearch(search)"
                                        @set-show-per-page="(showPerPage: number) => eventStore.setShowPerPage(showPerPage)"
                                        @set-sort-by="(sortBy: string) => eventStore.setSortBy(sortBy)"
                                        :is-from-store="true">
                                        <template v-slot:body>
                                            <tr v-for="(context, index) in eventStore.table.data" :key="context.id">
                                                <td class="text-center">
                                                    {{ index + ((Number(eventStore.table.showPerPage) *
                                                        (Number(eventStore.table.currentPage) - 1))) + 1 }}
                                                </td>
                                                <td class="text-left">{{ context.name }}</td>
                                                <td class="text-left">{{ context.city ?? '-' }}</td>
                                                <td class="text-left">{{ context.date }}</td>
                                                <td class="text-left">{{ context.location }}</td>
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

        <CustomModal ref="modalForm" :title="`${flag === 'insert' ? 'Tambah Event' : 'Edit Event'}`"
            :subtitle="`Silahkan lengkapi form berikut untuk ${flag === 'insert' ? 'menambah' : 'memperbarui'} data`" size="">
            <ModalBody>
                <div class="mb-5">
                    <label class="form-label fw-bolder fs-6" :class="v$.single.name.$error ? 'text-danger' : ''">Nama Event</label>
                    <input class="form-control" type="text" placeholder="Contoh: Summer Concert 2024" autocomplete="off"
                        v-model="single.name">
                    <div v-if="v$.single.name.$error" class="text-danger"> Nama Event tidak boleh kosong! </div>
                </div>

                <div class="mb-5">
                    <label class="form-label fw-bolder fs-6" :class="v$.single.date.$error ? 'text-danger' : ''">Tanggal</label>
                    <input class="form-control" type="date" autocomplete="off"
                        v-model="single.date">
                    <div v-if="v$.single.date.$error" class="text-danger"> Tanggal tidak boleh kosong! </div>
                </div>

                <div class="mb-5">
                    <label class="form-label fw-bolder fs-6" :class="v$.single.location.$error ? 'text-danger' : ''">Lokasi (Venue)</label>
                    <input class="form-control" type="text" placeholder="Contoh: Senayan JSE" autocomplete="off"
                        v-model="single.location">
                    <div v-if="v$.single.location.$error" class="text-danger"> Lokasi tidak boleh kosong! </div>
                </div>

                <div class="mb-5">
                    <label class="form-label fw-bolder fs-6">Kota</label>
                    <input class="form-control" type="text" placeholder="Contoh: Jakarta" autocomplete="off"
                        v-model="single.city">
                </div>

                <div class="mb-5">
                    <label class="form-label fw-bolder fs-6">External Gallery URL (Optional)</label>
                    <input class="form-control" type="text" placeholder="https://photos.google.com/..." autocomplete="off"
                        v-model="single.external_gallery_url">
                    <div class="text-muted fs-7 mt-1">Jika diisi, klik di landing page akan membuka link ini.</div>
                </div>

                <div class="mb-5">
                    <label class="form-label fw-bolder fs-6" :class="v$.single.description.$error ? 'text-danger' : ''">Deskripsi</label>
                    <textarea class="form-control" placeholder="Deskripsi Event" rows="3"
                        v-model="single.description"></textarea>
                    <div v-if="v$.single.description.$error" class="text-danger"> Deskripsi tidak boleh kosong! </div>
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
import { useEvent } from "@stores/fch-event";
import { useVuelidate } from '@vuelidate/core';
import { required } from '@vuelidate/validators';
import Swal from 'sweetalert2';
import { CustomModal, DataTable, ModalBody, ModalFooter } from '@/components/main';
import { axiosHandleError, initializeAppPlugins, loaderHide, loaderShow } from '@/plugins/global';
import type { IEventDetail } from '@/types/fch-event';

const authorizationStore = useAuthorizationStore();
const eventStore = useEvent();
const modalForm = ref<InstanceType<typeof CustomModal> | null>(null);
const flag = ref<'insert' | 'edit'>('insert');

const single = reactive({
    id: '' as string,
    name: '',
    date: '',
    location: '',
    city: '',
    description: '',
    external_gallery_url: ''
});

const rules = computed(() => ({
    single: {
        name: { required },
        date: { required },
        location: { required },
        description: { required }
    }
}));

const v$ = useVuelidate(rules, { single });

onMounted(() => {
    initializeAppPlugins();
    eventStore.resetTable();
    
    // Safety check for refresh
    if (authorizationStore.data.authorized) {
        eventStore.getData();
    }
});

function showModalAdd() {
    reset();
    modalForm.value?.show();
}

async function saveData() {
    await v$.value.$validate();
    if (v$.value.$error) return;
    
    loaderShow()
    try {
        const payload = {
            name: single.name,
            date: single.date,
            location: single.location,
            city: single.city,
            description: single.description,
            external_gallery_url: single.external_gallery_url
        };

        const res = flag.value === 'insert' && !single.id
                ? await eventStore.create(payload)
                : await eventStore.update(single.id, payload);

        $('#modal-form').modal('hide');
        Swal.fire({ icon: 'success', title: 'Berhasil!', text: res?.data?.meta?.message });

        eventStore.setCurrentPage(1);
        eventStore.getData()
    } catch (error) {
        axiosHandleError(error)
    } finally {
        loaderHide()
    }
}

async function edit(id: string) {
    try {
        loaderShow()
        const res = await eventStore.show(id);
        const data = res.data.data as IEventDetail

        reset();
        flag.value = 'edit';
        single.id = data.id;
        single.name = data.name;
        single.date = data.date;
        single.location = data.location;
        single.city = data.city || '';
        single.description = data.description;
        single.external_gallery_url = data.externalGalleryUrl || '';
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
                await eventStore.destroy(id);
                Swal.fire('Terhapus!', 'Data event telah dihapus.', 'success');
                eventStore.getData()
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
    single.name = '';
    single.date = '';
    single.location = '';
    single.city = '';
    single.description = '';
    single.external_gallery_url = '';
}
</script>
