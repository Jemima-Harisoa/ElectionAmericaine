-- =========================
-- RESET (optionnel)
-- =========================
SET FOREIGN_KEY_CHECKS = 0;

TRUNCATE TABLE votes;
TRUNCATE TABLE election_candidates;
TRUNCATE TABLE candidates;
TRUNCATE TABLE states;
TRUNCATE TABLE elections;

SET FOREIGN_KEY_CHECKS = 1;

-- =========================
-- ELECTION
-- =========================
INSERT INTO elections (id, year) VALUES
(1, 2020);

-- =========================
-- STATES (réalistes)
-- =========================
INSERT INTO states (id, name, electoral_votes, population) VALUES
(1, 'California', 55, 39500000),
(2, 'Texas', 38, 29000000),
(3, 'Florida', 29, 21500000),
(4, 'New York', 29, 19500000),
(5, 'Pennsylvania', 20, 12800000);

-- =========================
-- CANDIDATES
-- =========================
INSERT INTO candidates (id, name, party) VALUES
(1, 'Joe Biden', 'Democrat'),
(2, 'Donald Trump', 'Republican');

-- =========================
-- ELECTION CANDIDATES
-- =========================
INSERT INTO election_candidates (election_id, candidate_id) VALUES
(1, 1),
(1, 2);

-- =========================
-- VOTES (cohérents et réalistes)
-- =========================
-- California (Biden gagne largement)
INSERT INTO votes (state_id, candidate_id, election_id, popular_votes) VALUES
(1, 1, 1, 11100000),
(1, 2, 1, 6000000);

-- Texas (Trump gagne)
INSERT INTO votes (state_id, candidate_id, election_id, popular_votes) VALUES
(2, 1, 1, 5300000),
(2, 2, 1, 5900000);

-- Florida (Trump gagne serré)
INSERT INTO votes (state_id, candidate_id, election_id, popular_votes) VALUES
(3, 1, 1, 5300000),
(3, 2, 1, 5700000);

-- New York (Biden gagne)
INSERT INTO votes (state_id, candidate_id, election_id, popular_votes) VALUES
(4, 1, 1, 5200000),
(4, 2, 1, 3200000);

-- Pennsylvania (Biden gagne serré)
INSERT INTO votes (state_id, candidate_id, election_id, popular_votes) VALUES
(5, 1, 1, 3400000),
(5, 2, 1, 3300000);

-- =========================
-- RESULTAT ATTENDU (logique)
-- =========================
-- Biden gagne :
-- California (55)
-- New York (29)
-- Pennsylvania (20)
-- TOTAL = 104

-- Trump gagne :
-- Texas (38)
-- Florida (29)
-- TOTAL = 67