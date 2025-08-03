# Application de Gestion de Présence Étudiante

Cette application web permet de gérer les présences des étudiants via une interface sécurisée. Elle propose des fonctionnalités d’authentification, de CRUD sur les étudiants et les présences, et d’affichage de tableaux de bord.

## Fonctionnalités

- Authentification de l’administrateur
- Ajout, modification et suppression d'étudiants
- Enregistrement et édition des présences
- Tableau de bord avec résumé des présences
- Interface simple en PHP avec HTML/CSS

## Technologies

- PHP 8.x
- MySQL
- HTML/CSS
- Normes de codage PSR-12 partiellement suivies

## Installation

1. Cloner ou extraire ce dépôt sur votre serveur local (XAMPP, WAMP, etc.) :
    ```
    git clone https://votre-repo.git
    ```

2. Créer une base de données MySQL nommée `gestion_presence` et importer le fichier `schema.sql`.

3. Configurer la connexion à la base de données dans le fichier :
    ```
    config/db.php
    ```

4. Lancer l’application depuis le navigateur :
    ```
    http://localhost/gestion-presence-etudiant/gestion-presence-etudiant/dashboard.php
    ```

## Structure du projet

- `index.php` : page de connexion
- `dashboard.php` : tableau de bord
- `etudiants.php`, `ajouter_etudiant.php`, etc. : gestion des étudiants
- `ajouter_presence.php`, etc. : gestion des présences
- `includes/` : en-têtes et pied de page HTML
- `config/db.php` : configuration de la base de données

## Sécurité

- Système de connexion avec vérification via `login.php` et `security.php`
- Prévention partielle contre les accès non autorisés

## Auteur

Projet développé dans le cadre d’un exercice ou projet pédagogique.

## Groupe 13
### Membres:
**RAZAFIMANDIMBY Soatiana Sandrine** (n°33)
**RANDRIAMPARANY Jessi Fanirisoa** (n°42)
