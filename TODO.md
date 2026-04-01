# TODO — Élection Américaine
> Organisation par **module fonctionnel** — chaque module contient tout ce dont il a besoin (BDD, back, front)

---

## DÉJÀ FAIT
- [x] Tables BDD : `elections`, `states`, `candidates`, `election_candidates`, `votes`
- [x] Triggers SQL : validation votes > population, candidat non inscrit à l'élection
- [x] Vues SQL : `state_winners`, `election_results`, `election_winner`
- [x] Structure dossiers MVC : `controllers/`, `repositories/`, `services/`, `views/`
- [x] Fichiers de base : `bootstrap.php`, `config.php`, `routes.php`, `public/index.php`
- [x] Docker Compose + Dockerfile + `.htaccess`

---

##  MODULE 0 — Corrections bloquantes

### BDD
- [X] **Données `states`** — Insérer les 50 états US avec leur nom, nb de grands électeurs réels (2020) et population
- [X] **Données `candidates`** — Insérer Biden et Trump
- [X] **Données `elections`** — Insérer l'élection 2020
- [X] **Données `election_candidates`** — Associer Biden et Trump à l'élection 2020

---

##  MODULE 1 — Authentification

### BDD
- [ ] **Table `users`** — Créer avec les champs : `id`, `username`, `password_hash`, `role` (`admin` ou `observer`)
- [ ] **Insérer 2 utilisateurs de test** — 1 admin + 1 observer, mots de passe hashés avec `password_hash()`

### Back
- [ ] **`UserRepository::findByUsername`** — Cherche un utilisateur par son nom pour le login
- [ ] **`AuthService::login`** — Vérifie username + password, crée la session avec `user_id` et `role`
- [ ] **`AuthService::logout`** — Détruit la session et redirige vers `/login`
- [ ] **`AuthService::getCurrentUser`** — Retourne les infos de l'utilisateur depuis la session
- [ ] **`AuthService::isAdmin`** — Retourne `true` si le rôle en session est `admin`
- [ ] **`AuthService::requireAuth`** — Middleware : redirige vers `/login` si pas de session active
- [ ] **`AuthService::requireAdmin`** — Middleware : redirige avec erreur 403 si l'user n'est pas admin
- [ ] **`AuthController::showLogin`** — Affiche la page de login
- [ ] **`AuthController::handleLogin`** — Traite le POST, redirige vers `/tableau` si succès, affiche erreur sinon
- [ ] **`AuthController::handleLogout`** — Appelle `AuthService::logout`

### Routes
- [ ] `GET  /login`  → `AuthController::showLogin`
- [ ] `POST /login`  → `AuthController::handleLogin`
- [ ] `GET  /logout` → `AuthController::handleLogout`

### Vues
- [ ] **`layout.php`** — Template de base : navbar avec liens dynamiques selon le rôle, inclusion Bootstrap
- [ ] **`auth/login.php`** — Formulaire avec champs `username` + `password`, affichage du message d'erreur si échec

---

##  MODULE 2 — Saisie des votes
> Réservé à l'admin — permet d'entrer les voix par état

### Back
- [ ] **`VoteRepository::getVotesByState`** — Retourne les votes existants d'un état pour une élection (pour pré-remplir le formulaire)
- [ ] **`VoteRepository::upsertVote`** — Insère ou met à jour un vote (`INSERT ... ON DUPLICATE KEY UPDATE`)
- [ ] **`VoteService::saveVoteForState`** — Valide les données (entiers positifs, état existant, candidats valides) puis appelle `upsertVote` pour chaque candidat
- [ ] **`VoteController::showSaisie`** — Charge la liste des états + les votes existants et affiche le formulaire
- [ ] **`VoteController::handleSaisie`** — Reçoit le POST, appelle `VoteService::saveVoteForState`, redirige avec message de succès ou d'erreur

### Routes
- [ ] `GET  /saisie` → `VoteController::showSaisie` *(admin)*
- [ ] `POST /saisie` → `VoteController::handleSaisie` *(admin)*

### Vues
- [ ] **`votes/saisie.php`** — Select liste des états + 2 champs numériques (Biden / Trump) + bouton Valider, message flash de confirmation/erreur

---

## MODULE 3 — Statistiques (pourcentages)
> Accessible à tous les utilisateurs connectés

### Back
- [ ] **`VoteRepository::getVotesByElection`** — Retourne tous les votes d'une élection groupés par état
- [ ] **`VoteService::computePercentages`** — Pour chaque état, calcule `(votes_candidat / total_votes_etat) * 100` et retourne le résultat formaté
- [ ] **`VoteController::showTableau`** — Appelle `computePercentages` et passe les données à la vue

### Routes
- [ ] `GET /tableau` → `VoteController::showTableau` *(connecté)*

### Vues
- [ ] **`votes/tableau.php`** — Tableau avec colonnes : État / Biden % / Trump %, lien "Voir résultats" en bas

---

##  MODULE 4 — Résultats globaux
> Accessible à tous — affiche le gagnant final

### Back
- [ ] **`ResultRepository::getElectionResults`** — Interroge la vue SQL `election_results` (grands électeurs par candidat)
- [ ] **`ResultRepository::getElectionWinner`** — Interroge la vue SQL `election_winner`
- [ ] **`ResultService::getSummaryByElection`** — Retourne : liste candidats + leurs grands électeurs + le vainqueur
- [ ] **`ResultController::showResults`** — Appelle `getSummaryByElection` et passe les données à la vue

