/**
 * Toohga client | client main file
 */

import { createApp } from "vue"
import { createPinia } from "pinia"

import { Notyf } from "notyf"
import "notyf/notyf.min.css"

import App from "./App.vue"
import router from "./router"

import "./../node_modules/bootstrap/dist/css/bootstrap.min.css"
import "./assets/main.css"

const app = createApp(App)

app.use(createPinia())
app.use(router)

app.provide(
    "notyf",
    new Notyf({
        duration: 5000,
    })
)

app.mount("#app")
