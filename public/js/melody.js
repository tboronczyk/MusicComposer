jQuery(document).ready(function ($) {
    var staff = document.getElementById("staff"),
        paramForm = $("#paramForm");

    function msg(title, text) {
        $("#msg").show().html(
            "<strong>" + title + "</strong><br>" + text
        );
    }

    staff.clear = function (w, h) {
        staff.width = w;
        staff.height = h;
        this.getContext("2d").clearRect(0, 0, w, h);
    };

    staff.drawNote = function (note, x, y) {
        var img = new Image(),
            ctx = this.getContext("2d");

        img.src = "/img/" + encodeURIComponent(note) + ".png";
        img.onload = function () {
            ctx.drawImage(img, x, y);
        };
    };

    paramForm.submit(function () {
        var result = $("#result"),
            midi = $("#midi"),
            vote = $("#vote");

        $.ajax({
            type: paramForm.attr("method"),
            url: paramForm.attr("action"),
            data: paramForm.serialize(),
            datType: "json"
        }).fail(function (jqXHR, resp, err) {
            msg(jqXHR.status + " " + err, jqXHR.responseText);
            result.hide();
            staff.clear(0, 0);
        }).done(function (resp) {
            var i = 0,
                len = resp.melody.length,
                offset = 34,
                pos = 55,
                height = 90,
                encoded = encodeURIComponent(resp.melody.join("."));

            staff.clear(pos + offset * len, height);
            staff.drawNote("clef", 0, 0);
            for (; i < len; i++) {
                staff.drawNote(resp.melody[i], pos, 0);
                pos += offset;
            }

            midi.attr("href", "/melody/midi/" + encoded);
            vote.attr("data-melody", encoded).show();
            result.show();

            msg("Data", resp.melody.join(" "));
        });
        return false;
    });

    $("#voteYes").click(function () {
        $("#vote").hide();
        $.ajax({
            type: "post",
            url: "/melody/vote/" + $("#vote").attr("data-melody"),
            data: {vote: "Y"}
        });
    });

    $("#voteNo").click(function () {
        $("#vote").hide();
        $.ajax({
            type: "post",
            url: "/melody/vote/" + $("#vote").attr("data-melody"),
            data: {vote: "N"}
        });
    });
});
