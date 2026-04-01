-- =========================
-- RESET
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
-- STATES (50 États US - 2020)
-- =========================
INSERT INTO states (id, name, electoral_votes, population) VALUES
(1, 'Alabama', 9, 4900000),
(2, 'Alaska', 3, 730000),
(3, 'Arizona', 11, 7200000),
(4, 'Arkansas', 6, 3000000),
(5, 'California', 55, 39500000),
(6, 'Colorado', 9, 5800000),
(7, 'Connecticut', 7, 3600000),
(8, 'Delaware', 3, 990000),
(9, 'Florida', 29, 21500000),
(10, 'Georgia', 16, 10700000),
(11, 'Hawaii', 4, 1400000),
(12, 'Idaho', 4, 1800000),
(13, 'Illinois', 20, 12600000),
(14, 'Indiana', 11, 6700000),
(15, 'Iowa', 6, 3200000),
(16, 'Kansas', 6, 2900000),
(17, 'Kentucky', 8, 4500000),
(18, 'Louisiana', 8, 4600000),
(19, 'Maine', 4, 1300000),
(20, 'Maryland', 10, 6000000),
(21, 'Massachusetts', 11, 6900000),
(22, 'Michigan', 16, 10000000),
(23, 'Minnesota', 10, 5600000),
(24, 'Mississippi', 6, 3000000),
(25, 'Missouri', 10, 6100000),
(26, 'Montana', 3, 1100000),
(27, 'Nebraska', 5, 1900000),
(28, 'Nevada', 6, 3100000),
(29, 'New Hampshire', 4, 1400000),
(30, 'New Jersey', 14, 8900000),
(31, 'New Mexico', 5, 2100000),
(32, 'New York', 29, 19500000),
(33, 'North Carolina', 15, 10400000),
(34, 'North Dakota', 3, 760000),
(35, 'Ohio', 18, 11700000),
(36, 'Oklahoma', 7, 4000000),
(37, 'Oregon', 7, 4200000),
(38, 'Pennsylvania', 20, 12800000),
(39, 'Rhode Island', 4, 1100000),
(40, 'South Carolina', 9, 5100000),
(41, 'South Dakota', 3, 880000),
(42, 'Tennessee', 11, 6900000),
(43, 'Texas', 38, 29000000),
(44, 'Utah', 6, 3200000),
(45, 'Vermont', 3, 620000),
(46, 'Virginia', 13, 8600000),
(47, 'Washington', 12, 7700000),
(48, 'West Virginia', 5, 1800000),
(49, 'Wisconsin', 10, 5800000),
(50, 'Wyoming', 3, 580000);

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
-- VOTES (simplifiés mais cohérents)
-- =========================
-- ⚠️ Logique :
-- - États démocrates → Biden gagne
-- - États républicains → Trump gagne
-- - Swing states → résultats serrés

INSERT INTO votes (state_id, candidate_id, election_id, popular_votes) VALUES

-- Exemple sur plusieurs états clés

-- California (Biden)
(5, 1, 1, 11100000),
(5, 2, 1, 6000000),

-- Texas (Trump)
(43, 1, 1, 5300000),
(43, 2, 1, 5900000),

-- Florida (Trump serré)
(9, 1, 1, 5300000),
(9, 2, 1, 5700000),

-- New York (Biden)
(32, 1, 1, 5200000),
(32, 2, 1, 3200000),

-- Pennsylvania (Biden serré)
(38, 1, 1, 3400000),
(38, 2, 1, 3300000),

-- Georgia (Biden très serré)
(10, 1, 1, 2470000),
(10, 2, 1, 2450000),

-- Michigan (Biden)
(22, 1, 1, 2800000),
(22, 2, 1, 2650000),

-- Arizona (Biden serré)
(3, 1, 1, 1670000),
(3, 2, 1, 1660000),

-- Ohio (Trump)
(35, 1, 1, 2700000),
(35, 2, 1, 3100000),

-- North Carolina (Trump serré)
(33, 1, 1, 2650000),
(33, 2, 1, 2750000);
