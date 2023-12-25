/**
 * Toohga client | shortened URL store
 */

import { defineStore } from "pinia"

import { useAuthStore } from "./auth.js"

export const useUrlStore = defineStore("url", {
    state: () => ({
        urls: [],
    }),
    actions: {
        async loadUrls() {
            const auth = useAuthStore()

            let res
            try {
                const resp = await fetch(
                    `${import.meta.env.VITE_API_ENDPOINT || "/admin/api"}/url`,
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

            this.urls = res.short_urls
        },
        async deleteUrl(id) {
            const auth = useAuthStore()

            const resp = await fetch(
                `${
                    import.meta.env.VITE_API_ENDPOINT || "/admin/api"
                }/url/${id}`,
                {
                    method: "DELETE",
                    headers: {
                        Authorization: `Bearer ${auth.token}`,
                    },
                }
            )

            if (resp.status === 204) {
                await this.loadUrls()

                return
            }

            const res = await resp.json()
            if (res.error) {
                throw new Error(res.error.code)
            }
        },
        async cleanUpUrls() {
            const auth = useAuthStore()

            const resp = await fetch(
                `${
                    import.meta.env.VITE_API_ENDPOINT || "/admin/api"
                }/urlCleanup`,
                {
                    method: "POST",
                    headers: {
                        Authorization: `Bearer ${auth.token}`,
                    },
                }
            )

            if (resp.status === 204) {
                await this.loadUrls()

                return
            }

            const res = await resp.json()
            if (res.error) {
                throw new Error(res.error.code)
            }
        },
    },
})
