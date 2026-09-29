# EcoGarden API

API REST développée avec Symfony permettant de fournir des conseils de jardinage écologique adaptés au mois en cours, ainsi que la météo locale de l'utilisateur en fonction de son code INSEE.

## Sommaire

- [Fonctionnalités](#fonctionnalités)
- [Prérequis](#prérequis)
- [Installation](#installation)
- [Configuration](#configuration)
- [Base de données](#base-de-données)
- [Lancer le projet](#lancer-le-projet)
- [Authentification](#authentification)
- [Endpoints de l'API](#endpoints-de-lapi)
- [Modèle de données](#modèle-de-données)
- [Comptes de démonstration](#comptes-de-démonstration)

## Fonctionnalités

- Inscription et gestion des utilisateurs (email + code INSEE).
- Authentification par JWT.
- Consultation des conseils de jardinage du mois en cours ou d'un mois donné.
- Gestion (création, modification, suppression) des conseils réservée aux administrateurs.
- Consultation de la météo locale (celle de l'utilisateur connecté ou par code INSEE), avec mise en cache.
- Validation du code INSEE via l'API cadastre IGN.

## Prérequis

- PHP >= 8.2
- Composer
- Symfony CLI (recommandé)
- Une base de données (MySQL/MariaDB/PostgreSQL selon votre configuration `DATABASE_URL`)
- Extensions PHP : `ctype`, `iconv`

## Installation

```bash
composer install
```

## Configuration

Créer un fichier `.env.local` (non versionné) à la racine du projet pour surcharger les variables suivantes :

```env
APP_ENV=dev
APP_SECRET=change-me

# Connexion à la base de données
DATABASE_URL="mysql://app:password@127.0.0.1:3306/ecogarden?serverVersion=8.0.32&charset=utf8mb4"

# Clés JWT (voir génération ci-dessous)
JWT_SECRET_KEY=%kernel.project_dir%/config/jwt/private.pem
JWT_PUBLIC_KEY=%kernel.project_dir%/config/jwt/public.pem
JWT_PASSPHRASE=votre_passphrase

# Jeton de l'API météo (api.meteo-concept.com)
API_METEO_TOKEN=votre_token_meteo
```

### Génération des clés JWT

```bash
php bin/console lexik:jwt:generate-keypair
```

## Base de données

```bash
# Création de la base
php bin/console doctrine:database:create

# Exécution des migrations
php bin/console doctrine:migrations:migrate

# (optionnel) Chargement des fixtures de démonstration
php bin/console doctrine:fixtures:load
```

## Lancer le projet

```bash
symfony server:start
# ou
php -S localhost:8000 -t public
```

## Authentification

L'API utilise l'authentification par JWT (`lexik/jwt-authentication-bundle`).

1. Créer un compte via `POST /api/user` (accès public).
2. Obtenir un token via `POST /api/auth` avec l'email et le mot de passe.
3. Transmettre le token dans l'en-tête `Authorization` sur les routes protégées :

```
Authorization: Bearer <votre_token>
```

Toutes les routes sous `/api` nécessitent une authentification, à l'exception de `POST /api/auth` et `POST /api/user`.

### Obtenir un token

```http
POST /api/auth
Content-Type: application/json

{
  "email": "user@ecogarden.com",
  "password": "password"
}
```

## Endpoints de l'API

### Authentification

| Méthode | Route         | Accès  | Description                      |
|---------|---------------|--------|-----------------------------------|
| POST    | `/api/auth`   | Public | Connexion et récupération du JWT |

### Utilisateurs

| Méthode | Route             | Accès          | Description                              |
|---------|-------------------|----------------|-------------------------------------------|
| POST    | `/api/user`       | Public         | Inscription d'un nouvel utilisateur       |
| PUT     | `/api/user/{id}`  | `ROLE_ADMIN`   | Mise à jour d'un utilisateur              |
| DELETE  | `/api/user/{id}`  | `ROLE_ADMIN`   | Suppression d'un utilisateur              |

### Conseils (Tips)

| Méthode | Route                       | Accès          | Description                                             |
|---------|------------------------------|----------------|----------------------------------------------------------|
| GET     | `/api/tips`                 | Authentifié    | Liste des conseils du mois en cours                      |
| GET     | `/api/tips/{monthNumber}`   | Authentifié    | Liste des conseils pour un mois donné (1 à 12)           |
| POST    | `/api/tips`                 | `ROLE_ADMIN`   | Création d'un conseil                                    |
| PUT     | `/api/tips/{id}`            | `ROLE_ADMIN`   | Mise à jour d'un conseil                                 |
| DELETE  | `/api/tips/{id}`            | `ROLE_ADMIN`   | Suppression d'un conseil                                 |

Exemple de corps de requête pour la création d'un conseil :

```json
{
  "title": "Arroser tôt le matin",
  "text": "Arrosez vos plantes tôt le matin pour limiter l'évaporation.",
  "months": [6, 7, 8]
}
```

### Météo

| Méthode | Route                     | Accès          | Description                                              |
|---------|----------------------------|----------------|-------------------------------------------------------------|
| GET     | `/api/weather`            | Authentifié    | Météo pour le code INSEE de l'utilisateur connecté        |
| GET     | `/api/weather/{insee}`    | Authentifié    | Météo pour un code INSEE donné                             |

Les résultats météo sont mis en cache 15 minutes par code INSEE.

## Modèle de données

- **User** : `id`, `email`, `password`, `roles` (`ROLE_USER`, `ROLE_ADMIN`), `insee` (code INSEE de la commune).
- **Tip** : `id`, `title`, `text`, `months` (relation many-to-many avec `Month`).
- **Month** : `id`, `number` (1-12), `name`, `tips` (relation many-to-many avec `Tip`).

## Comptes de démonstration

Après chargement des fixtures (`php bin/console doctrine:fixtures:load`) :

| Email                  | Mot de passe | Rôle          |
|-------------------------|--------------|---------------|
| user@ecogarden.com      | Password123!     | `ROLE_USER`   |
| admin@ecogarden.com     | Password123!     | `ROLE_ADMIN`  |
