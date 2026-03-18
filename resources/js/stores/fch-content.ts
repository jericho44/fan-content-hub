import { defineStore } from 'pinia'
import { reactive, ref } from 'vue'
import api from '@services/api'
import type { IDataTableContent, ICreateContentPayload, IEditContentPayload, IContent } from '@/types/fch-content'
import { axiosHandleError } from '@/plugins/global'

const prefix = '/api-web'
const resource = '/contents'

export const useContent = defineStore('fchContent', () => {
    // State
    const table = reactive<IDataTableContent>({
        column: [
            { text: 'NO', sortBy: 'id', sortColumn: true },
            { text: 'Thumbnail', sortBy: '', sortColumn: false },
            { text: 'Judul', sortBy: 'title', sortColumn: true, class: 'w-200px' },
            { text: 'Tipe', sortBy: 'type', sortColumn: true },
            { text: 'Event', sortBy: 'event_id', sortColumn: false },
            { text: 'Tags', sortBy: '', sortColumn: false },
            { text: 'Status', sortBy: 'status', sortColumn: true },
            { text: 'Aksi', sortBy: '', sortColumn: false, class: 'text-center' }
        ],
        data: [],
        showPerPage: 10,
        search: '',
        order: 'desc',
        sortBy: 'created_at',
        totalData: 0,
        currentPage: 1,
        loading: false,
        showSearch: true,
    })

    // Actions
    function setShowPerPage(perPage: number) { table.showPerPage = perPage }
    function setCurrentPage(page: number) { table.currentPage = page }
    function setLoading(loading: boolean) { table.loading = loading }
    function setSearch(value: string) { table.search = value }
    function setData(data: IContent[]) { table.data = data }
    function setOrder(order: string) { table.order = order }
    function setSortBy(sort: string) { table.sortBy = sort }
    function setTotalData(total: number) { table.totalData = total }

    const eventIdFilter = ref('');

    function resetTable() {
        setCurrentPage(1)
        setShowPerPage(10)
        setSearch('')
        setTotalData(0)
        setOrder('desc')
        setSortBy('created_at')
    }

    async function getData() {
        setLoading(true)
        try {
            const params: Record<string, any> = {
                page: table.currentPage,
                limit: table.showPerPage,
                orderBy: table.order,
                sortBy: table.sortBy,
                search: table.search
            }
            if (eventIdFilter.value) {
                params['event_id'] = eventIdFilter.value;
            }
            const res = await api().get(`${prefix}${resource}`, { params })
            setData(res.data.data.data)
            setTotalData(res.data.data.total)
            return res
        } catch (err) {
            setData([])
            setTotalData(0)
            axiosHandleError(err)
            return err
        } finally {
            setLoading(false)
        }
    }

    async function create(payload: ICreateContentPayload) {
        const formData = new FormData()
        formData.append('title', payload.title)
        formData.append('type', payload.type)
        formData.append('event_id', payload.event_id)
        if (payload.files && payload.files.length > 0) {
            payload.files.forEach((file, index) => {
                formData.append(`files[${index}]`, file)
            })
        }
        
        if (payload.tags && payload.tags.length > 0) {
            payload.tags.forEach((tag, index) => {
                formData.append(`tags[${index}]`, tag)
            })
        }

        return api().post(`${prefix}${resource}`, formData, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        })
    }

    async function show(id: string) {
        return api().get(`${prefix}${resource}/${id}`)
    }

    async function update(id: string, payload: IEditContentPayload) {
        return api().put(`${prefix}${resource}/${id}`, payload)
    }

    async function destroy(id: string) {
        return api().delete(`${prefix}${resource}/${id}`)
    }

    return {
        table, eventIdFilter,
        setShowPerPage, setCurrentPage, setLoading, setSearch, setData, setOrder, setSortBy, setTotalData,
        resetTable, getData, create, show, update, destroy
    }
})