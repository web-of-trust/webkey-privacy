// Utilities
import axios from 'axios'
import { defineStore } from 'pinia'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    token: localStorage.getItem('AUTH_TOKEN'),
    user: {
      displayName: localStorage.getItem('USER_DISPLAY_NAME'),
      email: localStorage.getItem('USER_EMAIL'),
    },
  }),

  getters: {
    token: (state) => state.token,
    user: (state) => state.user,
  },

  actions: {
    async login(username: string, password: string) {
      await axios.post('/login', {
        username: username,
        password: password,
      })
      .then(function (response) {
        console.log(response)
      })
      .catch(function (error) {
         console.log(error)
      });
    },

    logout() {
      this.token = null
      this.user = {
        displayName: '',
        email: '',
      }
      localStorage.removeItem('AUTH_TOKEN')
      localStorage.removeItem('USER_DISPLAY_NAME')
      localStorage.removeItem('USER_EMAIL')
    },
  },
})
