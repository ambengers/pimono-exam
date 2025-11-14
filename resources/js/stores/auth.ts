import { defineStore } from 'pinia'
import axios from 'axios'
import type { User } from '@/interfaces/user'

export const useAuthStore = defineStore('auth', {
    state: (): { user: User | null } => ({
        user: null,
    }),

    getters: {
        isLoggedIn: (state) => state.user !== null
    },

    actions: {
        async loadAuth() {
            await axios.get('/auth/user')
                .then(({ data }) => {
                    this.user = data.data
                })
        },
    },
})