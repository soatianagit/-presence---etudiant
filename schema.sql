-- Création de la base de données
CREATE DATABASE IF NOT EXISTS gestion_presence;
USE gestion_presence;

-- Table des utilisateurs (admin)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(40) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table des étudiants
CREATE TABLE IF NOT EXISTS etudiants (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom_et_prenom VARCHAR(100) NOT NULL,
    matricule VARCHAR(20) NOT NULL UNIQUE,
    niveau VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table des présences
CREATE TABLE IF NOT EXISTS presences (
    id INT AUTO_INCREMENT PRIMARY KEY,
    etudiant_id INT NOT NULL,
    etudiant_name VARCHAR(100) NOT NULL,
    status ENUM('PRESENT', 'ABSENT') NOT NULL,
    date_presence DATE NOT NULL,
    heure_debut TIME NOT NULL,
    heure_fin TIME NOT NULL,
    cours VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (etudiant_id) REFERENCES etudiants(id) ON DELETE CASCADE
);

-- Insertion d'un utilisateur admin par défaut (mot de passe: admin123)
-- Le mot de passe est hashé avec SHA1
INSERT INTO users (username, password) VALUES ('admin', SHA1('admin123'));

-- Insertion de quelques étudiants pour les tests
INSERT INTO etudiants (nom_et_prenom, matricule, niveau) VALUES 
('Dupont Jean', 'ETU001', 'Licence 1'),
('Martin Sophie', 'ETU002', 'Licence 2'),
('Dubois Pierre', 'ETU003', 'Licence 3'),
('Leroy Emma', 'ETU004', 'Master 1'),
('Moreau Thomas', 'ETU005', 'Master 2');

-- Insertion de quelques présences pour les tests
INSERT INTO presences (etudiant_id, etudiant_name, status, date_presence, heure_debut, heure_fin, cours) VALUES 
(1, 'Dupont Jean', 'PRESENT', '2025-05-15', '08:00:00', '10:00:00', 'Mathématiques'),
(2, 'Martin Sophie', 'ABSENT', '2025-05-15', '08:00:00', '10:00:00', 'Mathématiques'),
(3, 'Dubois Pierre', 'PRESENT', '2025-05-15', '08:00:00', '10:00:00', 'Mathématiques'),
(4, 'Leroy Emma', 'PRESENT', '2025-05-15', '10:30:00', '12:30:00', 'Informatique'),
(5, 'Moreau Thomas', 'ABSENT', '2025-05-15', '10:30:00', '12:30:00', 'Informatique');
