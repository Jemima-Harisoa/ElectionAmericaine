<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Élection Américaine — <?= $pageTitle ?? 'Dashboard' ?></title>

  <!-- Bootstrap CSS (offline) -->
  <link rel="stylesheet" href="/css/style.css" />

  <!-- Google Font : Syne (display) + Source Serif 4 (body) -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=Source+Serif+4:ital,wght@0,300;0,400;0,600;1,300&display=swap" rel="stylesheet" />

</head>

<body>

<!-- ═══════════════════════════════════════════
     NAVBAR
════════════════════════════════════════════ -->
<nav class="app-nav">

  <!-- Logo -->
  <a class="app-nav__logo" href="/">
    <!-- SVG : étoile (symbole US) -->
    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
      <path d="M12 2L14.9 9.26L22.5 9.73L17 14.54L18.7 22L12 18.27L5.3 22L7 14.54L1.5 9.73L9.1 9.26L12 2Z"
            fill="#f4d35e" stroke="#f4d35e" stroke-width="1" stroke-linejoin="round"/>
    </svg>
    US Election 2020
  </a>

  <!-- Liens de navigation -->
  <div class="app-nav__links">

    <a href="/tableau" class="active">
      <!-- SVG : tableau / chart-bar -->
      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/>
      </svg>
      Tableau
    </a>

    <a href="/resultats">
      <!-- SVG : trophée -->
      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M6 9H4a2 2 0 0 1-2-2V5h4"/><path d="M18 9h2a2 2 0 0 0 2-2V5h-4"/>
        <path d="M12 17v4"/><path d="M8 21h8"/>
        <path d="M6 5v4a6 6 0 0 0 12 0V5H6z"/>
      </svg>
      Résultats
    </a>

    <a href="/carte">
      <!-- SVG : carte / map -->
      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <polygon points="3 6 9 3 15 6 21 3 21 18 15 21 9 18 3 21"/>
        <line x1="9" y1="3" x2="9" y2="18"/><line x1="15" y1="6" x2="15" y2="21"/>
      </svg>
      Carte
    </a>

    <!-- Lien admin uniquement — conditionnel PHP en vrai projet -->
    <a href="/saisie">
      <!-- SVG : crayon / edit -->
      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
      </svg>
      Saisie
    </a>

    <a href="/audit">
      <!-- SVG : historique / clock -->
      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
      </svg>
      Audit
    </a>

  </div>

  <!-- Utilisateur connecté -->
  <div class="app-nav__user">
    <span class="badge badge-admin">Admin</span>
    <a href="/logout" class="btn btn-ghost btn-sm">
      <!-- SVG : logout -->
      <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
        <polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/>
      </svg>
      Déconnexion
    </a>
  </div>

</nav>
