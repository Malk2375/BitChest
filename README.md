# BitChest

# BitChest - Système de Gestion de Portefeuille de Cryptomonnaies

BitChest est une application web conçue pour gérer les portefeuilles de cryptomonnaies. Ce README fournit un aperçu des spécifications et des fonctionnalités du projet.

## Présentation du Projet

Le produit est une application web accessible via un navigateur web et disponible en anglais. Elle est conçue pour s'adapter à divers appareils, notamment les mobiles, les tablettes et les ordinateurs de bureau, en mode paysage et portrait.

### Administrateurs

Les administrateurs, travaillant aux côtés de Jérôme, sont responsables de la gestion quotidienne du site. Une fois authentifiés, les administrateurs peuvent effectuer les actions suivantes :

- Modifier les données personnelles
- Gérer les clients
  - Créer, afficher, modifier et supprimer des utilisateurs
  - Les mots de passe ne peuvent pas être visualisés ou modifiés directement
  - Lors de la création d'un nouvel utilisateur, un mot de passe temporaire est généré et présenté à l'administrateur, qui peut ensuite l'envoyer à l'utilisateur
- Consulter les prix des cryptomonnaies
  - Afficher la liste des cryptomonnaies et leurs prix actuels

### Clients

Les clients de BitChest ont accès à leur interface d'administration privée, où ils peuvent effectuer les actions suivantes :

- Gérer les données personnelles
  - Afficher et modifier les informations personnelles
- Gérer le portefeuille
  - Afficher le contenu du portefeuille
  - Afficher le solde en euros (toujours visible)
  - Vendre des cryptomonnaies
- Consulter les prix des cryptomonnaies
  - Afficher la liste des cryptomonnaies et leurs prix actuels
- Visualiser la courbe de progression des prix de chaque cryptomonnaie
- Acheter une quantité de cryptomonnaies au prix actuel

### Portefeuille

Chaque client possède un portefeuille privé contenant toutes ses achats de cryptomonnaies. Le client peut consulter :

- La liste des cryptomonnaies qu'il possède
- Pour chaque cryptomonnaie, les différents achats effectués (date, quantité et prix)
- Pour chaque cryptomonnaie, la valeur d'achat d'une unité
- Pour chaque cryptomonnaie, le profit ou la perte actuelle (gain en cas de vente totale)

### Vente

Lorsqu'un client décide de vendre, il reçoit la valeur de son achat en euros. Les calculs de valeur d'achat et de profit/perte doivent tenir compte du solde de cryptomonnaie actualisé.

### Cryptomonnaies prises en charge

BitChest prend en charge les 10 cryptomonnaies suivantes lors du lancement :

1. Bitcoin
2. Ethereum
3. Ripple
4. Bitcoin Cash
5. Cardano
6. Litecoin
7. NEM
8. Stellar
9. IOTA
10. Dash

Pendant la phase de prototypage, les cotations des cryptomonnaies pour les 30 derniers jours seront générées à l'aide de la fonction `cotation_generator.php` fournie. De plus, la première cotation sera générée à l'aide d'une autre fonction dans le même document. Les prix des cryptomonnaies ne peuvent pas être négatifs.

### Représentation Graphique

Pour visualiser l'évolution du prix d'une cryptomonnaie, une représentation graphique doit être affichée en utilisant la solution JavaScript la plus appropriée.

## Démarrage

Pour commencer à utiliser BitChest, suivez ces étapes :

1. Clonez le dépôt.
2. Installez les éventuelles dépendances nécessaires.
3. Configurez la base de données selon le schéma fourni.
4. Configurez les paramètres de l'application.
5. Lancez l'application.

## Contributeurs

- [Votre Nom](https://github.com/votreusername)

## Licence

Ce projet est sous licence [MIT License](LICENSE).
