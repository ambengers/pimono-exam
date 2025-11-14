import routes from './routes.ts'
import NProgress from 'nprogress'
import {createRouter, createWebHistory} from 'vue-router'
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

async function beforeEach(to, from, next) {
    NProgress.start()

    const auth = useAuthStore()

    if (!auth.isLoggedIn && to.meta.requiresAuth === true) {
        await auth.loadAuth()
    }

    next()
}

async function afterEach(to, from, next) {
    NProgress.done()
}

export default router