### Routes
- [ ] `GET /resultats` → `ResultController::showResults` *(connecté)*

### Vues
- [ ] **`results/resultats.php`** — Tableau : Candidat / Nb grands électeurs, mention "Vainqueur : XXX" en bas, bouton "Exporter en PDF"

---

## MODULE 5 — Export PDF
> Déclenché depuis la page résultats

### Back
- [ ] **Installer dompdf** — `composer require dompdf/dompdf`
- [ ] **`PdfService::exportResultsPDF`** — Génère un PDF à partir des données de `ResultService::getSummaryByElection` et déclenche le téléchargement
- [ ] **`ResultController::exportPDF`** — Appelle `PdfService::exportResultsPDF`

### Routes
- [ ] `GET /resultats/pdf` → `ResultController::exportPDF` *(connecté)*

### Vues
- [ ] **`results/pdf_template.php`** — Template HTML simple (tableau résultats + vainqueur) utilisé par dompdf pour générer le PDF

---

## MODULE 6 — Carte des résultats
> Visualisation simplifiée — pas besoin de géométrie réelle

### Back
- [ ] **`ResultRepository::getStateWinners`** — Interroge la vue `state_winners` : retourne pour chaque état le nom, nb grands électeurs, id candidat gagnant
- [ ] **`ResultService::getMapData`** — Enrichit les données avec la couleur (`blue` pour Biden, `red` pour Trump) par état
- [ ] **`MapController::showMap`** — Appelle `getMapData` et passe les données à la vue
- [ ] **`MapController::getStateDetail`** — Endpoint AJAX : retourne les votes détaillés d'un état en JSON (Biden X voix / Trump Y voix / gagnant)

### Routes
- [ ] `GET /carte`          → `MapController::showMap` *(connecté)*
- [ ] `GET /carte/etat/@id` → `MapController::getStateDetail` *(AJAX, connecté)*

### Vues
- [ ] **`map/carte.php`** — Grille CSS de cases colorées (rouge/bleu/gris si pas de données), chaque case : nom état + nb grands électeurs, clic déclenche popup
- [ ] **JS inline dans `carte.php`** — `fetch('/carte/etat/{id}')` au clic, affiche les détails dans un modal Bootstrap

---

## MODULE 7 — Audit & Historique
> Réservé à l'admin — traçabilité complète des modifications

### BDD
- [ ] **Table `audit_log`** — Créer avec : `id`, `user_id`, `state_id`, `election_id`, `candidate_id`, `old_value`, `new_value`, `changed_at`
- [ ] **Clé étrangère** `audit_log.user_id → users.id`

### Back
- [ ] **`AuditRepository::insert`** — Insère une ligne dans `audit_log`
- [ ] **`AuditRepository::getAll`** — Retourne tout l'historique, trié par date décroissante
- [ ] **`AuditRepository::getByState`** — Filtre l'historique par état
- [ ] **`AuditRepository::getEntryById`** — Retourne une entrée précise (utilisé pour le rollback)
- [ ] **`AuditService::logChange`** — Appelé dans `VoteService::saveVoteForState` : enregistre automatiquement ancienne et nouvelle valeur à chaque modification
- [ ] **`AuditService::getHistoryByState`** — Retourne l'historique formaté pour la vue
- [ ] **`AuditService::rollback`** — Relit l'entrée d'audit et réécrit les votes à l'ancienne valeur via `VoteRepository::upsertVote`
- [ ] **`AuditService::exportHistoryCSV`** — Génère un CSV : date, utilisateur, état, candidat, ancienne valeur, nouvelle valeur
- [ ] **`AuditController::showAudit`** — Affiche tout l'historique
- [ ] **`AuditController::showAuditByState`** — Filtre par état (paramètre GET)
- [ ] **`AuditController::handleRollback`** — Traite le POST rollback, appelle `AuditService::rollback`
- [ ] **`AuditController::exportCSV`** — Déclenche le téléchargement du CSV

### Routes
- [ ] `GET  /audit`          → `AuditController::showAudit` *(admin)*
- [ ] `GET  /audit/etat/@id` → `AuditController::showAuditByState` *(admin)*
- [ ] `POST /audit/rollback` → `AuditController::handleRollback` *(admin)*
- [ ] `GET  /audit/export`   → `AuditController::exportCSV` *(admin)*

### Vues
- [ ] **`audit/historique.php`** — Tableau : date / utilisateur / état / candidat / ancienne valeur / nouvelle valeur + bouton "Annuler" (rollback) par ligne + bouton "Exporter CSV"

---

##  MODULE 8 — Finalisation & Rendu

- [ ] **Tester le flux admin complet** — Login → saisie → tableau % → résultats → PDF → carte → audit → rollback
- [ ] **Tester le flux observer** — Vérifier que `/saisie` et `/audit` sont bloqués
- [ ] **`schema.sql` final propre** — Toutes les tables + données de base (états, candidats, users de test)
- [ ] **Créer le ZIP de rendu** — Code source + `schema.sql`
- [ ] **Rédiger le PDF de documentation** — Une capture d'écran par écran, description de chaque étape

---
