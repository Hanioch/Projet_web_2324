# Projet PRWB 2324 - Groupe a04 - Google Keep

## Notes de version itération 3

### Liste des utilisateurs et mots de passes

  * boverhaegen@epfc.eu, password "Password1,", utilisateur
  * bepenelle@epfc.eu, password "Password1,", utilisateur
  * xapigeolet@epfc.eu, password "Password1,", utilisateur
  * mamichel@epfc.eu, password "Password1,", utilisateur
  
### Remarques

	* Nous n'avons finalement rien factorisé dans le modèle MyModel
    * Il y a toujours un bug lié aux accents dans la base de données : si on rajoute un label ou un item par exemple "Prive" alors qu'il existe le label "Privé" en DB, il y a violation de contrainte car les deux caractères "e" et "é" sont considérés identiques.

### Liste des bugs connus
    
	

### Liste des fonctionnalités supplémentaires
    
    * Ajout d'une icône label en bas de note_card à côté des pastilles Labels, pour mener directement à la page d'édition de Labels

### Divers
