<main class="app-main" style="max-width: 1200px; width: 100%; margin: 2rem auto;">
  <section class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
      <h1 class="card__title">Audit & Historique</h1>
      <a href="/audit/export" class="btn btn-primary" style="margin: 0;">
        Exporter CSV
      </a>
    </div>

    <?php if (empty($history)): ?>
      <div style="padding: 2rem; text-align: center; color: var(--text-2, #6b7f99);">
        <p>Aucune modification enregistrée</p>
      </div>
    <?php else: ?>
      <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; background-color: var(--surface, #ffffff);">
          <thead>
            <tr style="border-bottom: 2px solid var(--border, #d5dfef);">
              <th style="text-align: left; padding: 0.75rem; font-weight: 600; color: var(--text-1, #1e2a3a);">Date</th>
              <th style="text-align: left; padding: 0.75rem; font-weight: 600; color: var(--text-1, #1e2a3a);">Utilisateur</th>
              <th style="text-align: left; padding: 0.75rem; font-weight: 600; color: var(--text-1, #1e2a3a);">État</th>
              <th style="text-align: left; padding: 0.75rem; font-weight: 600; color: var(--text-1, #1e2a3a);">Candidat</th>
              <th style="text-align: center; padding: 0.75rem; font-weight: 600; color: var(--text-1, #1e2a3a);">Ancienne valeur</th>
              <th style="text-align: center; padding: 0.75rem; font-weight: 600; color: var(--text-1, #1e2a3a);">Nouvelle valeur</th>
              <th style="text-align: center; padding: 0.75rem; font-weight: 600; color: var(--text-1, #1e2a3a);">Action</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($history as $entry): ?>
              <tr style="border-bottom: 1px solid var(--border, #d5dfef);">
                <td style="padding: 0.75rem; color: var(--text-1, #1e2a3a);">
                  <?= htmlspecialchars($entry['changed_at_formatted'], ENT_QUOTES, 'UTF-8') ?>
                </td>
                <td style="padding: 0.75rem;">
                  <span style="background-color: var(--surface-2, #eef3fb); padding: 0.25rem 0.75rem; border-radius: 4px; font-size: 12px; font-weight: 500; color: var(--text-1, #1e2a3a);">
                    <?= htmlspecialchars($entry['username'], ENT_QUOTES, 'UTF-8') ?>
                  </span>
                </td>
                <td style="padding: 0.75rem; color: var(--text-1, #1e2a3a);">
                  <?= htmlspecialchars($entry['state_name'], ENT_QUOTES, 'UTF-8') ?>
                </td>
                <td style="padding: 0.75rem; color: var(--text-1, #1e2a3a);">
                  <?= htmlspecialchars($entry['candidate_name'], ENT_QUOTES, 'UTF-8') ?>
                </td>
                <td style="padding: 0.75rem; text-align: center; color: var(--text-2, #6b7f99);">
                  <?php if ($entry['old_value'] !== null): ?>
                    <?= htmlspecialchars($entry['old_value'], ENT_QUOTES, 'UTF-8') ?>
                  <?php else: ?>
                    <em>-</em>
                  <?php endif; ?>
                </td>
                <td style="padding: 0.75rem; text-align: center; font-weight: 600; color: var(--blue, #457bdd);">
                  <?= htmlspecialchars($entry['new_value'], ENT_QUOTES, 'UTF-8') ?>
                </td>
                <td style="padding: 0.75rem; text-align: center;">
                  <button 
                    class="btn btn-secondary" 
                    style="padding: 0.35rem 0.75rem; font-size: 12px;"
                    onclick="rollbackEntry(<?= (int) $entry['id'] ?>)"
                  >
                    Annuler
                  </button>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </section>
</main>

<style>
.app-main {
  flex: 1;
  width: 100%;
}

.card {
  background: var(--surface, #ffffff);
  border-radius: 12px;
  padding: 2rem;
  box-shadow: var(--shadow, 0 6px 24px rgba(30, 60, 114, .08));
  margin-bottom: 2rem;
}

.card__title {
  font-size: 1.75rem;
  margin-bottom: 1.5rem;
  color: var(--text-1, #1e2a3a);
}

.btn {
  display: inline-block;
  padding: 0.5rem 1rem;
  border: none;
  border-radius: 6px;
  font-weight: 500;
  cursor: pointer;
  text-decoration: none;
  font-size: 14px;
  font-family: var(--font-display, 'Syne', sans-serif);
  transition: background-color 0.2s ease, color 0.2s ease, border-color 0.2s ease;
}

.btn-primary {
  background-color: var(--blue, #457bdd);
  color: white;
}

.btn-primary:hover {
  background-color: #3564c4;
}

.btn-secondary {
  background-color: var(--surface-2, #eef3fb);
  color: var(--text-1, #1e2a3a);
  border: 1px solid var(--border, #d5dfef);
}

.btn-secondary:hover {
  background-color: var(--border, #d5dfef);
}
</style>

<script>
function rollbackEntry(entryId) {
  if (!confirm('Êtes-vous sûr de vouloir annuler cette modification ?')) {
    return;
  }

  const formData = new FormData();
  formData.append('entry_id', entryId);

  fetch('/audit/rollback', {
    method: 'POST',
    body: formData
  })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        alert('Rollback effectué avec succès');
        location.reload();
      } else {
        alert('Erreur: ' + (data.error || 'Erreur lors du rollback'));
      }
    })
    .catch(error => {
      console.error('Erreur:', error);
      alert('Erreur lors de la requête');
    });
}
</script>

