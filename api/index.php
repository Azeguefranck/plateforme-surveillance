<?php
/**
 * Vercel Serverless Entry Point for Laravel
 *
 * Ce fichier est le point d'entrée pour Vercel (runtime vercel-php).
 * Il redirige toutes les requêtes vers public/index.php de Laravel.
 *
 * PRÉREQUIS VERCEL :
 *  1. Base de données MySQL externe (PlanetScale, Railway, Supabase)
 *  2. Variables d'environnement configurées dans le tableau de bord Vercel
 *  3. SESSION_DRIVER=cookie (pas de filesystem)
 *  4. CACHE_STORE=array (pas de filesystem persistant)
 *  5. FILESYSTEM_DISK=local (uploads non persistants → utiliser S3)
 *
 * DÉPLOIEMENT :
 *  1. Connecter le dépôt GitHub à Vercel
 *  2. Ajouter les variables d'environnement dans Settings → Environment Variables
 *  3. Générer APP_KEY : php artisan key:generate --show
 *  4. Push sur la branche main → déploiement automatique
 */

// Définir le chemin public Laravel
define('LARAVEL_ROOT', dirname(__DIR__));

// Changer le répertoire de travail vers la racine Laravel
chdir(LARAVEL_ROOT);

// Charger l'index public de Laravel
require LARAVEL_ROOT . '/public/index.php';
