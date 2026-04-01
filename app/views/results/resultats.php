<main class="app-main" style="max-width: 980px; width: 100%; margin: 2rem auto;">
  <section class="card">
    <h1 class="card__title">Résultats de l'élection 2020</h1>

    <?php if (!empty($candidates)): ?>
      <div style="margin-bottom: 2rem;">
        <table class="table" style="width: 100%; border-collapse: collapse;">
          <thead>
            <tr style="border-bottom: 2px solid var(--border-1, #d5dfef);">
              <th style="text-align: left; padding: 0.75rem; font-weight: 600;">Candidat</th>
              <th style="text-align: right; padding: 0.75rem; font-weight: 600;">Grands électeurs</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($candidates as $candidate): ?>
              <tr style="border-bottom: 1px solid var(--border-1, #d5dfef);">
                <td style="padding: 0.75rem;">
                  <?= htmlspecialchars((string) $candidate['name'], ENT_QUOTES, 'UTF-8') ?>
                </td>
                <td style="text-align: right; padding: 0.75rem; font-weight: 500;">
                  <?= (int) $candidate['total_electors'] ?>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>

        <?php if (!empty($winner)): ?>
          <div style="margin-top: 1.5rem; padding: 1rem; background-color: var(--bg-success, #e6f5e6); border-left: 4px solid var(--success, #2d7d2d); border-radius: 4px;">
            <p style="margin: 0; color: var(--text-1, #1e2a3a); font-weight: 600;">
              🏆 Vainqueur : 
              <strong style="color: var(--success, #2d7d2d);">
                <?= htmlspecialchars((string) $winner['name'], ENT_QUOTES, 'UTF-8') ?>
              </strong>
              <span style="color: var(--text-2, #6b7f99);">
                (<?= (int) $winner['total_electors'] ?> grands électeurs)
              </span>
            </p>
          </div>
        <?php endif; ?>
      </div>
    <?php else: ?>
      <div style="padding: 2rem; text-align: center; color: var(--text-2, #6b7f99);">
        <p>Aucune donnée disponible pour cette élection.</p>
      </div>
    <?php endif; ?>

    <div class="form-footer" style="margin-top: 2rem; display: flex; gap: 1rem;">
      <a href="/tableau" class="btn btn-secondary">Voir les statistiques</a>
      <a href="/resultats/pdf" class="btn btn-primary">Exporter en PDF</a>
    </div>
  </section>
</main>

<style>
.table {
  background-color: var(--surface, #ffffff);
  width: 100%;
  border-collapse: collapse;
}

.table thead {
  background-color: rgba(69, 123, 221, 0.05);
}

.table th,
.table td {
  padding: 0.75rem;
  text-align: left;
  border-bottom: 1px solid var(--border-1, #d5dfef);
}

.table tbody tr:hover {
  background-color: rgba(69, 123, 221, 0.02);
}

.form-footer {
  display: flex;
  gap: 1rem;
  margin-top: 2rem;
}

.btn {
  padding: 0.5rem 1.5rem;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-weight: 500;
  text-decoration: none;
  display: inline-block;
  text-align: center;
  transition: all 0.2s ease;
}

.btn-primary {
  background-color: var(--blue, #457bdd);
  color: white;
}

.btn-primary:hover {
  background-color: #3560b8;
}

.btn-secondary {
  background-color: var(--border-1, #d5dfef);
  color: var(--text-1, #1e2a3a);
}

.btn-secondary:hover {
  background-color: #c4d0e0;
}
</style>
