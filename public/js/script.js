function Staff (id) {
    "use strict";
    var canvas = $(id).get(0);

    function clear(w, h) {
        canvas.width = w;
        canvas.height = h;
        canvas.getContext("2d").clearRect(0, 0, w, h);
    }

    function drawNote(note, x, y) {
        var img = new Image();
        img.src = "/img/" + encodeURIComponent(note) + ".png";
        img.onload = function () {
            canvas.getContext("2d").drawImage(img, x, y);
        };
    }

    this.drawMelody = function (melody) {
        var i = 0;
        var width = 34;  // note images are 34x100
        var height = 100;
        var pos = 55;    // clef image is 55x100

        clear(pos + width * melody.length, height);

        drawNote("clef", 0, 0);
        while (i < melody.length) {
            drawNote(melody[i], pos, 0);
            pos += width;
            ++i;
        }
    };
}

jQuery(document).ready(function ($) {
    "use strict";
    var form = $("form");
    var staff = new Staff("canvas");

    var encoded = "";

    form.submit(function (e) {
        $.ajax({
            type: form.attr("method"),
            url: form.attr("action"),
            data: form.serialize(),
            datType: "json"
        }).done(function (resp) {
            $("#thanks").attr("aria-hidden", true);
            $("#download, #result, #vote").attr("aria-hidden", false);

            staff.drawMelody(resp.melody);

            encoded = encodeURIComponent(resp.melody.join(","));
            $("#download").attr("href", "/midi/" + encoded);
        });
        e.preventDefault();
    });

    $("#vote-yes, #vote-no").click(function (e) {
        $("#vote").attr("aria-hidden", true);
        $("#thanks").attr("aria-hidden", false);

        $.ajax({
            type: "post",
            url: "/vote/" + encoded,
            data: {vote: ($(this).text() == "Yes") ? "Y" : "N"}
        });
        e.preventDefault();
    });
});
