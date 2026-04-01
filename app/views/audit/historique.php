<main class="app-main" style="max-width: 1200px; padding: 2rem 1rem; margin: 0 auto;">
  <section class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
      <h1 class="card__title">Audit & Historique</h1>
      <a href="/audit/export" class="btn btn-primary btn-sm" style="margin: 0;">
        Exporter CSV
      </a>
    </div>

    <?php if (empty($history)): ?>
      <div style="padding: 2rem; text-align: center; color: #6b7f99;">
        <p>Aucune modification enregistrée</p>
      </div>
    <?php else: ?>
      <div style="overflow-x: auto;">
        <table class="table table-sm" style="margin: 0;">
          <thead style="background-color: #f6f8fc;">
            <tr>
              <th style="padding: 0.75rem;">Date</th>
              <th style="padding: 0.75rem;">Utilisateur</th>
              <th style="padding: 0.75rem;">État</th>
              <th style="padding: 0.75rem;">Candidat</th>
              <th style="padding: 0.75rem; text-align: center;">Ancienne valeur</th>
              <th style="padding: 0.75rem; text-align: center;">Nouvelle valeur</th>
              <th style="padding: 0.75rem; text-align: center;">Action</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($history as $entry): ?>
              <tr style="border-bottom: 1px solid #e0e4e8;">
                <td style="padding: 0.75rem;">
                  <?= htmlspecialchars($entry['changed_at_formatted'], ENT_QUOTES, 'UTF-8') ?>
                </td>
                <td style="padding: 0.75rem;">
                  <span style="background-color: #f6f8fc; padding: 0.25rem 0.5rem; border-radius: 3px; font-size: 12px;">
                    <?= htmlspecialchars($entry['username'], ENT_QUOTES, 'UTF-8') ?>
                  </span>
                </td>
                <td style="padding: 0.75rem;">
                  <?= htmlspecialchars($entry['state_name'], ENT_QUOTES, 'UTF-8') ?>
                </td>
                <td style="padding: 0.75rem;">
                  <?= htmlspecialchars($entry['candidate_name'], ENT_QUOTES, 'UTF-8') ?>
                </td>
                <td style="padding: 0.75rem; text-align: center; color: #6b7f99;">
                  <?= $entry['old_value'] !== null ? htmlspecialchars($entry['old_value'], ENT_QUOTES, 'UTF-8') : '-' ?>
                </td>
                <td style="padding: 0.75rem; text-align: center; font-weight: 600; color: #457bdd;">
                  <?= htmlspecialchars($entry['new_value'], ENT_QUOTES, 'UTF-8') ?>
                </td>
                <td style="padding: 0.75rem; text-align: center;">
                  <button 
                    class="btn btn-sm" 
                    style="padding: 0.25rem 0.75rem; background-color: #f6f8fc; border: 1px solid #d5dfef; border-radius: 4px; cursor: pointer; font-size: 12px; color: #1e2a3a;"
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
