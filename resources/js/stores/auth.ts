import {defineStore} from 'pinia'

export const useAuthStore = defineStore('auth', {
    state: () => ({
        user: null,
        organization: null,
    }),

    getters: {
        isLoggedIn: (state) => state.user !== null
    },

    actions: {
        async loadAuth() {
            await axios
                .get('/auth/user')
                .then(({ data }) => {
                    this.user = data.data
                    this.organization = this.user.organization
                })
        },
    },
})