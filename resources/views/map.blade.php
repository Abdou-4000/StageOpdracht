<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Map</title>
    <!-- Add Leaflet before Vue -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    @vite(['resources/css/app.css', 'resources/js/app.ts'])
    <style>
        html, body {
            margin: 0;
            padding: 0;
            height: 100vh;
            width: 100vw;
            overflow: hidden;
        }
        #app {
            height: 100vh;
            width: 100vw;
        }

    </style>
</head>
<body>
    <div class="header">
        <img src="{{ asset('images/Logo.png') }}" alt="Logo">
    </div>
    <div id="app">
        <teacher-map :teachers='@json($teachers)'></teacher-map>
    </div>
</body>
</html>