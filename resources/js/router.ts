import routes from './routes.js'
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

    if (!auth.isLoggedIn && to.meta.requiresAuth === true) {
        await auth.loadAuth()
    }

    next()
}

function afterEach(_to: RouteLocationNormalized, _from: RouteLocationNormalized) {
    NProgress.done()
}

export default router