import './axios'
import router from './router'
import {createApp} from 'vue/dist/vue.esm-bundler'
import {createPinia} from 'pinia'
import '../css/app.css'

const app = createApp({})
const pinia = createPinia()

app.use(router)
app.use(pinia)

app.mount('#app')