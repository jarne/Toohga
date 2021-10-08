/**
 * Toohga | admin panel client api script
 */

/* Alert functions */

const toastContainer = document.getElementById("toastContainer");

let toastCounter = 0;

function show(message, color, txtColor) {
    const toastIdStr = "toastMsg" + toastCounter++;

    const toastEl = document.createElement("div");
    toastEl.id = toastIdStr;
    toastEl.classList.add(
        "toast",
        "align-items-center",
        "text-" + txtColor,
        "bg-" + color,
        "border-0"
    );

    const dFlex = document.createElement("div");
    dFlex.classList.add("d-flex");

    const toastBody = document.createElement("div");
    toastBody.classList.add("toast-body");
    toastBody.innerHTML = message;

    const closeBtn = document.createElement("button");
    closeBtn.classList.add(
        "btn-close",
        "btn-close-" + txtColor,
        "me-2",
        "m-auto"
    );
    closeBtn.setAttribute("data-bs-dismiss", "toast");

    dFlex.appendChild(toastBody);
    dFlex.appendChild(closeBtn);

    toastEl.appendChild(dFlex);

    toastContainer.appendChild(toastEl);

    const bsToast = new bootstrap.Toast(toastEl);
    bsToast.show();
}

function showSuccess(message) {
    show(message, "dark", "white");
}

function showError(message) {
    show(message, "light", "black");
}

/* API data loading functions */

const urlsTable = document.getElementById("urls");
const usersTable = document.getElementById("users");

function loadUrlsTable() {
    fetch("/admin/api/url", {
        method: "GET",
    })
        .then((resp) => resp.json())
        .then((json) => {
            if (json.status === "success") {
                const urls = json.shortUrls;

                urls.forEach((url) => {
                    urlsTable.innerHTML +=
                        '<tr><th scope="row">' +
                        url.id +
                        "</th><td>" +
                        url.shortId +
                        "</td><td>" +
                        url.target +
                        "</td><td>" +
                        url.client +
                        "</td><td>" +
                        url.displayName +
                        "</td></tr>";
                });
            }
        });
}

function loadUsersTable() {
    fetch("/admin/api/user", {
        method: "GET",
    })
        .then((resp) => resp.json())
        .then((json) => {
            if (json.status === "success") {
                const users = json.users;

                users.forEach((user) => {
                    usersTable.innerHTML +=
                        '<tr><th scope="row">' +
                        user.id +
                        "</th><td>" +
                        user.upin +
                        "</td><td>" +
                        user.displayName +
                        "</td></tr>";
                });
            }
        });
}

loadUrlsTable();
loadUsersTable();

/* API action functions */

const delUrlForm = document.getElementById("delUrlForm");
const cleanupForm = document.getElementById("cleanupForm");
const delShortId = document.getElementById("delShortId");

const createUserForm = document.getElementById("createUserForm");
const createUserPIN = document.getElementById("createUserPIN");
const createUserDisplayName = document.getElementById("createUserDisplayName");

const delUserForm = document.getElementById("delUserForm");
const delUserId = document.getElementById("delUserId");

delUrlForm.onsubmit = (e) => {
    e.preventDefault();

    const shortId = delShortId.value;

    fetch("/admin/api/url/" + shortId, {
        method: "DELETE",
    })
        .then((resp) => resp.json())
        .then((json) => {
            if (json.status === "success") {
                showSuccess("URL successfully deleted!");

                return;
            }

            showError("URL couldn't be deleted!");
        });
};

cleanupForm.onsubmit = (e) => {
    e.preventDefault();

    fetch("/admin/api/urlCleanup", {
        method: "POST",
    })
        .then((resp) => resp.json())
        .then((json) => {
            if (json.status === "success") {
                showSuccess("URL's successfully cleaned up!");

                return;
            }

            showError("URL's couldn't be cleaned up!");
        });
};

createUserForm.onsubmit = (e) => {
    e.preventDefault();

    const uPin = createUserPIN.value;
    const displayName = createUserDisplayName.value;

    const formData = new FormData();
    formData.append("uniquePin", uPin);
    formData.append("displayName", displayName);

    fetch("/admin/api/user", {
        method: "POST",
        body: formData,
    })
        .then((resp) => resp.json())
        .then((json) => {
            if (json.status === "success") {
                showSuccess("User successfully created!");

                return;
            }

            showError("User couldn't be created!");
        });
};

delUserForm.onsubmit = (e) => {
    e.preventDefault();

    const userId = delUserId.value;

    fetch("/admin/api/user/" + userId, {
        method: "DELETE",
    })
        .then((resp) => resp.json())
        .then((json) => {
            if (json.status === "success") {
                showSuccess("User successfully deleted!");

                return;
            }

            showError("User couldn't be deleted!");
        });
};
