<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>ACTO</title>
    </head>
    <body>
        <div id="app" class="flex-center position-ref full-height">
            <card></card>
        </div>
    </body>
    <script src="{{ asset('/js/app.js') }}"></script>
</html>
