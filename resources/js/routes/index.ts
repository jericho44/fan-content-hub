
import { createRouter, createWebHistory } from 'vue-router'
import Admin from "@/views/admin/Index.vue";

import Dashboard from "@src/views/admin/dashboard/Index.vue"

// errors
import NotFound from "@/views/errors/404.vue";
import ForbiddenAccess from "@/views/errors/403.vue";


import masterRoutes from "@routes/master";
import authRoutes from "@routes/authorization";
import fanContentRoutes from "@routes/fan-content";
import PublicFanContentHub from "@src/views/public/fan-content/Index.vue";

import { useAuthorizationStore } from '@src/stores/authorization';

const routes = [
    ...authRoutes,
    {
        path: '/fan-content-hub',
        name: 'public-fan-content-hub',
        component: PublicFanContentHub,
        meta: {
            auth: false,
            guest: false // So it can be accessed whether logged in or out
        }
    },
    {
        path: '/admin',
        component: Admin,
        meta: {
            auth: true,
        },
        children: [
            {
                path: '',
                name: 'a-index',
                component: Dashboard
            },
            {
                path: 'dashboard',
                name: 'a-dashboard',
                component: Dashboard
            },
            ...masterRoutes,
            ...fanContentRoutes,
        ]
    },
    {
        path: "/404",
        component: NotFound,
        name: "not-found",
    },
    {
        path: "/403",
        component: ForbiddenAccess,
        name: "forbidden",
    },
    {
        path: "/:pathMatch(.*)*",
        redirect: "/404",
    },

]

const router = createRouter({
    history: createWebHistory(import.meta.env.VITE_SUBDIRECTORY),
    routes,
    scrollBehavior() {
        return {
            top: 0,
        };
    },
});

router.beforeEach((to, _from, next) => {
    const authorizationStore = useAuthorizationStore();
    const token = localStorage.getItem("lp_token");

    if (to.matched.some((record) => record.meta.auth)) {
        if (!token) {
            return next({ name: "login" });
        }

        const checkRoleAndProceed = () => {
            const matchedRoles = to.matched.flatMap(record => record.meta.specificRole || []) as string[];
            if (matchedRoles.length > 0) {
                const userRoleSlug = authorizationStore.data.role?.slug;
                if (!matchedRoles.includes(userRoleSlug || '')) {
                    console.warn(`Access denied! Role: ${userRoleSlug}. Required: ${matchedRoles.join(', ')}`);
                    return next("/403");
                }
            }
            return next();
        };

        // If not initialized, fetch profile first
        if (authorizationStore.data.authorized === false) {
            authorizationStore.getProfile(true)
                .then(() => checkRoleAndProceed())
                .catch(() => {
                    localStorage.clear();
                    return next({ name: "login" });
                });
            return;
        }

        // Even if marked authorized, double check if role is populated (safeguard for refresh)
        if (!authorizationStore.data.role) {
            authorizationStore.getProfile(true)
                .then(() => checkRoleAndProceed())
                .catch(() => {
                    localStorage.clear();
                    return next({ name: "login" });
                });
            return;
        }

        return checkRoleAndProceed();
    } else if (to.matched.some((record) => record.meta.guest)) {
        // Cek jika route untuk guest (tanpa login)
        if (!token) {
            return next(); // Lanjutkan jika belum login
        } else {
            return next({ name: "a-dashboard" }); // Jika sudah login, redirect ke halaman dashboard
        }
    } else {
        // Jika rute tidak membutuhkan autentikasi, lanjutkan
        return next();
    }
})


export default router
