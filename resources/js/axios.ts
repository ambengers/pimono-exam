import axios, { type InternalAxiosRequestConfig, type AxiosResponse, type AxiosError } from 'axios'

// Extend Window interface for axios
declare global {
    interface Window {
        axios: typeof axios;
    }
}

window.axios = axios
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'
window.axios.defaults.withCredentials = true // Important for SPA with session-based auth

// Function to refresh CSRF token
function refreshCsrfToken() {
    const metaToken = document.head.querySelector('meta[name="csrf-token"]')
    if (metaToken) {
        const token = metaToken.getAttribute('content')
        if (token) {
            window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token
        }
    }
}

// Initialize CSRF token on load
refreshCsrfToken()

// Request interceptor to ensure CSRF token is always set
window.axios.interceptors.request.use(
    (config: InternalAxiosRequestConfig) => {
        refreshCsrfToken()
        return config
    },
    (error: AxiosError) => {
        return Promise.reject(error)
    }
)

// Response interceptor
window.axios.interceptors.response.use(
    (response: AxiosResponse) => {
        // Update CSRF token from response headers if provided
        const newToken = response.headers['x-csrf-token']
        if (newToken) {
            const metaTag = document.head.querySelector('meta[name="csrf-token"]')
            if (metaTag) {
                metaTag.setAttribute('content', newToken)
            }
            window.axios.defaults.headers.common['X-CSRF-TOKEN'] = newToken
        }
        return response
    },
    (error: AxiosError) => {
        const status = error.response?.status

        switch (status) {
            // In this switch statement, we handle the HTTP errors that need to be redirected to
            // a different page in the site. This should not intercept the error that needs to 
            // be displayed on the current page the user is on, e.g: 422 (validation errors)
            case 401:
            case 419:
                // Only redirect if not already on login/register page
                if (window.location.pathname !== '/login' && window.location.pathname !== '/register') {
                    window.location.href = '/login'
                }
                break
        }

        return Promise.reject(error)
    }
)