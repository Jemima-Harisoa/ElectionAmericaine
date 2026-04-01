<main class="app-main" style="max-width: 980px; width: 100%; margin: 2rem auto;">
  <section class="card">
    <h1 class="card__title">Saisie des votes par etat</h1>

    <?php if (!empty($success)): ?>
      <div class="alert alert-success" role="alert">Votes enregistres avec succes.</div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
      <div class="alert alert-error" role="alert"><?= htmlspecialchars((string) $error, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>

    <form action="/saisie" method="post">
      <input type="hidden" name="election_id" value="<?= (int) $electionId ?>" />

      <div class="form-group" style="margin-bottom: 1rem;">
        <label class="form-label" for="state_id">État</label>
        <input
          class="form-control"
          id="state_id"
          name="state_id"
          type="text"
          list="states-list"
          placeholder="Rechercher un état…"
          value="<?= htmlspecialchars((string) ($currentStateName ?? ''), ENT_QUOTES, 'UTF-8') ?>"
          oninput="syncStateId(this)"
          required
        />
        <datalist id="states-list">
          <?php foreach ($states as $state): ?>
            <option
              value="<?= htmlspecialchars((string) $state['name'], ENT_QUOTES, 'UTF-8') ?> (<?= (int) $state['electoral_votes'] ?> GE)"
              data-id="<?= (int) $state['id'] ?>"
            >
            </option>
          <?php endforeach; ?>
        </datalist>
        <!-- Champ caché qui porte le vrai id à soumettre -->
        <input type="hidden" id="state_id_value" name="state_id" value="<?= (int) ($stateId ?? 0) ?>" />
      </div>

      <div class="row g-3">
        <?php foreach ($candidates as $candidate): ?>
          <?php $cid = (int) $candidate['id']; ?>
          <div class="col-12 col-md-6">
            <div class="form-group">
              <label class="form-label" for="votes_<?= $cid ?>">
                <?= htmlspecialchars((string) $candidate['name'], ENT_QUOTES, 'UTF-8') ?>
              </label>
              <input
                class="form-control"
                id="votes_<?= $cid ?>"
                name="votes[<?= $cid ?>]"
                type="number"
                min="0"
                required
                <?php if (isset($existingVotes[$cid])): ?>
                  value="<?= (int) $existingVotes[$cid] ?>"
                <?php endif; ?>
              />
            </div>
          </div>
        <?php endforeach; ?>
      </div>

      <div class="form-footer" style="margin-top: 1.5rem;">
        <button class="btn btn-primary" type="submit">Valider</button>
      </div>
    </form>
  </section>
</main>

<script>
function syncStateId(input) {
  const options = document.querySelectorAll('#states-list option');
  for (const opt of options) {
    if (opt.value === input.value) {
      const id = opt.dataset.id;
      document.getElementById('state_id_value').value = id;
      window.location.href = '/saisie?state_id=' + id;
      return;
    }
  }
  document.getElementById('votes-section').style.display = 'none';
}
</script>

<style>
datalist option {
  background-color: var(--surface);
  color: var(--text-1);
  padding: 0.5rem 1rem;
}

input[type="text"][list]::-webkit-calendar-picker-indicator {
  display: none;
}

input[list]::-webkit-outer-spin-button,
input[list]::-webkit-inner-spin-button {
  display: none;
}
</style>
