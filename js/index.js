/**
 * Created by jarne on 09.03.17.
 */

$(document).ready(function() {
    $("#submit").click(function() {
        $.ajax({
            type: "POST",
            url: "",
            data: {longUrl: $("#url").val()},
            success: function(data) {
                if(data.status == "success") {
                    $("#url").val(data.shortUrl);
                }
            }
        });
    });
});