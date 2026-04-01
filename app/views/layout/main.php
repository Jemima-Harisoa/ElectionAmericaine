<!-- ═══════════════════════════════════════════
     CONTENU PRINCIPAL
════════════════════════════════════════════ -->
<main class="app-main">

  <!-- ── ALERT flash (succès) ── -->
  <div class="alert alert-success">
    <!-- SVG : check-circle -->
    <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;margin-top:2px">
      <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
      <polyline points="22 4 12 14.01 9 11.01"/>
    </svg>
    <span>Votes enregistrés avec succès pour la <strong>Californie</strong>.</span>
  </div>


  <!-- ── STAT CARDS (KPI) ── -->
  <div class="stat-grid">

    <div class="stat-card">
      <div class="stat-card__accent accent-blue"></div>
      <div class="stat-card__label">Joe Biden</div>
      <div class="stat-card__value text-blue">306</div>
      <div class="stat-card__desc">grands électeurs</div>
    </div>

    <div class="stat-card">
      <div class="stat-card__accent accent-red"></div>
      <div class="stat-card__label">Donald Trump</div>
      <div class="stat-card__value text-red">232</div>
      <div class="stat-card__desc">grands électeurs</div>
    </div>

    <div class="stat-card">
      <div class="stat-card__accent accent-gold"></div>
      <div class="stat-card__label">États saisis</div>
      <div class="stat-card__value text-gold">50</div>
      <div class="stat-card__desc">sur 50 états</div>
    </div>

    <div class="stat-card">
      <div class="stat-card__accent accent-blue"></div>
      <div class="stat-card__label">Vainqueur</div>
      <div class="stat-card__value" style="font-size:1.3rem">Biden</div>
      <div class="stat-card__desc">270 requis pour gagner</div>
    </div>

  </div>


  <!-- ── FORMULAIRE DE SAISIE ── -->
  <div class="card">
    <div class="card__title">
      <!-- SVG : edit -->
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--gold)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
      </svg>
      Saisie des résultats
    </div>

    <form action="/saisie" method="POST">

      <div class="form-grid">

        <div class="form-group">
          <label class="form-label" for="state_id">État</label>
          <select class="form-control" id="state_id" name="state_id" required>
            <option value="">— Choisir un état —</option>
            <option value="1">Californie (54 GE)</option>
            <option value="2">Texas (40 GE)</option>
            <option value="3">New York (28 GE)</option>
            <option value="4">Floride (30 GE)</option>
          </select>
        </div>

        <div class="form-group">
          <label class="form-label" for="votes_biden">Voix — Joe Biden</label>
          <input type="number" class="form-control" id="votes_biden" name="votes_biden"
                 placeholder="Ex : 11110250" min="0" required />
          <span class="form-hint">Nombre de votes populaires</span>
        </div>

        <div class="form-group">
          <label class="form-label" for="votes_trump">Voix — Donald Trump</label>
          <input type="number" class="form-control is-invalid" id="votes_trump" name="votes_trump"
                 placeholder="Ex : 6006429" min="0" required />
          <span class="form-error">Ce champ est requis.</span>
        </div>

      </div>

      <div class="divider"></div>

      <div class="form-footer">
        <button type="reset" class="btn btn-ghost">Annuler</button>
        <button type="submit" class="btn btn-primary">
          <!-- SVG : save -->
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
            <polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/>
          </svg>
          Enregistrer
        </button>
      </div>

    </form>
  </div>


  <!-- ── FILTRES + TABLEAU ── -->
  <div class="card">

    <div class="card__title">
      <!-- SVG : list -->
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--blue)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <line x1="8" y1="6"  x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/>
        <line x1="8" y1="18" x2="21" y2="18"/>
        <line x1="3" y1="6"  x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/>
        <line x1="3" y1="18" x2="3.01" y2="18"/>
      </svg>
      Résultats par état
    </div>

    <!-- Barre de filtres -->
    <div class="filter-bar" style="margin-bottom:1.25rem">

      <input type="text" placeholder="Rechercher un état…" />

      <select>
        <option value="">Tous les vainqueurs</option>
        <option value="biden">Biden (bleu)</option>
        <option value="trump">Trump (rouge)</option>
      </select>

      <select>
        <option value="">Trier par…</option>
        <option value="name">Nom</option>
        <option value="electoral">Grands électeurs ↓</option>
        <option value="diff">Écart de votes</option>
      </select>

      <button class="btn btn-ghost btn-sm">
        <!-- SVG : refresh -->
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <polyline points="1 4 1 10 7 10"/>
          <path d="M3.51 15a9 9 0 1 0 .49-3.5"/>
        </svg>
        Réinitialiser
      </button>

    </div>

    <!-- Tableau -->
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>État</th>
            <th>GE</th>
            <th>Vainqueur</th>
            <th>Biden %</th>
            <th>Trump %</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>

          <tr>
            <td class="fw-bold">Californie</td>
            <td><span class="badge badge-neutral">54</span></td>
            <td><span class="badge badge-blue">Biden</span></td>
            <td>
              <div class="progress-bar-wrap">
                <div class="progress-bg"><div class="progress-fill" style="width:63.5%;background:var(--blue)"></div></div>
                <span class="progress-pct">63,5%</span>
              </div>
            </td>
            <td>
              <div class="progress-bar-wrap">
                <div class="progress-bg"><div class="progress-fill" style="width:34.3%;background:var(--red)"></div></div>
                <span class="progress-pct">34,3%</span>
              </div>
            </td>
            <td>
              <div class="d-flex gap-2">
                <button class="btn btn-ghost btn-sm btn-icon" title="Modifier">
                  <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                  </svg>
                </button>
                <button class="btn btn-ghost btn-sm btn-icon" title="Historique">
                  <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                  </svg>
                </button>
              </div>
            </td>
          </tr>

          <tr>
            <td class="fw-bold">Texas</td>
            <td><span class="badge badge-neutral">40</span></td>
            <td><span class="badge badge-red">Trump</span></td>
            <td>
              <div class="progress-bar-wrap">
                <div class="progress-bg"><div class="progress-fill" style="width:46.5%;background:var(--blue)"></div></div>
                <span class="progress-pct">46,5%</span>
              </div>
            </td>
            <td>
              <div class="progress-bar-wrap">
                <div class="progress-bg"><div class="progress-fill" style="width:52.1%;background:var(--red)"></div></div>
                <span class="progress-pct">52,1%</span>
              </div>
            </td>
            <td>
              <div class="d-flex gap-2">
                <button class="btn btn-ghost btn-sm btn-icon" title="Modifier">
                  <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                  </svg>
                </button>
                <button class="btn btn-ghost btn-sm btn-icon" title="Historique">
                  <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                  </svg>
                </button>
              </div>
            </td>
          </tr>

          <tr>
            <td class="fw-bold">Floride</td>
            <td><span class="badge badge-neutral">30</span></td>
            <td><span class="badge badge-red">Trump</span></td>
            <td>
              <div class="progress-bar-wrap">
                <div class="progress-bg"><div class="progress-fill" style="width:47.9%;background:var(--blue)"></div></div>
                <span class="progress-pct">47,9%</span>
              </div>
            </td>
            <td>
              <div class="progress-bar-wrap">
                <div class="progress-bg"><div class="progress-fill" style="width:51.2%;background:var(--red)"></div></div>
                <span class="progress-pct">51,2%</span>
              </div>
            </td>
            <td>
              <div class="d-flex gap-2">
                <button class="btn btn-ghost btn-sm btn-icon" title="Modifier">
                  <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                  </svg>
                </button>
                <button class="btn btn-ghost btn-sm btn-icon" title="Historique">
                  <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                  </svg>
                </button>
              </div>
            </td>
          </tr>

        </tbody>
      </table>
    </div><!-- /table-wrap -->

  </div><!-- /card -->

</main>

