import Login from "@src/views/Login.vue"
import Landing from "@src/views/Landing.vue"

const routes = [
    {
        path: '/',
        name: 'landing',
        component: Landing,
        meta: {
            auth: false,
            guest: false
        }
    },
    {
        path: '/login',
        name: 'login',
        component: Login,
        meta: {
            auth: false,
            guest: true
        }
    }
]

export default routes
