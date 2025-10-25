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
- Créer les volumes optimisés pour `vendor` et `var`

### 2. Installer les dépendances Composer

**Sur Windows, utilisez cette méthode optimisée pour éviter les timeouts :**

```bash
# Installer avec un conteneur Composer dédié (plus rapide)
docker run --rm -v "$(pwd):/app" -w /app -e COMPOSER_PROCESS_TIMEOUT=0 composer:2.2 composer install --ignore-platform-reqs --no-scripts --prefer-source

# Copier vendor dans le volume Docker (pour les performances)
docker cp ./vendor/. worldle_php:/var/www/vendor/

# Configurer les permissions
docker-compose exec php chown -R www-data:www-data /var/www/var

# Redémarrer PHP
docker-compose restart php
```

**Alternative (si vous avez déjà vendor installé localement) :**

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
- **MySQL** : localhost:3307 (⚠️ port modifié pour éviter les conflits)
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

## Optimisations de performance (Windows)

### Volumes nommés pour vendor et var

Pour améliorer drastiquement les performances sur Docker Desktop Windows, les dossiers `vendor` et `var` sont stockés dans des volumes Docker nommés au lieu d'être montés depuis Windows.

**Avantages :**
- ⚡ Chargement des pages **10x plus rapide**
- 🚀 Pas de timeout lors de l'installation Composer
- 💾 Cache Symfony optimisé

**Configuration dans `docker-compose.yml` :**
```yaml
volumes:
  - ./:/var/www                    # Code source monté depuis Windows
  - vendor-data:/var/www/vendor    # Vendor dans un volume Docker (rapide)
  - var-data:/var/www/var          # Cache dans un volume Docker (rapide)
```

### Première installation

Lors de la première installation, il faut copier `vendor` dans le volume :

```bash
# Après avoir installé les dépendances localement
docker cp ./vendor/. worldle_php:/var/www/vendor/
docker-compose exec php chown -R www-data:www-data /var/www/var
docker-compose restart php
```

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

# Ne pas oublier de recopier vendor après un rebuild avec -v
docker cp ./vendor/. worldle_php:/var/www/vendor/
docker-compose exec php chown -R www-data:www-data /var/www/var
```

### Réinitialiser les tentatives de jeu

```bash
# Supprimer toutes les tentatives
docker-compose exec php php bin/console doctrine:query:sql "DELETE FROM attempt"

# Vider le cache
docker-compose exec php php bin/console cache:clear
```

### Problème "Unable to create cache directory"

```bash
docker-compose exec php chown -R www-data:www-data /var/www/var
docker-compose exec php php bin/console cache:clear
```
