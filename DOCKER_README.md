# Docker Setup pour WordleMonde

## Prérequis

- Docker
- Docker Compose

## Installation et démarrage

### 1. Construire et démarrer les conteneurs

```bash
docker-compose up -d --build
```

Cette commande va :
- Construire l'image PHP avec toutes les dépendances
- Démarrer MySQL 5.7
- Démarrer PHP-FPM
- Démarrer Nginx

### 2. Installer les dépendances Composer (si nécessaire)

```bash
docker-compose exec php composer install
```

### 3. Créer la base de données

```bash
docker-compose exec php php bin/console doctrine:database:create
```

### 4. Exécuter les migrations

```bash
docker-compose exec php php bin/console doctrine:migrations:migrate
```

### 5. Charger les fixtures (données de test)

```bash
docker-compose exec php php bin/console doctrine:fixtures:load
```

## Accès à l'application

- **Application web** : http://localhost:8080
- **MySQL** : localhost:3306
  - Database: `worldle`
  - User: `symfony`
  - Password: `symfony`
  - Root password: `root`

## Commandes utiles

### Voir les logs

```bash
# Tous les services
docker-compose logs -f

# Un service spécifique
docker-compose logs -f php
docker-compose logs -f nginx
docker-compose logs -f database
```

### Arrêter les conteneurs

```bash
docker-compose stop
```

### Arrêter et supprimer les conteneurs

```bash
docker-compose down
```

### Arrêter et supprimer les conteneurs + volumes (⚠️ supprime la base de données)

```bash
docker-compose down -v
```

### Exécuter des commandes Symfony

```bash
# Console Symfony
docker-compose exec php php bin/console [commande]

# Exemples
docker-compose exec php php bin/console cache:clear
docker-compose exec php php bin/console debug:router
```

### Accéder au conteneur PHP

```bash
docker-compose exec php bash
```

### Accéder à MySQL

```bash
docker-compose exec database mysql -u symfony -psymfony worldle
```

## Configuration

Les variables d'environnement peuvent être modifiées dans le fichier `.env` :

```env
MYSQL_DATABASE=worldle
MYSQL_ROOT_PASSWORD=root
MYSQL_USER=symfony
MYSQL_PASSWORD=symfony
```

La configuration de la base de données dans `.env` :

```env
DATABASE_URL="mysql://symfony:symfony@database:3306/worldle?serverVersion=5.7"
```

## Structure Docker

- **Dockerfile** : Image PHP 7.4-FPM avec toutes les extensions nécessaires
- **docker-compose.yml** : Orchestration des services (MySQL, PHP, Nginx)
- **docker/nginx/nginx.conf** : Configuration Nginx pour Symfony
- **.dockerignore** : Fichiers à exclure lors du build

## Troubleshooting

### Erreur de connexion à la base de données

Attendez quelques secondes que MySQL soit complètement démarré :

```bash
docker-compose logs database
```

### Problème de permissions

```bash
docker-compose exec php chown -R www-data:www-data /var/www/var
```

### Rebuild complet

```bash
docker-compose down -v
docker-compose up -d --build --force-recreate
```
