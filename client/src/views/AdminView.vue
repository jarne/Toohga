<script lang="ts">
import { storeToRefs } from "pinia"

import AdminHeader from "./../components/admin/AdminHeader.vue"

import { useAuthStore } from "./../stores/auth.js"
import { useUrlStore } from "./../stores/url.js"
import { useUserStore } from "./../stores/user.js"

export default {
    inject: ["notyf"],
    components: {
        AdminHeader,
    },
    data() {
        const urlStore = useUrlStore()
        const userStore = useUserStore()

        const { urls } = storeToRefs(urlStore)
        const { users } = storeToRefs(userStore)

        return {
            urls,
            users,
            delShortId: "",
            createUserDisplayName: "",
            createUserPIN: "",
            delUserId: "",
        }
    },
    methods: {
        async sendDelUrl() {
            const urlStore = useUrlStore()

            try {
                await urlStore.deleteUrl(this.delShortId)
            } catch (e) {
                switch (e.message) {
                    case "internal_database_error":
                        this.notyf.error(
                            `URL with ID ${this.delShortId} cannot be looked up in database`
                        )
                        break
                    default:
                        this.notyf.error(
                            `Unknown error occurred when trying to delete URL: ${e.message}`
                        )
                        break
                }

                return
            }

            this.delShortId = ""
            this.notyf.success("URL was deleted.")
        },
        async sendCleanUrls() {
            const urlStore = useUrlStore()

            try {
                await urlStore.cleanUpUrls()
            } catch (e) {
                switch (e.message) {
                    case "internal_database_error":
                        this.notyf.error(
                            `An internal error with the database occurred`
                        )
                        break
                    default:
                        this.notyf.error(
                            `Unknown error occurred when trying to clean up URLs: ${e.message}`
                        )
                        break
                }

                return
            }

            this.notyf.success("URL clean up was executed successfully.")
        },
        async sendCreateUser() {
            const userStore = useUserStore()

            try {
                await userStore.createUser(
                    this.createUserPIN,
                    this.createUserDisplayName
                )
            } catch (e) {
                switch (e.message) {
                    case "parameters_missing":
                        this.notyf.error(
                            `Values required for creating the user are missing or empty`
                        )
                        break
                    case "internal_database_error":
                        this.notyf.error(
                            `User could not be written to database`
                        )
                        break
                    default:
                        this.notyf.error(
                            `Unknown error occurred when trying to create user: ${e.message}`
                        )
                        break
                }

                return
            }

            this.createUserPIN = ""
            this.createUserDisplayName = ""
            this.notyf.success("User was created.")
        },
        async sendDelUser() {
            const userStore = useUserStore()

            try {
                await userStore.deleteUser(this.delUserId)
            } catch (e) {
                switch (e.message) {
                    case "internal_database_error":
                        this.notyf.error(
                            `User with ID ${this.delUserId} cannot be looked up in database`
                        )
                        break
                    default:
                        this.notyf.error(
                            `Unknown error occurred when trying to delete user: ${e.message}`
                        )
                        break
                }

                return
            }

            this.delUserId = ""
            this.notyf.success("User was deleted.")
        },
    },
    async mounted() {
        const auth = useAuthStore()

        const urlStore = useUrlStore()
        const userStore = useUserStore()

        if (auth.token === "") {
            this.$router.push("/admin-auth")

            return
        }

        await urlStore.loadUrls()
        await userStore.loadUsers()
    },
}
</script>

