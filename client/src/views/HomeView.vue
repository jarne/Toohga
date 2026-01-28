<script lang="ts">
import "@fontsource/pacifico/index.css"
import type { Notyf } from "notyf"
import { inject } from "vue"

const notyf: Notyf = inject("notyf")!

export default {
    data() {
        const authReq = import.meta.env.TGA_AUTH_REQUIRED === "true"
        const contactMail = import.meta.env.TGA_CONTACT_EMAIL
        const theme = import.meta.env.TGA_THEME
        const privacyUrl = import.meta.env.TGA_PRIVACY_URL
        const analyticsScript = import.meta.env.TGA_ANALYTICS_SCRIPT

        let bgGradCols = []
        switch (theme) {
            case "pink":
                bgGradCols = ["#fcb5d9", "#f2d5e3"]
                break
            case "orange":
                bgGradCols = ["#fcd194", "#f2e0c6"]
                break
            default:
                bgGradCols = ["#b6f5f9", "#e6f0f2"]
                break
        }

        return {
            authReq,
            contactMail,
            privacyUrl,
            analyticsScript,
            bgGradCols,
            url: "",
            pin: "",
            showingResult: false,
        }
    },
    methods: {
        async sendForm() {
            if (this.url.indexOf("://") < 0) {
                this.url = "https://" + this.url
            }

            let res
            try {
                const resp = await fetch(
                    `${import.meta.env.TGA_API_ENDPOINT || "/api"}/create`,
                    {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                        },
                        body: JSON.stringify({
                            longUrl: this.url,
                            userPin: this.pin,
                        }),
                    }
                )
                res = await resp.json()
            } catch (e) {
                notyf.error("Connection to Toohga server failed")

                return
            }

            if (res.error) {
                switch (res.error.code) {
                    case "auth_failed":
                        notyf.error("Invalid authentication PIN")
                        break
                    case "internal_database_error":
                        notyf.error("Internal database error occurred")
                        break
                    default:
                        notyf.error("Unknown error occurred")
                        break
                }

                return
            }

            const short = res.short
            const urlInput = this.$refs.urlInput as HTMLInputElement

            this.showingResult = true
            this.url = short
            urlInput.focus()
        },
        async copyResultToClip() {
            try {
                await navigator.clipboard.writeText(this.url)
            } catch (e) {
                notyf.error("Cannot access the clipboard")
            }
        },
    },
}
</script>

<template>
    <div class="gradient-bg">
        <div class="points-bg-overlay">
            <div class="little-dark-background">
                <div class="main-content text-center">
                    <h1>Toohga</h1>
                    <h2>The smart URL shortener</h2>
                    <br />
                    <form id="entryForm" @submit.prevent="sendForm">
                        <div class="input-group input-group-lg">
                            <input
                                type="url"
                                required
                                class="form-control form-control-custom text-center"
                                id="urlInput"
                                ref="urlInput"
                                v-model="url"
                                :readonly="showingResult"
                                placeholder="Paste the long URL here ..."
                                autofocus
                                autocomplete="off"
                            />
                            <input
                                v-if="authReq"
                                type="password"
                                class="form-control form-control-custom form-control-pin text-center"
                                id="pinInput"
                                v-model="pin"
                                placeholder="PIN"
                            />
                            <button
                                type="button"
                                v-if="showingResult"
                                @click="copyResultToClip"
                                class="btn btn-custom"
                            >
                                <span
                                    class="oi oi-clipboard"
                                    aria-hidden="true"
                                ></span>
                            </button>
                            <button
                                type="submit"
                                :disabled="showingResult"
                                class="btn btn-custom"
                            >
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
                            <a :href="`mailto:${contactMail}`">{{
                                contactMail
                            }}</a>
                        </span>
                        <span v-if="privacyUrl">
                            | <a :href="privacyUrl">privacy policy</a>
                        </span>
                    </p>
                </div>
            </div>
        </div>
    </div>
    <div v-if="analyticsScript" v-html="analyticsScript"></div>
</template>

<style lang="scss" scoped>
@import "./../assets/bootstrapCustom.scss";

.gradient-bg,
.points-bg-overlay {
    height: 100%;
}

.gradient-bg {
    background: linear-gradient(
        to bottom right,
        v-bind("bgGradCols[0]"),
        v-bind("bgGradCols[1]")
    );
}

.points-bg-overlay {
    display: flex;
    justify-content: center;
    align-items: center;
    flex-direction: column;

    background: url("./../assets/points-overlay.svg") no-repeat center center
        fixed;

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
        rgba(white, 0.6),
        rgba(white, 0.85)
    );

    width: 95vw;
    max-width: 1220px;

    border-radius: 16px;
}

.main-content {
    width: 90%;

    padding: 40px 20px 40px 20px;
}

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

#entryForm {
    padding-bottom: 10px;
}

$input-opacity: 0.075;
$shadow-opacity: 0.15;
$custom-input-bg: rgba(black, $input-opacity);

.form-control-custom,
.form-control-custom[readonly],
.form-control-custom:focus {
    background-color: $custom-input-bg;
    border-color: $custom-input-bg;
}

.form-control-custom:focus {
    box-shadow: 0 0 2px 0.2rem rgba(black, $shadow-opacity);
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
    background-color: $custom-input-bg;
}

.btn-custom:hover {
    background-color: shade-color($custom-input-bg, 10%);
}

.btn-custom {
    padding: 11px 13px 5px 19px !important;
}

.footer-line a,
.footer-line a:visited {
    color: $gray-600;
}

.footer-line a:hover,
.footer-line a:active {
    color: $gray-700;
}

@media (min-width: 1150px) {
    .main-content {
        width: 1100px;
    }
}

@include color-mode(dark) {
    .little-dark-background {
        background: linear-gradient(
            to bottom right,
            rgba(black, 0.8),
            rgba(black, 0.95)
        );
    }

    $input-opacity: 0.2;
    $shadow-opacity: 0.35;
    $custom-input-bg: rgba(white, $input-opacity);

    .form-control-custom,
    .form-control-custom[readonly],
    .form-control-custom:focus {
        background-color: $custom-input-bg;
        border-color: $custom-input-bg;
    }

    .form-control-custom:focus {
        box-shadow: 0 0 2px 0.2rem rgba(white, $shadow-opacity);
    }

    .btn-custom {
        background-color: $custom-input-bg;
    }

    .btn-custom:hover {
        background-color: shade-color($custom-input-bg, 10%);
    }

    .footer-line a,
    .footer-line a:visited {
        color: $gray-400;
    }

    .footer-line a:hover,
    .footer-line a:active {
        color: $gray-300;
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
