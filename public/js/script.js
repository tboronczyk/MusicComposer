jQuery(document).ready(function ($) {
    var myform = $("#myform");
    var staff  = $("#staff").get(0);

    staff.clear = function (w, h) {
        staff.width  = w;
        staff.height = h;
        this.getContext("2d").clearRect(0, 0, w, h);
    };

    staff.drawNote = function (note, x, y) {
        var ctx = this.getContext("2d");
        var img = new Image();

        img.src = "/img/" + encodeURIComponent(note) + ".png";
        img.onload = function () {
            ctx.drawImage(img, x, y);
        };
    };

    myform.submit(function () {
        var result = $("#result");
        var midi   = $("#midi");
        var vote   = $("#vote");

        $.ajax({
            type:    myform.attr("method"),
            url:     myform.attr("action"),
            data:    myform.serialize(),
            datType: "json"
        }).done(function (resp) {
            var i       = 0;
            var len     = resp.melody.length;
            var offset  = 34;
            var pos     = 55;
            var height  = 90;
            var encoded = encodeURIComponent(resp.melody.join("."));

            staff.clear(pos + offset * len, height);
            staff.drawNote("clef", 0, 0);
            for (; i < len; i++) {
                staff.drawNote(resp.melody[i], pos, 0);
                pos += offset;
            }
            midi.attr("href", "/midi/" + encoded);
            vote.attr("data-melody", encoded).removeClass("hidden");
            result.removeClass("hidden");
        });
        return false;
    });

    $(".voteLink").click(function () {
        var melody = $("#vote").attr("data-melody");
        var choice = ($(this).text() == "Yes") ? "Y" : "N";

        $("#vote").addClass("hidden");
        $.ajax({
            type: "post",
            url:  "/vote/" + melody,
            data: {
                vote: choice
            }
        });
        return false;
    });
});
