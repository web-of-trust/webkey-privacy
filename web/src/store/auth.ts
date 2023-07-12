// Utilities
import axios from 'axios'
import { defineStore } from 'pinia'

export const useAuthStore = defineStore('auth', {
    state: (): AuthState => {
        return {
            token: localStorage.getItem('AUTH_TOKEN'),
            user: {
                displayName: localStorage.getItem('USER_DISPLAY_NAME'),
                email: localStorage.getItem('USER_EMAIL'),
            },
        }
    },

    getters: {
        token: (state) => state.token,
        user: (state) => state.user,
    },

    actions: {
        login(username: string, password: string) {
            const self = this;
            axios.post('/login', {
                username: username,
                password: password,
            }, {
                headers: {
                    'Authorization': 'Basic ' + window.btoa(username + ':' + password),
                    'Content-Type': 'application/x-www-form-urlencoded',
                }
            })
            .then(function (response) {
                self.token = response.data.token
                self.user = {
                    displayName: response.data.displayName,
                    email: response.data.email,
                }
                localStorage.setItem('AUTH_TOKEN', response.data.token)
                localStorage.setItem('USER_DISPLAY_NAME', response.data.displayName)
                localStorage.setItem('USER_EMAIL', response.data.email)
            })
            .catch(function (error) {
                 console.log(error)
            });
        },

        logout() {
            const self = this;
            axios.post('/logout').then(function (response) {
                self.token = null
                self.user = {
                    displayName: null,
                    email: null,
                }
                localStorage.removeItem('AUTH_TOKEN')
                localStorage.removeItem('USER_DISPLAY_NAME')
                localStorage.removeItem('USER_EMAIL')
                window.location.pathname = '/login'
            })
            .catch(function (error) {
                 console.log(error)
            });
        },
    },
})

interface AuthState {
    token: string | null
    user: UserInfo
}

interface UserInfo {
    displayName: string | null
    email: string | null
}
