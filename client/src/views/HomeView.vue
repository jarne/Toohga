<script setup>
import "@fontsource/pacifico"

const authReq = import.meta.env.VITE_AUTH_REQUIRED
const deleteAfterDays = import.meta.env.VITE_DELETE_AFTER_DAYS
const contactMail = import.meta.env.VITE_CONTACT_EMAIL
const theme = import.meta.env.VITE_THEME
const privacyUrl = import.meta.env.VITE_PRIVACY_URL
const analyticsScript = import.meta.env.VITE_ANALYTICS_SCRIPT
</script>

<template>
    <div class="full-box">
        <div class="little-dark-background">
            <div class="main-content text-center">
                <h1>Toohga</h1>
                <h2>The smart URL shortener</h2>
                <br />
                <form id="entryForm">
                    <div class="input-group input-group-lg">
                        <input
                            type="url"
                            class="form-control form-control-custom text-center"
                            id="url"
                            placeholder="Paste the long URL here ..."
                        />
                        <input
                            v-if="authReq"
                            type="password"
                            class="form-control form-control-custom form-control-pin text-center"
                            id="pin"
                            placeholder="PIN"
                        />
                        <button class="btn btn-custom">
                            <span
                                class="oi oi-chevron-right"
                                aria-hidden="true"
                            ></span>
                        </button>
                    </div>
                </form>
                <br />
                <p class="footer-line">
                    open source on
                    <a
                        target="_blank"
                        rel="noopener noreferrer"
                        href="https://github.com/jarne/Toohga"
                        >GitHub</a
                    >
                    <span v-if="contactMail">
                        | contact:
                        <a :href="`mailto:${contactMail}`">{{ contactMail }}</a>
                    </span>
                    <span v-if="privacyUrl">
                        | <a href="/privacy">privacy policy</a>
                    </span>
                </p>
            </div>
        </div>
    </div>
    <div v-if="analyticsScript" v-html="analyticsScript"></div>
</template>

<style scoped>
h1 {
    font-family: "Pacifico", sans-serif;

    font-size: 72px;
    line-height: 96px;
}

h2 {
    margin-top: 12px;

    font-size: 32px;
    line-height: 48px;
}

p {
    font-size: 18px;
    line-height: 24px;
}

.full-box {
    height: 100%;

    display: flex;
    justify-content: center;
    align-items: center;
    flex-direction: column;

    background: url("/assets/dest/images/points-overlay.svg") no-repeat center
        center fixed;

    -webkit-background-size: cover;
    -moz-background-size: cover;
    -o-background-size: cover;
    background-size: cover;
}

.little-dark-background {
    display: flex;
    justify-content: center;
    flex-direction: row;

    background: linear-gradient(
        to bottom right,
        rgba(0, 0, 0, 0.8),
        rgba(0, 0, 0, 0.95)
    );

    width: 95vw;
    max-width: 1220px;

    border-radius: 16px;
}

.main-content {
    width: 90%;

    color: white;

    padding: 40px 20px 40px 20px;
}

#entryForm {
    padding-bottom: 10px;
}

.form-control-custom,
.form-control-custom[readonly] {
    background-color: rgba(255, 255, 255, 0.2);
    border-color: rgba(255, 255, 255, 0.2);

    color: white;
}

.form-control-custom:focus {
    background-color: rgba(255, 255, 255, 0.2);
    border-color: rgba(255, 255, 255, 0.2);

    color: white;

    box-shadow: 0 0 2px 0.2rem rgba(255, 255, 255, 0.35);
}

.form-control-custom::placeholder {
    color: #bebebe;
}

.form-control-pin {
    max-width: 75px;
}

@media (min-width: 768px) {
    .form-control-pin {
        max-width: 150px;
    }
}

.btn-custom {
    background-color: rgba(255, 255, 255, 0.2);

    color: white;
}

.btn-custom:hover {
    color: rgba(255, 255, 255, 0.6);
}

.btn-custom:focus {
    box-shadow: 0 0 2px 0.2rem rgba(255, 255, 255, 0.35);
}

.btn-custom {
    padding: 11px 13px 5px 19px !important;
}

.footer-line a,
.footer-line a:visited {
    color: #c5c5c5;
}

.footer-line a:hover,
.footer-line a:active {
    text-decoration: none;

    color: #949494;
}

@media (min-width: 1150px) {
    .main-content {
        width: 1100px;
    }
}
</style>

<style>
html,
body,
#app {
    height: 100%;
}
</style>
