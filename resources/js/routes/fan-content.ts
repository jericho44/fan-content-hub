import EventIndex from "@src/views/admin/events/Index.vue"
import TagIndex from "@src/views/admin/tags/Index.vue"
import ContentIndex from "@src/views/admin/contents/Index.vue"

import { useEnvironmentStore } from "@/stores/environment"
import { createPinia, setActivePinia } from "pinia"

const pinia = createPinia()
setActivePinia(pinia)

const environmentStore = useEnvironmentStore();

const routes = [
    {
        path: 'fan-content',
        children: [
            {
                path: 'events',
                name: 'a-fch-events',
                component: EventIndex,
                meta: {
                    specificRole: [environmentStore.data.roleSuperAdmin, environmentStore.data.roleAdmin],
                }
            },
            {
                path: 'tags',
                name: 'a-fch-tags',
                component: TagIndex,
                meta: {
                    specificRole: [environmentStore.data.roleSuperAdmin, environmentStore.data.roleAdmin],
                }
            },
            {
                path: 'contents',
                name: 'a-fch-contents',
                component: ContentIndex,
                meta: {
                    specificRole: [environmentStore.data.roleSuperAdmin, environmentStore.data.roleAdmin],
                }
            }
        ]
    }
]

export default routes