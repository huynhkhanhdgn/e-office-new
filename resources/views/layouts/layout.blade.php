<!DOCTYPE html>
<?php
$locale = Helpers::getLocale(); //return vi, en, zh, ja
?>
<html lang="en">
<head>
    <base href="./">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <meta name="description" content="CoreUI - Open Source Bootstrap Admin Template">
    <meta name="author" content="Diginet Corp.">
    <meta name="keyword" content="Bootstrap,Admin,Template,Open,Source,jQuery,CSS,HTML,RWD,Dashboard">
    <title>eOffice</title>
    @include('layouts.head')
</head>
@if (Helpers::getDevice() != 'DESKTOP')
    <body class="app header-fixed sidebar-fixed aside-menu-fixed sidebar-lg-show">
@else
    <body class="app sidebar-fixed">
@endif
        @include('layouts.top')
        <div class="app-body">
            @include('layouts.sidebar')
            <main class="main">
                @include('layouts.breadcrumb')
                @include('layouts.container')
            </main>
            @include('layouts.aside-menu')
        </div>
        <footer class="app-footer hide">
            @include('layouts.footer')
        </footer>
        </body>
        <style>
            .app-body {
                /*-webkit-animation-name: example; !* Safari 4.0 - 8.0 *!*/
                /*-webkit-animation-duration: 4s; !* Safari 4.0 - 8.0 *!*/
                /*animation-name: example;*/
                /*animation-duration: 1s;*/
            }

            /* Safari 4.0 - 8.0 */
            @-webkit-keyframes example {
                0% {
                    opacity: 0
                }
                25% {
                    opacity: 0.25
                }
                50% {
                    opacity: 0.5
                }
                100% {
                    opacity: 1
                }
            }

            /* Standard syntax */
            @keyframes example {
                0% {
                    opacity: 0
                }
                25% {
                    opacity: 0.25
                }
                50% {
                    opacity: 0.5
                }
                100% {
                    opacity: 1
                }
            }
        </style>
        <script>
            //store resources for using of javascript
            var langText = JSON.parse('{!! json_encode(\Lang::get('message')) !!}');
            var lang = "{{\Helpers::getLang()}}";

            $(".nav-item").click(function (evt) {
                $(".dropdown").removeClass('show');
            });

            $(document).ready(function () {
                setTimeout(function () {
//            $(".app-body").removeClass("opacity").fadeIn(function(){
//                resizePqGrid();
//            });
                }, 100);

            });

            $(window).resize(function () {
                setTimeout(function () {
                    resizePqGrid();
                }, 200);
            });


        </script>
        @yield('script')
        <div id="spinLoading" class="loading hide">Loading&#8230;</div>
</html>
<div id="divModalContainer"></div>
</html>

<script>
    //store resources for using of javascript
    var langText = JSON.parse('{!! json_encode(Lang::get('message')) !!}');
    var lang = "{{Helpers::getLang()}}";

</script>


