<script setup></script>

<template>
    <div class="container">
        <div
            id="toastContainer"
            class="toast-container position-fixed top-0 end-0 p-3"
        ></div>
        <nav class="navbar navbar-light navbar-custom">
            <div class="container-fluid justify-content-center">
                <a class="navbar-brand" href="/admin">
                    <img
                        src="./../assets/AppIcon.png"
                        width="30"
                        height="30"
                        class="d-inline-block align-top"
                        alt="Toohga icon"
                    />
                    <span class="space-left">Toohga admin center</span>
                </a>
            </div>
        </nav>
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
                <tbody id="urls"></tbody>
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
                <tbody id="users"></tbody>
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
                        <label for="delShortId">Delete URL</label>
                    </h5>
                    <form id="delUrlForm">
                        <div class="input-group">
                            <input
                                type="text"
                                class="form-control"
                                id="delShortId"
                                placeholder="Short ID"
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
                        <label for="createUserPIN">Create user</label>
                    </h5>
                    <form id="createUserForm">
                        <p>
                            <input
                                type="text"
                                class="form-control"
                                id="createUserDisplayName"
                                placeholder="Display name"
                            />
                        </p>
                        <div class="input-group">
                            <input
                                type="text"
                                class="form-control"
                                id="createUserPIN"
                                placeholder="Unique PIN"
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
                        <label for="delUserId">Delete user</label>
                    </h5>
                    <form id="delUserForm">
                        <div class="input-group">
                            <input
                                type="text"
                                class="form-control"
                                id="delUserId"
                                placeholder="User ID"
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
                    <form id="cleanupForm">
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
    margin-bottom: 35px;
}

.toast-container {
    z-index: 1060;
}

.navbar-custom {
    margin-bottom: 20px;
    padding: 15px;

    border-radius: 25px;
    box-shadow: 0 0 7px #d3d3d3;
}

.space-left {
    margin-left: 8px;
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
