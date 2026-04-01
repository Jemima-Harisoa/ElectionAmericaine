<main class="app-main" style="max-width: 560px; width: 100%; margin: 4rem auto;">
  <section class="card">
    <h1 class="card__title">Connexion</h1>

    <?php if (!empty($error)): ?>
      <div class="alert alert-error" role="alert">
        <?= htmlspecialchars((string) $error, ENT_QUOTES, 'UTF-8') ?>
      </div>
    <?php endif; ?>

    <form action="/login" method="post" autocomplete="on">
      <div class="form-group">
        <label class="form-label" for="username">Nom d'utilisateur</label>
        <input
          class="form-control"
          id="username"
          name="username"
          type="text"
          required
          value="<?= htmlspecialchars((string) ($username ?? ''), ENT_QUOTES, 'UTF-8') ?>"
          placeholder="admin"
        />
      </div>

      <div class="form-group" style="margin-top: 1rem;">
        <label class="form-label" for="password">Mot de passe</label>
        <input
          class="form-control"
          id="password"
          name="password"
          type="password"
          required
          placeholder="Votre mot de passe"
        />
      </div>

      <div class="form-footer" style="margin-top: 1.25rem;">
        <button class="btn btn-primary" type="submit">Se connecter</button>
      </div>
    </form>
  </section>
</main>
