/**
 * Toohga | client side api script
 */

const entryForm = document.getElementById("entryForm");
const urlField = document.getElementById("url");

let lastShortenedUrl = "";

entryForm.onsubmit = e => {
    let givenUrl = urlField.value;

    if(givenUrl !== lastShortenedUrl || lastShortenedUrl === "") {
        if(givenUrl.indexOf("://") < 0) {
            givenUrl = "https://" + givenUrl;
        }

        const formData = new FormData();
        formData.append("longUrl", givenUrl);

        fetch("/", {
            method: "POST",
            body: formData
        })
            .then(resp => resp.json())
            .then(json => {
                if(json.status === "success") {
                    const shortUrl = json.shortUrl;

                    lastShortenedUrl = shortUrl;
                    urlField.value = shortUrl;
                }
            });
    }

    e.preventDefault();
};
