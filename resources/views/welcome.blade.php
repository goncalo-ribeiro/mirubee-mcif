<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
    <!doctype html>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Scripts -->
        <script src="{{ asset('js/app.js') }}" defer></script>

        <!-- Fonts -->
        <link rel="dns-prefetch" href="//fonts.gstatic.com">
        <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

        <!-- Styles 
        -->
        <link href="{{ asset('css/app.css') }}" rel="stylesheet">                

        <script>
            var myUrl = '{{ env('APP_URL') }}';
            window.Laravel = <?php echo json_encode([
                'csrfToken' => csrf_token(),
            ]); ?>
        </script>

        <!-- cookie-->
        <script src="https://cdn.jsdelivr.net/npm/js-cookie@2/src/js.cookie.min.js"></script>

        <!-- icon pack-->
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

        <!-- echarts-->
        <!--
        <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/echarts/dist/echarts.min.js"></script>
    -->
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/echarts/4.6.0/echarts-en.common.js"></script>
            
    
        <!-- apexcharts-->    
<!--
        <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
-->

        <!-- chart.js-->
<!--
        <script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.2/dist/Chart.min.js"></script>
-->

        <!-- google charts-->
<!--
        <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
-->

        <!-- plotly-->
<!--
        <script src="https://cdn.plot.ly/plotly-1.2.0.min.js"></script>
-->

    </head>
    <body>
        <div id="app">
            <div class="content">
                
                <transition appear name="slide-fade" mode="out-in">
                    <keep-alive>
                        <router-view></router-view>
                    </keep-alive>
                </transition>
            </div>
        </div>
    </body>
</html>
