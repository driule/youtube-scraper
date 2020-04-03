<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Youtube Scraper</title>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    </head>

    <body>
        <div class="container" style="margin-top: 50px;">
            <h2 class="text-center">Videos & Statistic</h2>
            <hr>

            <label for="tag">search: </label>
            <input type="text" name="tag" id="tag" placeholder="by tag">

            <div id="video-list"></div>
        </div>

        <script type="text/javascript">
            $(document).ready(function () {

                $.ajax({
                    url: "get-all-videos",
                    type: "GET",

                    success: function (data) {
                        $('#video-list').html(makeVideoList(data));
                    }
                });

                $('#tag').on('keyup', function () {
                    var tagInput = $(this).val();
                    $.ajax({
                        url: "search-video-by-tag",
                        type: "GET",
                        data: {'tag': tagInput},

                        success: function (data) {
                            $('#video-list').html(makeVideoList(data));
                        }
                    })
                });

                function makeVideoList(videos) {
                    let content = '<ul class="list-group" style="display: block; position: relative; z-index: 1">';
                    for (let i = 0; i < videos.length; i++) {
                        content += '<li class="list-group-item">' + videos[i]['title'] + '</li>';
                    }
                    content += '</ul>';

                    return content;
                }
            });
        </script>
    </body>
</html>