<template>
    <div class="container">
        <AdminHeader />
        <p>Welcome to the Toogha admin center!</p>
        <h3>
            <span class="oi oi-clock heading-icon" aria-hidden="true"></span>
            Recently shortened URL's
        </h3>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Short ID</th>
                        <th scope="col">Target URL</th>
                        <th scope="col">Creator IP address</th>
                        <th scope="col">User display name</th>
                    </tr>
                </thead>
                <tbody id="urls">
                    <tr v-for="url in urls">
                        <th scope="row">{{ url.id }}</th>
                        <td>{{ url.shortId }}</td>
                        <td>{{ url.target }}</td>
                        <td>{{ url.client }}</td>
                        <td>{{ url.displayName }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <h3>
            <span class="oi oi-person heading-icon" aria-hidden="true"></span>
            Users
        </h3>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Unique PIN</th>
                        <th scope="col">Display name</th>
                    </tr>
                </thead>
                <tbody id="users">
                    <tr v-for="user in users">
                        <th scope="row">{{ user.id }}</th>
                        <td>{{ user.upin }}</td>
                        <td>{{ user.displayName }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <h3>
            <span class="oi oi-command heading-icon" aria-hidden="true"></span>
            Actions
        </h3>
        <div class="row">
            <div class="col-md-4 col-12">
                <div class="action-box">
                    <h5>
                        <span
                            class="oi oi-trash heading-icon-sm"
                            aria-hidden="true"
                        ></span>
                        <label for="delShortInput">Delete URL</label>
                    </h5>
                    <form id="delUrlForm" @submit.prevent="this.sendDelUrl">
                        <div class="input-group">
                            <input
                                type="text"
                                class="form-control"
                                id="delShortInput"
                                v-model="delShortId"
                                placeholder="Short ID"
                                autocomplete="off"
                            />
                            <button type="submit" class="btn btn-danger">
                                Delete
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-md-4 col-12">
                <div class="action-box">
                    <h5>
                        <span
                            class="oi oi-plus heading-icon-sm"
                            aria-hidden="true"
                        ></span>
                        <label for="createUserDisplayNameInput"
                            >Create user</label
                        >
                    </h5>
                    <form
                        id="createUserForm"
                        @submit.prevent="this.sendCreateUser"
                    >
                        <p>
                            <input
                                type="text"
                                class="form-control"
                                id="createUserDisplayNameInput"
                                v-model="createUserDisplayName"
                                placeholder="Display name"
                                autocomplete="off"
                            />
                        </p>
                        <div class="input-group">
                            <input
                                type="text"
                                class="form-control"
                                id="createUserPINInput"
                                v-model="createUserPIN"
                                placeholder="Unique PIN"
                                autocomplete="off"
                            />
                            <button type="submit" class="btn btn-primary">
                                Create
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-md-4 col-12">
                <div class="action-box">
                    <h5>
                        <span
                            class="oi oi-trash heading-icon-sm"
                            aria-hidden="true"
                        ></span>
                        <label for="delUserInput">Delete user</label>
                    </h5>
                    <form id="delUserForm" @submit.prevent="this.sendDelUser">
                        <div class="input-group">
                            <input
                                type="text"
                                class="form-control"
                                id="delUserInput"
                                v-model="delUserId"
                                placeholder="User ID"
                                autocomplete="off"
                            />
                            <button type="submit" class="btn btn-danger">
                                Delete
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4 col-12">
                <div class="action-box">
                    <h5>
                        <span
                            class="oi oi-layers heading-icon-sm"
                            aria-hidden="true"
                        ></span>
                        Cleanup URL's
                    </h5>
                    <form id="cleanupForm" @submit.prevent="this.sendCleanUrls">
                        <button type="submit" class="btn btn-danger">
                            Cleanup
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.container {
    margin-top: 25px;
    padding-bottom: 35px;
}

.container > h3 {
    margin-top: 15px;
}

.heading-icon {
    font-size: 23px;

    margin-right: 6px;
}

.heading-icon-sm {
    font-size: 18px;

    margin-right: 3px;
}

.action-box {
    margin: 7px 3px;
    padding: 20px 25px 22px 25px;

    border-radius: 15px;
    box-shadow: 0 0 7px #d3d3d3;
}

.action-box > h5 {
    margin-bottom: 15px;
}
</style>
