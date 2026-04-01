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
        <label class="form-label" for="state_id">Etat</label>
        <select class="form-control" id="state_id" name="state_id" onchange="window.location.href='/saisie?state_id=' + this.value" required>
          <?php foreach ($states as $state): ?>
            <option value="<?= (int) $state['id'] ?>" <?= (int) $stateId === (int) $state['id'] ? 'selected' : '' ?>>
              <?= htmlspecialchars((string) $state['name'], ENT_QUOTES, 'UTF-8') ?> (<?= (int) $state['electoral_votes'] ?> GE)
            </option>
          <?php endforeach; ?>
        </select>
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
                value="<?= (int) ($existingVotes[$cid] ?? 0) ?>"
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
