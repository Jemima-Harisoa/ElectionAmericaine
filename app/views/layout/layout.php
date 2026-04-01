<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Élection Américaine — <?= htmlspecialchars($pageTitle ?? 'Dashboard', ENT_QUOTES, 'UTF-8') ?></title>

  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link rel="stylesheet" href="/css/style.css" />

  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=Source+Serif+4:ital,wght@0,300;0,400;0,600;1,300&display=swap" rel="stylesheet" />

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" defer></script>
</head>

<body>
<?php
$showNavbar = $showNavbar ?? true;
$currentUser = $currentUser ?? null;
$currentRole = is_array($currentUser) ? ($currentUser['role'] ?? null) : null;
?>

<?php if ($showNavbar): ?>
<nav class="app-nav">
  <a class="app-nav__logo" href="/tableau">US Election 2020</a>

  <div class="app-nav__links">
    <a href="/tableau">Tableau</a>
    <a href="/resultats">Résultats</a>
    <a href="/carte">Carte</a>
    <?php if ($currentRole === 'admin'): ?>
      <a href="/saisie">Saisie</a>
      <a href="/audit">Audit</a>
    <?php endif; ?>
  </div>

  <div class="app-nav__user">
    <?php if ($currentUser): ?>
      <span class="badge <?= $currentRole === 'admin' ? 'badge-admin' : 'badge-observer' ?>">
        <?= htmlspecialchars((string) $currentRole, ENT_QUOTES, 'UTF-8') ?>
      </span>
      <a href="/logout" class="btn btn-ghost btn-sm">Déconnexion</a>
    <?php else: ?>
      <a href="/login" class="btn btn-primary btn-sm">Connexion</a>
    <?php endif; ?>
  </div>
</nav>
<?php endif; ?>

<?= $content ?? '' ?>

</body>
</html>
