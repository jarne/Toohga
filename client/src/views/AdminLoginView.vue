<script lang="ts">
import AdminHeader from "./../components/admin/AdminHeader.vue"

import { useAuthStore } from "./../stores/auth.js"

export default {
    inject: ["notyf"],
    components: {
        AdminHeader,
    },
    data() {
        return {
            adminSecretKey: "",
        }
    },
    methods: {
        async sendLoginRequest() {
            let res
            try {
                const resp = await fetch(
                    `${
                        import.meta.env.TGA_ADMIN_API_ENDPOINT || "/admin/api"
                    }/auth`,
                    {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                        },
                        body: JSON.stringify({
                            admin_key: this.adminSecretKey,
                        }),
                    }
                )
                res = await resp.json()
            } catch (e) {
                this.notyf.error(
                    "Error while communicating with the login server!"
                )

                return
            }

            if (res.error) {
                switch (res.error.code) {
                    case "invalid_credentials":
                        this.notyf.error("Authentication request failed")
                        break
                    default:
                        this.notyf.error(
                            "Unknown error during authentication occurred"
                        )
                        break
                }

                return
            }

            const auth = useAuthStore()

            auth.$patch({
                token: res.jwt,
            })

            this.$router.push("/admin")
        },
    },
}
</script>

<template>
    <div class="container">
        <AdminHeader />
        <p>Please authenticate at the Toogha admin center!</p>
        <div class="auth-area">
            <h5>
                <span
                    class="oi oi-lock-locked heading-icon-sm"
                    aria-hidden="true"
                ></span>
                <label for="adminSecretKeyInput">Authentication</label>
            </h5>
            <form id="loginForm" @submit.prevent="this.sendLoginRequest">
                <div class="input-group">
                    <input
                        type="password"
                        required
                        class="form-control"
                        id="adminSecretKeyInput"
                        v-model="adminSecretKey"
                        placeholder="Management secret key"
                        autofocus
                    />
                    <button type="submit" class="btn btn-primary">
                        Authenticate
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<style scoped>
.container {
    margin-top: 25px;
    margin-bottom: 35px;
}

.auth-area {
    max-width: 540px;
}

.auth-area h5 {
    margin-top: 15px;
}

.heading-icon-sm {
    font-size: 18px;

    margin-right: 6px;
}
</style>
