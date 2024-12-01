Projet Symfony de Loïc STEBACH | Groupe 2

-----------------------------------------

Attention : Il vous faut PHP 8.2 pour faire fonctionner ce projet si vous avez PHP 8.1 utiliser la branche symfony64 du repository.

## Initialisation de l'IDE

### Visual Studio Code

1. Ouvrir le projet dans Visual Studio Code
2. Installer les extensions pour PHP, Twig et Symfony
    - Aller dans l'onglet Extensions
    - Installer les extensions (whatwedo.twig, TheNouillet.symfony-vscode, DEVSENSE.phptools-vscode, 
    bmewburn.vscode-intelephense-client, zobo.php-intellisense)

## Installation

1. Cloner le projet
2. Installer PHP >= 8.2 et Composer (Sur votre machine utiliser XAMPP pour windows, MAMP pour mac ou LAMP pour linux bien prendre la version PHP 8.2)
3. Installer les dépendances du projet avec la commande `composer install`
4. Faire un virtual host sur votre serveur local (XAMPP par exemple pour Windows) 
 - Ouvrir le fichier `httpd-vhosts.conf` dans le répertoire `C:\xampp\apache\conf\extra`
    - Ajouter le code suivant à la fin du fichier
    ```
    <VirtualHost *>
        DocumentRoot "C:\Users\votre_username\Documents\iut\symfony_base\public"
        ServerName symfony_base.local
        
        <Directory "C:\Users\votre_username\Documents\iut\symfony_base\public">
            AllowOverride All
            Require all granted
        </Directory>
    </VirtualHost>
    ```
    - Ajouter l'adresse IP de votre machine dans le fichier `C:\Windows\System32\drivers\etc\hosts`
    ```
    127.0.0.1 symfony_base.local
    ```
    - Redémarrer Apache
    - Accéder à l'adresse `symfony_base.local` dans votre navigateur

4. Créer un fichier `.env.local` à la racine du projet et ajouter la configuration de la base de données
5. Créer la base de données avec la commande `php bin/console doctrine:database:create`

## Utilisation

- N'hésitez pas à consulter la documentation de Symfony pour plus d'informations sur l'utilisation du framework : https://symfony.com/doc/current/index.html

## Initialisation du projet

Maintenant que je projet, les dépendances et la base sont instalés, il faut dans un terminal lancer les commandes suivantes :

1. `php bin/console doctrine:migrations:diff` pour créer un fichier de migration

2. `php bin/console doctrine:migrations:migrate` pour ensuite envoyer le schéma des tables dans la base

3. `php bin/console doctrine:fixtures:load` pour ensuite charger les données des fixtures dans la base

Et vous voila donc avec un projet fonctionnel.

## Quelques données

- La page principale du projet est `http://symfony_base.local/home`

- Voici quelques identifiants de compte stockés dans ma base :

admin: 
    mail: loic.@gmail.com
    mot de passe: loic57350

user1: 
    mail: user1@example.com
    mot de passe: userpassword1

user2: 
    mail: user2@example.com
    mot de passe: userpassword2

10 utilisateurs sont disponibles, il faut seulement adapter les identifiants en fonction du numéro de l'utilisateur



