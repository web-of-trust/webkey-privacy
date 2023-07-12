// Utilities
import axios from 'axios'
import { defineStore } from 'pinia'

export const useAuthStore = defineStore('auth', {
    state: (): AuthState => {
        return {
            token: localStorage.getItem('AUTH_TOKEN'),
            user: {
                identity: localStorage.getItem('USER_IDENTITY'),
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
                localStorage.setItem('AUTH_TOKEN', response.data.token)
                localStorage.setItem('USER_IDENTITY', response.data.user.identity)
                localStorage.setItem('USER_DISPLAY_NAME', response.data.user.displayName)
                localStorage.setItem('USER_EMAIL', response.data.user.email)

                self.token = localStorage.getItem('AUTH_TOKEN')
                self.user = {
                    identity: localStorage.getItem('USER_IDENTITY'),
                    displayName: localStorage.getItem('USER_DISPLAY_NAME'),
                    email: localStorage.getItem('USER_EMAIL'),
                }
            })
            .catch(function (error) {
                 console.log(error)
            });
        },

        logout() {
            const self = this;
            axios.get('/logout').then(function (response) {
                localStorage.removeItem('AUTH_TOKEN')
                localStorage.removeItem('USER_IDENTITY')
                localStorage.removeItem('USER_DISPLAY_NAME')
                localStorage.removeItem('USER_EMAIL')

                self.token = null
                self.user = {
                    identity: null,
                    displayName: null,
                    email: null,
                }

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
    identity: string | null
    displayName: string | null
    email: string | null
}
