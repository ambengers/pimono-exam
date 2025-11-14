import axios from 'axios'
import router from './router'

window.axios = axios
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'

axios.interceptors.response.use(
    (response) => response,
    (error) => {
        const { status } = error.response

        switch (status) {
            // In this switch statement, we handle the HTTP errors that need to be redirected to
            // a different page in the site. This should not intercept the error that needs to 
            // be displayed on the current page the user is on, e.g: 422 (validation errors)
            case 401:
            case 419:
                window.location = '/login'
            break
        }

        return Promise.reject(error)
    }
)