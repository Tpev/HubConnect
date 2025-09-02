<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>TS-UI Upload Probe</title>

  @tallstackuiStyles
  @livewireStyles
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 min-h-screen">
  <div class="max-w-2xl mx-auto py-10 px-4">
    {{ $slot }}
  </div>

  @livewireScripts
  @tallstackuiScripts
</body>
</html>
