<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
    <!doctype html>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Sqrl Authorization Page</title>

        <!-- Scripts 
        <script src="{{ asset('js/app.js') }}" defer></script>
        -->

        <!-- Fonts -->
        <link rel="dns-prefetch" href="//fonts.gstatic.com">
        <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

        <!-- Styles 
        -->
        <link href="{{ asset('css/app.css') }}" rel="stylesheet">                

        <script>
            var myUrl = '{{ env('APP_URL') }}';
            var pass =  '{{ env('MAIL_PASSWORD') }}';
            var nut = '{{$nut}}'
            window.onload = function load() {
                console.log("Evento de carregamento detectado!");
                document.location.href = 'sqrl' + myUrl.slice(myUrl.indexOf(':'));
            }
            window.Laravel = <?php echo json_encode([
                'csrfToken' => csrf_token(),
            ]); ?>
        </script>

        <!-- icon pack-->
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    </head>
    <body>    
        <div class="container" id="sqrl-authorization">
            <div class="row my-5">
                <div class="col-md-12 m-auto animatebottom">
                    <div class="jumbotron">
                        <h1 class="display-4">Sqrl Authorization</h1>
                        <p class="lead">verifying your sqrl identity</p>
                        <hr class="my-4">
                        <div class="form-group">
                            <div class="progress">
                                <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%"></div>
                            </div>
                            <p class="lead">please make sure you have the sqrl desktop application installed</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>