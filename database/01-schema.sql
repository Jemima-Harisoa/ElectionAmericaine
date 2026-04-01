-- Schema de base de données pour l'application d'élection américaine
CREATE DATABASE IF NOT EXISTS elections;

USE elections;

-- Table des élections
CREATE TABLE IF NOT EXISTS elections (
    id INT AUTO_INCREMENT PRIMARY KEY,
    year INT NOT NULL UNIQUE
);

-- Tables des zones électorales
CREATE TABLE IF NOT EXISTS states (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    electoral_votes INT NOT NULL CHECK (electoral_votes >= 0),
    population INT DEFAULT 0
);

-- Table des candidats
CREATE TABLE IF NOT EXISTS candidates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    party VARCHAR(50) -- optionnel
);

-- Les candidats par élection
CREATE TABLE IF NOT EXISTS election_candidates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    election_id INT NOT NULL,
    candidate_id INT NOT NULL,

    FOREIGN KEY (election_id) REFERENCES elections(id),
    FOREIGN KEY (candidate_id) REFERENCES candidates(id),

    UNIQUE (election_id, candidate_id)
);

-- Table des votes
CREATE TABLE IF NOT EXISTS votes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    state_id INT NOT NULL,
    candidate_id INT NOT NULL,
    election_id INT NOT NULL,
    popular_votes INT NOT NULL CHECK (popular_votes >= 0),

    FOREIGN KEY (state_id) REFERENCES states(id),
    FOREIGN KEY (candidate_id) REFERENCES candidates(id),
    FOREIGN KEY (election_id) REFERENCES elections(id),

    UNIQUE (state_id, candidate_id, election_id)
);

CREATE INDEX idx_votes_state ON votes(state_id);
CREATE INDEX idx_votes_candidate ON votes(candidate_id);
CREATE INDEX idx_votes_election ON votes(election_id);

-- Trigger pour vérifier que le candidat existe dans l'élection avant d'insérer un vote
DELIMITER $$

CREATE TRIGGER before_vote_insert
BEFORE INSERT ON votes
FOR EACH ROW
BEGIN
    IF NOT EXISTS (
        SELECT 1 FROM election_candidates ec
        WHERE ec.election_id = NEW.election_id
        AND ec.candidate_id = NEW.candidate_id
    ) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Candidate not registered in this election';
    END IF;
END$$

DELIMITER ;

-- Trigger pour vérifier que le total des votes populaires ne dépasse pas la population de l'état
DELIMITER $$

CREATE TRIGGER check_votes_limit
BEFORE INSERT ON votes
FOR EACH ROW
BEGIN
    DECLARE total_votes INT;

    SELECT SUM(popular_votes)
    INTO total_votes
    FROM votes
    WHERE state_id = NEW.state_id
    AND election_id = NEW.election_id;

    IF total_votes + NEW.popular_votes > 
       (SELECT population FROM states WHERE id = NEW.state_id) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Total votes exceed population';
    END IF;
END$$

DELIMITER ;

-- vue potentielle pour les résultats par état
CREATE VIEW state_winners AS
SELECT v.state_id, v.election_id, v.candidate_id
FROM votes v
JOIN (
    SELECT state_id, election_id, MAX(popular_votes) as max_votes
    FROM votes
    GROUP BY state_id, election_id
) winners
ON v.state_id = winners.state_id
AND v.election_id = winners.election_id
AND v.popular_votes = winners.max_votes;

-- Total des grands électeurs par candidat
CREATE VIEW election_results AS
SELECT 
    sw.election_id,
    c.name,
    SUM(s.electoral_votes) as total_electors
FROM state_winners sw
JOIN states s ON sw.state_id = s.id
JOIN candidates c ON sw.candidate_id = c.id
GROUP BY sw.election_id, c.id;

-- Vue pour déterminer le gagnant de chaque élection
CREATE VIEW election_winner AS
SELECT *
FROM election_results
WHERE total_electors = (
    SELECT MAX(total_electors)
    FROM election_results er2
    WHERE er2.election_id = election_results.election_id
);