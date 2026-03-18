import { defineStore } from 'pinia'
import { reactive } from 'vue'
import api from '@services/api'
import type { IDataTableContent, IContent } from '@/types/fch-content'

const prefix = '/api'
const resource = '/public/fan-content'

export const usePublicContent = defineStore('fchPublicContent', () => {
    // State
    const table = reactive<IDataTableContent>({
        column: [],
        data: [],
        showPerPage: 12,
        search: '',
        order: 'desc',
        sortBy: 'created_at',
        totalData: 0,
        currentPage: 1,
        loading: false,
        showSearch: true,
    })

    const filters = reactive({
        year: '',
        idol: '',
        event: ''
    })

    // Actions
    function setPage(page: number) { table.currentPage = page }
    function setLoading(loading: boolean) { table.loading = loading }
    function setSearch(value: string) { table.search = value }
    function setData(data: IContent[]) { table.data = data }
    function setFilters(year: string, idol: string, event: string) {
        filters.year = year
        filters.idol = idol
        filters.event = event
    }

    function resetFilters() {
        filters.year = ''
        filters.idol = ''
        filters.event = ''
        table.search = ''
        table.currentPage = 1
    }

    async function getData() {
        setLoading(true)
        try {
            const params: any = {
                page: table.currentPage,
                limit: table.showPerPage,
                search: table.search
            }

            if (filters.year) params.year = filters.year
            if (filters.idol) params.idol = filters.idol
            if (filters.event) params.event = filters.event

            // Use the unauthenticated instance of axios or public endpoint 
            // In typical Laravel setup as configured, api() handles base interceptors. 
            // The route is unprotected in api.php
            const res = await api().get(`${prefix}${resource}`, { params })
            setData(res.data.data.data)
            table.totalData = res.data.data.total
            table.currentPage = res.data.data.current_page
            return res
        } catch (err) {
            setData([])
            table.totalData = 0
            return err
        } finally {
            setLoading(false)
        }
    }

    return {
        table,
        filters,
        setPage, setLoading, setSearch, setData, setFilters,
        resetFilters, getData
    }
})