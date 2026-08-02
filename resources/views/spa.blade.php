<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ config('app.name', 'Vibe Fashion') }}</title>
    
    <!-- React Refresh needed for Vite + React -->
    @viteReactRefresh
    
    <!-- Scripts & Styles for React App -->
    @vite(['resources/react/main.tsx'])
</head>
<body>
    <div id="root"></div>
</body>
</html>
