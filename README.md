# Projet PRWB 2324 - Groupe a04 - Google Keep

## Description
Ce projet est une réplique simplifiée de Google Keep, réalisée dans le cadre d'un exercice avec un framework PHP fourni par l'école. Il permet de créer, modifier et gérer des notes, en utilisant une base de données MariaDB.

### Liste des utilisateurs et mots de passes

  * boverhaegen@epfc.eu, password "Password1,", utilisateur
  * bepenelle@epfc.eu, password "Password1,", utilisateur
  * xapigeolet@epfc.eu, password "Password1,", utilisateur
  * mamichel@epfc.eu, password "Password1,", utilisateur
  
### Liste des fonctionnalités supplémentaires
    
    * Ajout d'une icône label en bas de note_card à côté des pastilles Labels, pour mener directement à la page d'édition de Labels
    
## Fonctionnalités

- **Création de notes** : Les utilisateurs peuvent créer des notes avec un titre et du contenu.
- **Création de compte** : Les utilisateurs peuvent créer un compte pour se connecter avec.
- **Modification de mail** : Les utilisateurs peuvent modifier leurs mail.
- **Modification de notes** : Les notes existantes peuvent être modifiées.
- **Suppression de notes** : Les notes peuvent être supprimées une fois qu'elles ne sont plus nécessaires.
- **Recherche  de notes** : Les notes peuvent être rechercher par tags.
- **Partage de notes** : Les notes peuvent être partagé entre les differents utilisateurs.
- **Déplacer une note** : Les notes peuvent être déplacé.
- **Gestion des bases de données** : Les notes sont stockées et récupérées dans une base de données MariaDB.

## Captures d'écran

1. **Page principale des notes**  
   La première capture montre la page principale de l'application où toutes les notes sont affichées, triées de la plus récente à la plus ancienne.
<img width="1444" alt="Capture d’écran 2024-09-14 à 17 12 41" src="https://github.com/user-attachments/assets/82349115-555a-49a2-95d6-32ece9fecc75">

2. **Onglet de recherche**  
   Sur la deuxième capture, on peut voir l'onglet de recherche qui permet de filtrer les notes en fonction des tags. Cela facilite l'organisation et la recherche des notes spécifiques.
<img width="1444" alt="Capture d’écran 2024-09-14 à 17 16 42" src="https://github.com/user-attachments/assets/fe045304-012b-4afe-835c-fda3ca120a2f">

3. **Détails d'une note**  
   La troisième capture montre l'affichage détaillé d'une note après avoir cliqué dessus. En fonction des autorisations de l'utilisateur, il est possible de partager la note, de la pin/unpin, de l'archiver/désarchiver, d'ajouter des tags ou de la modifier.
<img width="1444" alt="Capture d’écran 2024-09-14 à 17 17 20" src="https://github.com/user-attachments/assets/5de266fb-b08c-4237-a635-5a1f9e286322">

4. **Ajout d'une note**  
   Ces 2 captures montrent l'interface d'ajout d'une nouvelle text note et checklist notes.
<img width="1444" alt="Capture d’écran 2024-09-14 à 17 18 29" src="https://github.com/user-attachments/assets/0bf93691-cde1-456c-8da7-fb0b716d11c4">
<img width="1444" alt="Capture d’écran 2024-09-14 à 17 18 12" src="https://github.com/user-attachments/assets/fd3ea5ea-efe4-46ae-b9ba-f773c380dac3">

5. **Menu hamburger**  
   La dernière capture montre le menu hamburger ouvert, qui offre des options supplémentaires pour naviguer dans l'application.
<img width="1444" alt="Capture d’écran 2024-09-14 à 17 13 03" src="https://github.com/user-attachments/assets/238b8f76-559c-461c-9731-e26063fcef17">

## Technologies utilisées

- **PHP** : Utilisé avec un framework spécifique de l'école pour gérer le backend et les interactions avec la base de données.
- **MariaDB** : Base de données relationnelle utilisée pour stocker les informations des notes.
- **HTML5 & CSS3** : Pour la structure et le style de base de l'application.
- **Bootstrap** : Pour rendre l'application responsive et visuellement attrayante.
- **JavaScript & jQuery** : Pour gérer les interactions dynamiques sur la page (création, modification, suppression des notes).

## Structure du projet

- **config/** : Contient les fichiers de configuration pour l'application, notamment la connexion à la base de données.
- **controller/** : Gère la logique métier du projet, y compris les interactions entre les modèles et les vues.
- **css/** : Contient les fichiers CSS pour le style de l'interface utilisateur.
- **database/** : Gère les interactions avec la base de données.
- **framework/** : Contient le framework de l'école utilisé pour ce projet.
- **js/** : Contient les fichiers JavaScript et jQuery pour les fonctionnalités dynamiques.
- **lib/** : Bibliothèques utilisées dans le projet.
- **model/** : Définit la structure des données, y compris les requêtes à la base de données.
- **view/** : Contient les vues (fichiers HTML) utilisées pour l'interface utilisateur.
- **index.php** : Point d'entrée principal de l'application.
- **.htaccess** : Fichier de configuration du serveur Apache pour la réécriture d'URL et autres réglages.

