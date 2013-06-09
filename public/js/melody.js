jQuery(document).ready(function ($) {

    function msg(title, text) {
        $("#msg").show().html(
            "<strong>" + title + "</strong><br>" + text);
    }

    var staff = document.getElementById("staff");

    staff.clear = function (w, h) {
        staff.width = w;
        staff.height = h;
        this.getContext('2d').clearRect(0, 0, w, h);
    };

    staff.drawNote = function (note, x, y) {
        var img = new Image(),
            ctx = this.getContext('2d');

        img.src = '/img/' + encodeURIComponent(note) + '.png';
        img.onload = function () {
            ctx.drawImage(img, x, y);
        }
    }

    var midi = document.getElementById("midi");

    midi.clear = function () {
        this.innerHTML = '';
    }

    midi.link = function(data) {
        this.innerHTML = '<a href="/midi?data=' +
            encodeURIComponent(data.join('.')) + '">midi</a>';
    }

    var paramForm = $("#paramForm");

    paramForm.submit(function () {
        $.ajax({
                type: paramForm.attr("method"),
                url: paramForm.attr("action"),
                data: paramForm.serialize(),
                datType: 'json'
            })
            .fail(function (jqXHR, resp, err) {
                msg(jqXHR.status + " " + err, jqXHR.responseText);
                staff.clear(0, 0);
                midi.clear();
            })
            .done(function (resp) {
                var i = 0,
                    len = resp.melody.length,
                    offset = 34,
                    pos = 55,
                    height = 90;

                msg("Data", resp.melody.join(' '));

                staff.clear(pos + offset * len, height);
                staff.drawNote('clef', 0, 0);
                for (; i < len; i++) {
                    staff.drawNote(resp.melody[i], pos, 0);
                    pos += offset;
                }

                midi.link(resp.melody);
            });
        return false;
    });
});
