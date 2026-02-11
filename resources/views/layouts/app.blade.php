<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'CDM 2026 Simulator') }}</title>

    {{-- Bootstrap 5 via CDN --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    {{-- Icônes de drapeaux (flag-icons) --}}
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/flag-icons@6.6.6/css/flag-icons.min.css">

    @livewireStyles
</head>
<body class="bg-light d-flex flex-column min-vh-100">
    {{-- Barre de navigation principale --}}
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand fw-bold" href="{{ route('phases.index') }}">
                CDM 2026
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar"
                    aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="mainNavbar">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('phases.index') }}">Phases</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('knockout.bracket') }}">Tableau final</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    {{-- Contenu principal --}}
    <main class="flex-fill">
        <div class="container mb-4">
            {{ $slot ?? '' }}
        </div>
    </main>

    <footer class="mt-auto py-3 bg-white border-top">
        <div class="container text-center text-muted small">
            Coupe du Monde 2026 &mdash; Simulation Laravel / Livewire
        </div>
    </footer>

    {{-- Bootstrap JS (optionnel pour le navbar) --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
            crossorigin="anonymous"></script>

    @livewireScripts
</body>
</html>

