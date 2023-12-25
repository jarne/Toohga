/**
 * Toohga client | main router
 */

import { createRouter, createWebHistory } from "vue-router"

import HomeView from "./../views/HomeView.vue"

const router = createRouter({
    history: createWebHistory(import.meta.env.BASE_URL),
    routes: [
        {
            path: "/",
            name: "home",
            component: HomeView,
        },
        {
            path: "/admin-auth",
            name: "admin-auth",
            component: () => import("./../views/AdminLoginView.vue"),
        },
        {
            path: "/admin",
            name: "admin",
            component: () => import("./../views/AdminView.vue"),
        },
    ],
})

export default router
