$(document).ready(function () {
    $(".btn-generate").on("click", function () {
        $.getJSON("index.php?action=generate", function (data) {
            var correlationId = data.correlationId
            var number = data.number
            $(".number").text(number)
            $(".btn-generate").removeClass('btn-primary')
            $(".btn-generate").addClass('btn-default')
            $(".btn-generate").off("click")

            setTimeout(refreshLog, 1000, correlationId);
        });
    });
});

function refreshLog(correlationId) {
    $.getJSON("index.php?action=info&correlationId="+correlationId, function (data) {
        $(".log-container").text("")
        $.each(data.log, function( key, row ) {
            $(".log-container").append("<p>"+row+"</p>")
        })

        setTimeout(refreshLog, 3000, correlationId);
    });
}