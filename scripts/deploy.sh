#!/bin/bash
# KKS Store Deployment Script
# Usage: bash scripts/deploy.sh [environment] [branch]
# Example: bash scripts/deploy.sh production main

set -e

ENVIRONMENT=${1:-production}
BRANCH=${2:-main}
APP_PATH="/path/to/kks_store"  # ← CHANGE THIS TO YOUR SERVER PATH

echo "🚀 KKS Store Deployment Script"
echo "Environment: $ENVIRONMENT"
echo "Branch: $BRANCH"
echo "Path: $APP_PATH"
echo ""

# Color codes
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

check_command() {
    if ! command -v $1 &> /dev/null; then
        echo -e "${RED}✗ $1 not found${NC}"
        exit 1
    fi
    echo -e "${GREEN}✓ $1 found${NC}"
}

echo "📋 Checking prerequisites..."
check_command git
check_command php
check_command composer

echo ""
echo "📁 Deploying from $APP_PATH..."

if [ ! -d "$APP_PATH" ]; then
    echo -e "${RED}✗ Directory not found: $APP_PATH${NC}"
    exit 1
fi

cd "$APP_PATH"

echo ""
echo "🔄 Pulling latest code..."
git fetch origin
git checkout $BRANCH
git pull origin $BRANCH

echo ""
echo "📦 Installing dependencies..."
composer install --no-dev --optimize-autoloader

echo ""
echo "🔐 Checking .env file..."
if [ ! -f .env ]; then
    echo -e "${YELLOW}⚠ .env not found. Creating from .env.production.example...${NC}"
    cp .env.production.example .env
    echo -e "${YELLOW}⚠ IMPORTANT: Edit .env with your production credentials!${NC}"
    exit 1
fi

echo ""
echo "🗄️  Running database migrations..."
php artisan migrate --force

echo ""
echo "💾 Setting up storage..."
php artisan storage:link 2>/dev/null || true
chmod -R ug+rwx storage bootstrap/cache

echo ""
echo "⚡ Optimizing application..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo ""
echo "🧹 Clearing temporary files..."
php artisan cache:clear
php artisan view:clear

echo ""
echo -e "${GREEN}✅ Deployment complete!${NC}"
echo ""
echo "📝 Next steps:"
echo "  1. Verify .env configuration"
echo "  2. Test webhook endpoints: https://your-domain.com/webhooks/khpay"
echo "  3. Run payment gateway tests"
echo "  4. Check error logs: tail -f storage/logs/laravel.log"
echo ""
