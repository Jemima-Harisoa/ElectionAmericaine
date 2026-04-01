-- Table d'audit pour tracer les modifications de votes
CREATE TABLE IF NOT EXISTS audit_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    state_id INT NOT NULL,
    election_id INT NOT NULL,
    candidate_id INT NOT NULL,
    old_value INT,
    new_value INT NOT NULL,
    changed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (state_id) REFERENCES states(id) ON DELETE CASCADE,
    FOREIGN KEY (election_id) REFERENCES elections(id) ON DELETE CASCADE,
    FOREIGN KEY (candidate_id) REFERENCES candidates(id) ON DELETE CASCADE,
    INDEX idx_state_election (state_id, election_id),
    INDEX idx_changed_at (changed_at)
);

-- Données de test pour l'audit
-- Historique d'entrées de vote par l'admin sur différents états
INSERT INTO audit_log (user_id, state_id, election_id, candidate_id, old_value, new_value, changed_at) VALUES
-- California (state_id = 5) - Entrée initiale
(1, 5, 1, 1, NULL, 8753788, '2024-01-15 09:30:00'),   -- Biden: NULL -> 8753788
(1, 5, 1, 2, NULL, 3664525, '2024-01-15 09:30:30'),   -- Trump: NULL -> 3664525

-- Texas (state_id = 44) - Entrée initiale
(1, 44, 1, 1, NULL, 5259126, '2024-01-15 10:15:00'),  -- Biden: NULL -> 5259126
(1, 44, 1, 2, NULL, 5890347, '2024-01-15 10:15:30'),  -- Trump: NULL -> 5890347

-- Florida (state_id = 11) - Entrée initiale
(1, 11, 1, 1, NULL, 5297045, '2024-01-15 11:00:00'),  -- Biden: NULL -> 5297045
(1, 11, 1, 2, NULL, 5668731, '2024-01-15 11:00:30'),  -- Trump: NULL -> 5668731

-- Pennsylvania (state_id = 39) - Entrée initiale et modification
(1, 39, 1, 1, NULL, 3458229, '2024-01-16 08:45:00'),  -- Biden: NULL -> 3458229
(1, 39, 1, 2, NULL, 3377674, '2024-01-16 08:45:30'),  -- Trump: NULL -> 3377674
(1, 39, 1, 1, 3458229, 3465000, '2024-01-16 14:20:00'),  -- Biden: 3458229 -> 3465000 (correction)

-- Michigan (state_id = 22) - Entrée initiale
(1, 22, 1, 1, NULL, 2804040, '2024-01-16 09:30:00'),  -- Biden: NULL -> 2804040
(1, 22, 1, 2, NULL, 2649852, '2024-01-16 09:30:30'),  -- Trump: NULL -> 2649852

-- Georgia (state_id = 12) - Entrée initiale et plusieurs modifications
(1, 12, 1, 1, NULL, 2473633, '2024-01-16 10:00:00'),  -- Biden: NULL -> 2473633
(1, 12, 1, 2, NULL, 2458127, '2024-01-16 10:00:30'),  -- Trump: NULL -> 2458127
(1, 12, 1, 1, 2473633, 2474601, '2024-01-16 15:45:00'),  -- Biden: 2473633 -> 2474601 (correction)
(1, 12, 1, 2, 2458127, 2460256, '2024-01-16 15:45:30'),  -- Trump: 2458127 -> 2460256 (correction)

-- Arizona (state_id = 3) - Entrée initiale et modification
(1, 3, 1, 1, NULL, 1672143, '2024-01-17 08:00:00'),   -- Biden: NULL -> 1672143
(1, 3, 1, 2, NULL, 1602248, '2024-01-17 08:00:30'),   -- Trump: NULL -> 1602248
(1, 3, 1, 2, 1602248, 1606164, '2024-01-17 13:30:00'),  -- Trump: 1602248 -> 1606164 (correction)

-- Nevada (state_id = 30) - Entrée initiale
(1, 30, 1, 1, NULL, 703486, '2024-01-17 09:15:00'),   -- Biden: NULL -> 703486
(1, 30, 1, 2, NULL, 669890, '2024-01-17 09:15:30'),   -- Trump: NULL -> 669890

-- Wisconsin (state_id = 51) - Entrée initiale et modification
(1, 51, 1, 1, NULL, 1630866, '2024-01-17 10:30:00'),  -- Biden: NULL -> 1630866
(1, 51, 1, 2, NULL, 1610184, '2024-01-17 10:30:30'),  -- Trump: NULL -> 1610184
(1, 51, 1, 1, 1630866, 1630866, '2024-01-17 16:00:00'),  -- Biden: 1630866 -> 1630866 (vérification)

-- New York (state_id = 33) - Entrée initiale
(1, 33, 1, 1, NULL, 3244798, '2024-01-17 11:45:00'),  -- Biden: NULL -> 3244798
(1, 33, 1, 2, NULL, 1933492, '2024-01-17 11:45:30');  -- Trump: NULL -> 1933492

