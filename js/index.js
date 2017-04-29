/**
 * Created by jarne on 09.03.17.
 */

$(document).ready(function() {
    var lastShortenedUrl = "";

    $("#entryForm").submit(function(e) {
        var givenUrl = $("#url").val();

        if(givenUrl != lastShortenedUrl || lastShortenedUrl == "") {
            if(givenUrl.indexOf("://") < 0) {
                givenUrl = "https://" + givenUrl;
            }

            $.ajax({
                type: "POST",
                url: "",
                data: {
                    longUrl: givenUrl
                },
                success: function(data) {
                    if(data.status == "success") {
                        lastShortenedUrl = data.shortUrl;

                        $("#url").val(data.shortUrl);
                    }
                }
            });
        }

        e.preventDefault();
    });
});