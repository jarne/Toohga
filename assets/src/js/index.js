/**
 * Toohga | client side api script
 */

const entryForm = document.getElementById("entryForm");
const urlField = document.getElementById("url");
const pinField = document.getElementById("pin");

let lastShortenedUrl = "";

entryForm.onsubmit = (e) => {
    let givenUrl = urlField.value;

    if (givenUrl !== lastShortenedUrl || lastShortenedUrl === "") {
        if (givenUrl.indexOf("://") < 0) {
            givenUrl = "https://" + givenUrl;
        }

        const formData = new FormData();
        formData.append("longUrl", givenUrl);

        if (pinField !== null) {
            formData.append("userPin", pinField.value);
        }

        fetch("/api/create", {
            method: "POST",
            body: formData,
        })
            .then((resp) => resp.json())
            .then((json) => {
                if (json.status === "success") {
                    const shortUrl = json.shortUrl;

                    lastShortenedUrl = shortUrl;
                    urlField.value = shortUrl;

                    urlField.readOnly = true;
                    urlField.focus();
                }
            });
    }

    e.preventDefault();
};
