<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Youtube Scraper</title>

    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
</head>

<body>
<div style="margin: 5px;">
    <h2>Videos & Statistic</h2>
    <hr>

    <div class="ui-widget">
        <label for="tag">search: </label>
        <input type="text" name="tag" id="tag" placeholder="by tag" style="width: 250px">
    </div>

    <div id="video-list"></div>
</div>

<script type="text/javascript">
    $(document).ready(function () {

        $('#tag').bind("keyup change paste input", function () {
            let tag = $(this).val();
            $.ajax({
                url: "search-video-by-tag",
                type: "GET",
                data: {'tag': tag},

                success: function (data) {
                    $('#video-list').html(makeVideoList(data));
                }
            })
        });

        $.ajax({
            url: "get-all-videos",
            type: "GET",

            success: function (data) {
                $('#video-list').html(makeVideoList(data));
            }
        });

        $.ajax({
            url: "get-all-tags",
            type: "GET",

            success: function (tags) {
                setTagsAutocomplete(tags)
            }
        });

        function setTagsAutocomplete(tags) {
            $('#tag').autocomplete({
                source: tags,
                // delay: 100
            });
        }

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
