<main class="app-main" style="max-width: 1200px; width: 100%; margin: 2rem auto;">
  <section class="card">
    <h1 class="card__title">Tableau des votes par etat (%)</h1>

    <?php if (!empty($success)): ?>
      <div class="alert alert-success" role="alert">Les votes ont ete enregistres avec succes.</div>
    <?php endif; ?>

    <?php if (empty($percentages)): ?>
      <div class="alert alert-info" role="alert">Aucune donnee de vote disponible pour le moment.</div>
    <?php else: ?>
      <div class="table-wrap">
        <table>
          <thead>
            <tr>
              <th>Etat</th>
              <th>Joe Biden (%)</th>
              <th>Donald Trump (%)</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($percentages as $state): ?>
              <tr>
                <td class="fw-bold"><?= htmlspecialchars((string) $state['state_name'], ENT_QUOTES, 'UTF-8') ?></td>
                <td>
                  <?php 
                    $bidenpct = (float) ($state['percentages']['Joe Biden'] ?? 0);
                    $trumppct = (float) ($state['percentages']['Donald Trump'] ?? 0);
                  ?>
                  <div class="progress-bar-wrap">
                    <div class="progress-bg">
                      <div class="progress-fill" style="width:<?= $bidenpct ?>%;background:var(--blue)"></div>
                    </div>
                    <span class="progress-pct"><?= number_format($bidenpct, 1) ?>%</span>
                  </div>
                </td>
                <td>
                  <div class="progress-bar-wrap">
                    <div class="progress-bg">
                      <div class="progress-fill" style="width:<?= $trumppct ?>%;background:var(--red)"></div>
                    </div>
                    <span class="progress-pct"><?= number_format($trumppct, 1) ?>%</span>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <div class="form-footer" style="margin-top: 1.5rem;">
        <a href="/saisie" class="btn btn-ghost">Retour saisie</a>
        <a href="/resultats" class="btn btn-primary">Voir resultats</a>
      </div>
    <?php endif; ?>
  </section>
</main>
