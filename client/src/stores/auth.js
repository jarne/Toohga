/**
 * Toohga client | auth credentials store
 */

import { defineStore } from "pinia"

export const useAuthStore = defineStore("auth", {
    state: () => ({
        token: "",
    }),
})
