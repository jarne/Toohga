/**
 * Toohga client | user store
 */

import { defineStore } from "pinia"

import { useAuthStore } from "./auth.js"

export const useUserStore = defineStore("user", {
    state: () => ({
        users: [],
    }),
    actions: {
        async loadUsers() {
            const auth = useAuthStore()

            let res
            try {
                const resp = await fetch(
                    `${
                        import.meta.env.VITE_ADMIN_API_ENDPOINT || "/admin/api"
                    }/user`,
                    {
                        method: "GET",
                        headers: {
                            Authorization: `Bearer ${auth.token}`,
                        },
                    }
                )
                res = await resp.json()
            } catch (e) {
                return
            }

            if (res.error) {
                return
            }

            this.users = res.users
        },
        async createUser(uPin, displayName) {
            const auth = useAuthStore()

            const resp = await fetch(
                `${
                    import.meta.env.VITE_ADMIN_API_ENDPOINT || "/admin/api"
                }/user`,
                {
                    method: "POST",
                    headers: {
                        Authorization: `Bearer ${auth.token}`,
                        "Content-Type": "application/json",
                    },
                    body: JSON.stringify({
                        uniquePin: uPin,
                        displayName: displayName,
                    }),
                }
            )

            if (resp.status === 204) {
                await this.loadUsers()

                return
            }

            const res = await resp.json()
            if (res.error) {
                throw new Error(res.error.code)
            }
        },
        async deleteUser(id) {
            const auth = useAuthStore()

            const resp = await fetch(
                `${
                    import.meta.env.VITE_ADMIN_API_ENDPOINT || "/admin/api"
                }/user/${id}`,
                {
                    method: "DELETE",
                    headers: {
                        Authorization: `Bearer ${auth.token}`,
                    },
                }
            )

            if (resp.status === 204) {
                await this.loadUsers()

                return
            }

            const res = await resp.json()
            if (res.error) {
                throw new Error(res.error.code)
            }
        },
    },
})
