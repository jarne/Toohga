/**
 * Created by jarne on 09.03.17.
 */

$(document).ready(function() {
    var lastShortenedUrl = "";

    $("#submit").click(function() {
        if($("#url").val() != lastShortenedUrl || lastShortenedUrl == "") {
            $.ajax({
                type: "POST",
                url: "",
                data: {longUrl: $("#url").val()},
                success: function(data) {
                    if(data.status == "success") {
                        lastShortenedUrl = data.shortUrl;

                        $("#url").val(data.shortUrl);
                    }
                }
            });
        }
    });
});