// Utilities
import { defineStore } from 'pinia'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    auth_token: null,
    user_name: '',
    user_role: 'authencated_user'
  }),
})
