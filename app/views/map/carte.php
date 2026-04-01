<main class="app-main" style="max-width: 100%; padding: 2rem 1rem;">
  <section class="card">
    <h1 class="card__title">Carte des résultats électoraux</h1>
    <p style="color: var(--text-2, #6b7f99); margin-bottom: 1.5rem;">Cliquez sur un état pour voir les détails</p>

    <div class="states-grid">
      <?php foreach ($mapData as $state): ?>
        <div 
          class="state-box" 
          data-state-id="<?= (int) $state['state_id'] ?>"
          style="background-color: <?= htmlspecialchars($state['color'], ENT_QUOTES, 'UTF-8') ?>;"
          onclick="fetchStateDetail(<?= (int) $state['state_id'] ?>)"
        >
          <div class="state-name">
            <?= htmlspecialchars((string) $state['state_name'], ENT_QUOTES, 'UTF-8') ?>
          </div>
          <div class="state-electors">
            <?= (int) $state['electoral_votes'] ?> GE
          </div>
          <?php if ($state['candidate_name'] !== '(À remplir)'): ?>
            <div class="state-candidate">
              <?= htmlspecialchars((string) $state['candidate_name'], ENT_QUOTES, 'UTF-8') ?>
            </div>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
    </div>

    <div style="margin-top: 2rem; padding: 1rem; background-color: rgba(69, 123, 221, 0.05); border-radius: 4px;">
      <p style="margin: 0; font-size: 14px; color: var(--text-2, #6b7f99);">
        <strong>Légende:</strong> 
        <span style="display: inline-block; width: 16px; height: 16px; background-color: #457bdd; border-radius: 2px; vertical-align: middle; margin: 0 0.5rem;"></span> Biden (Démocrate)
        <span style="display: inline-block; width: 16px; height: 16px; background-color: #d42121; border-radius: 2px; vertical-align: middle; margin: 0 0.5rem;"></span> Trump (Républicain)
        <span style="display: inline-block; width: 16px; height: 16px; background-color: #d5dfef; border-radius: 2px; vertical-align: middle; margin: 0 0.5rem;"></span> À remplir
      </p>
    </div>
  </section>
</main>

<!-- Modal pour afficher les détails -->
<div class="modal fade" id="stateDetailModal" tabindex="-1" aria-labelledby="stateDetailLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="stateDetailLabel">Détails de l'état</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
      </div>
      <div class="modal-body" id="stateDetailContent">
        <!-- Contenu chargé dynamiquement -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
      </div>
    </div>
  </div>
</div>

<style>
.states-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
  gap: 0.75rem;
  margin-bottom: 2rem;
}

.state-box {
  padding: 1rem;
  border-radius: 6px;
  cursor: pointer;
  transition: all 0.2s ease;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  text-align: center;
  min-height: 120px;
  border: 2px solid transparent;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.state-box:hover {
  transform: scale(1.05);
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
  border-color: var(--text-1, #1e2a3a);
}

.state-name {
  font-weight: 600;
  font-size: 13px;
  color: white;
  margin-bottom: 0.5rem;
}

.state-electors {
  font-size: 16px;
  font-weight: bold;
  color: white;
  margin-bottom: 0.5rem;
}

.state-candidate {
  font-size: 11px;
  color: rgba(255, 255, 255, 0.9);
  font-style: italic;
}

@media (max-width: 768px) {
  .states-grid {
    grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
    gap: 0.5rem;
  }

  .state-box {
    min-height: 90px;
  }

  .state-name {
    font-size: 11px;
  }

  .state-electors {
    font-size: 14px;
  }

  .state-candidate {
    font-size: 9px;
  }
}
</style>

<script>
function fetchStateDetail(stateId) {
  console.log('Fetching details for state ID:', stateId);
  
  fetch('/carte/etat/' + stateId)
    .then(response => {
      console.log('Response status:', response.status);
      
      if (!response.ok) {
        return response.json().then(data => {
          throw new Error(data.error || 'Erreur lors du chargement');
        }).catch(e => {
          throw new Error('Erreur: ' + response.status);
        });
      }
      return response.json();
    })
    .then(data => {
      console.log('Data received:', data);
      displayStateDetail(data);
      const modal = new bootstrap.Modal(document.getElementById('stateDetailModal'));
      modal.show();
    })
    .catch(error => {
      console.error('Erreur complète:', error);
      alert('Impossible de charger les détails: ' + error.message);
    });
}

function displayStateDetail(data) {
  console.log('Displaying state detail:', data);
  let html = '<table class="table table-sm">';
  html += '<thead><tr><th>Candidat</th><th style="text-align: right;">Voix</th></tr></thead>';
  html += '<tbody>';

  if (data.votes && Array.isArray(data.votes)) {
    data.votes.forEach(vote => {
      html += '<tr>';
      html += '<td>' + escapeHtml(vote.candidate) + '</td>';
      html += '<td style="text-align: right;">' + vote.votes + '</td>';
      html += '</tr>';
    });
  }

  html += '</tbody></table>';
  document.getElementById('stateDetailContent').innerHTML = html;
}

function escapeHtml(text) {
  const map = {
    '&': '&amp;',
    '<': '&lt;',
    '>': '&gt;',
    '"': '&quot;',
    "'": '&#039;'
  };
  return text.replace(/[&<>"']/g, m => map[m]);
}
</script>
