<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Youtube Scraper</title>

    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="https://pagination.js.org/dist/2.1.5/pagination.css">

    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="https://pagination.js.org/dist/2.1.5/pagination.min.js"></script>

    <style>
        a {
            color: #333;
            text-decoration: none;
            outline: 0;
        }

        a:hover {
            color: #808080;
            text-decoration: none;
            outline: 0;
        }

        .container {
            margin: 5px;
        }

        .data-container {
            overflow: auto;
            margin: 15px 0;
        }

        .data-container ul {
            margin: 0;
            padding-left: 0;
        }

        .data-container li {
            background: #EEE;
            margin-bottom: 3px;
            padding: 8px;
            line-height: 1em;
            list-style: none;
        }

        input.filter {
            width: 250px;
        }

        .ui-autocomplete {
            max-width: 250px;
        }
    </style>
</head>

<body>
<div class="container">
    <h2>Videos & Statistic</h2>
    <hr>

    <div class="ui-widget">
        <table>
            <tr>
                <td>
                    <label for="tag"><h4>Filters</h4></label>
                </td>
                <td><input class="filter" type="text" name="tag" id="tag" placeholder="by tag"></td>
            </tr>
            <tr>
                <td></td>
                <td>
                    <input class="filter" type="number" name="performance" id="performance" placeholder="by performance limit"
                           title="First hour views after video uploaded divided by channels’ all videos first hour views median">
                </td>
            </tr>
        </table>
        <hr>
        <h4>[performance] Caption</h4>
        <div id="video-list">
            <div class="data-container"></div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {

        $('#tag').bind("keyup change paste input", function () {
            let tag = $(this).val();
            $.ajax({
                url: "search-videos-by-tag",
                type: "GET",
                data: {'tag': tag},

                success: function (data) {
                    makeVideoList(data)
                }
            })
            $('#performance').val('');
        });

        $('#performance').bind("keyup change paste input", function () {
            let performance = $(this).val();
            $.ajax({
                url: "filter-videos-by-performance",
                type: "GET",
                data: {'performance': performance},

                success: function (data) {
                    makeVideoList(data)
                }
            })
            $('#tag').val('');
        });

        $.ajax({
            url: "get-all-videos",
            type: "GET",

            success: function (data) {
                makeVideoList(data)
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
            $('#video-list').pagination({
                dataSource: videos,
                pageSize: 15,

                callback: function (data, pagination) {
                    $('.data-container').html(videosTemplate(data));
                }
            })
        }

        function videosTemplate(videos) {
            let content = '<ul>';
            for (let i = 0; i < videos.length; i++) {
                content += '<li><a href="https://www.youtube.com/watch?v='
                    + videos[i]['video_id']
                    + '" target="_blank" title=" ' + videos[i]['description'] + ' ">'
                    + '[' + videos[i]['performance'] + '] '
                    + videos[i]['title']
                    + '</a></li>';
            }
            content += '</ul>';

            return content;
        }

    });
</script>
</body>
</html>
