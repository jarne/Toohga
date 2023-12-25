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
                            Authorization: `Bearer ${user.token}`,
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

            this.urls = res.urls
        },
    },
})
