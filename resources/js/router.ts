import routes from './routes.ts'
// @ts-ignore - nprogress doesn't have types
import NProgress from 'nprogress'
import {createRouter, createWebHistory, type NavigationGuardNext, type RouteLocationNormalized} from 'vue-router'
import {useAuthStore} from "@stores/auth";

const router = createRouter({
    history: createWebHistory(),
    linkExactActiveClass: 'active',
    routes: routes,
})

NProgress.configure({
    easing: 'ease',
    speed: 500,
    showSpinner: false,
})

router.beforeEach(beforeEach)
router.afterEach(afterEach)

async function beforeEach(to: RouteLocationNormalized, _from: RouteLocationNormalized, next: NavigationGuardNext) {
    NProgress.start()

    const auth = useAuthStore()

    // Handle root path redirect
    if (to.path === '/') {
        if (auth.isLoggedIn) {
            next({ name: 'dashboard' })
        } else {
            next({ name: 'login' })
        }
        return
    }

    // Check if route requires authentication
    if (to.meta.requiresAuth === true) {
        if (!auth.isLoggedIn) {
            await auth.loadAuth()
            if (!auth.isLoggedIn) {
                next({ name: 'login', query: { redirect: to.fullPath } })
                return
            }
        }
    }

    // Check if route is guest-only (login/register)
    if (to.meta.guestOnly === true && auth.isLoggedIn) {
        next({ name: 'dashboard' })
        return
    }

    next()
}

function afterEach() {
    NProgress.done()
}

export default router