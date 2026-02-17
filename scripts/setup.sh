#!/bin/bash

set -e

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$SCRIPT_DIR"

RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

print_header() {
    echo ""
    echo -e "${BLUE}==============================================${NC}"
    echo -e "${BLUE}   $1${NC}"
    echo -e "${BLUE}==============================================${NC}"
    echo ""
}

print_step() {
    echo -e "${YELLOW}==> Step $1: $2${NC}"
}

print_success() {
    echo -e "${GREEN}✓ $1${NC}"
}

print_info() {
    echo -e "${BLUE}ℹ $1${NC}"
}

print_warning() {
    echo -e "${YELLOW}⚠ $1${NC}"
}

print_error() {
    echo -e "${RED}✗ $1${NC}"
}

wait_for_service() {
    local service=$1
    local max_attempts=30
    local attempt=1
    
    echo -n "Waiting for $service"
    while [ $attempt -le $max_attempts ]; do
        if docker ps --format '{{.Names}}' | grep -q "^${service}$"; then
            echo ""
            return 0
        fi
        echo -n "."
        sleep 2
        attempt=$((attempt + 1))
    done
    echo ""
    return 1
}

cleanup() {
    rm -f /tmp/chainvote_network
}

trap cleanup EXIT

print_header "Onchain Voting - Local Setup Wizard"

echo "This will set up the complete local development environment."
echo ""

read -p "Continue with setup? [y/N]: " -n 1 -r
echo ""
if [[ ! $REPLY =~ ^[Yy]$ ]]; then
    echo "Setup cancelled."
    exit 0
fi

print_step "1" "Copying environment files..."
if [ ! -f application/.env ]; then
    cp -n application/.env.example application/.env 2>/dev/null || true
    print_success "Created application/.env"
else
    print_info "application/.env already exists, skipping"
fi

if [ ! -f serverless-lucid/.env ]; then
    cp -n serverless-lucid/.env.example serverless-lucid/.env 2>/dev/null || true
    print_success "Created serverless-lucid/.env"
else
    print_info "serverless-lucid/.env already exists, skipping"
fi

print_step "2" "Starting Docker services..."
docker compose up -d 2>&1 | grep -v "level=warning" | grep -v "level=info" || true
print_success "Docker services started"

echo ""
echo -n "Waiting for containers to be ready"
sleep 5
for container in chainvote-app chainvote-db chainvote-worker chainvote-lucid chainvote-serverless; do
    while ! docker ps --format '{{.Names}}' | grep -q "^${container}$"; do
        sleep 1
        echo -n "."
    done
    echo -n "."
done
echo ""
print_success "All containers are running"

print_step "3" "Installing composer dependencies..."
if docker exec chainvote-app test -d /var/www/vendor 2>/dev/null; then
    print_info "Vendor directory already exists, skipping composer install"
else
    docker exec chainvote-app bash -c "cd /var/www/html && composer install --ignore-platform-reqs --no-interaction --quiet" 2>/dev/null || true
    print_success "Composer dependencies installed"
fi

print_step "4" "Cardano Network Selection"
echo "Available networks:"
echo "  1) Preview    (cardano-preview.blockfrost.io)"
echo "  2) Preprod   (cardano-preprod.blockfrost.io)"
echo "  3) Mainnet   (cardano-mainnet.blockfrost.io)"
echo ""

read -p "Select network [1]: " network_choice
network_choice=${network_choice:-1}

case $network_choice in
    1) network="preview";;
    2) network="preprod";;
    3) network="mainnet";;
    *) network="preview";;
esac

echo "$network" > /tmp/chainvote_network
print_success "Selected network: $network"

print_step "5" "Blockfrost API Configuration"
case $network in
    preview)
        echo "Enter your Blockfrost Preview Project ID:"
        echo "Get it from: https://blockfrost.io/dashboard"
        read -p "[leave empty for local dev only]: " bf_id
        if [ -n "$bf_id" ]; then
            docker exec chainvote-app bash -c "sed -i 's/BLOCKFROST_PROJECT_ID=.*/BLOCKFROST_PROJECT_ID=$bf_id/' /var/www/html/.env" 2>/dev/null
            docker exec chainvote-serverless sh -c "sed -i 's/BF_PREVIEW_ID=.*/BF_PREVIEW_ID=$bf_id/' /code/.env" 2>/dev/null
            print_success "Blockfrost Preview ID configured"
        else
            print_info "Skipping Blockfrost configuration"
        fi
        docker exec chainvote-app bash -c "sed -i 's|CARDANO_LUCID_NETWORK=.*|CARDANO_LUCID_NETWORK=preview|' /var/www/html/.env" 2>/dev/null
        docker exec chainvote-app bash -c "sed -i 's|blockfrost.io/api/v0|cardano-preview.blockfrost.io/api/v0|' /var/www/html/.env" 2>/dev/null
        docker exec chainvote-app bash -c "sed -i 's|CARDANO_NETWORK=.*|CARDANO_NETWORK=0|' /var/www/html/.env" 2>/dev/null
        ;;
    preprod)
        echo "Enter your Blockfrost Preprod Project ID:"
        echo "Get it from: https://blockfrost.io/dashboard"
        read -p "[leave empty for local dev only]: " bf_id
        if [ -n "$bf_id" ]; then
            docker exec chainvote-app bash -c "sed -i 's/BLOCKFROST_PROJECT_ID=.*/BLOCKFROST_PROJECT_ID=$bf_id/' /var/www/html/.env" 2>/dev/null
            docker exec chainvote-serverless sh -c "sed -i 's/BF_PREPROD_ID=.*/BF_PREPROD_ID=$bf_id/' /code/.env" 2>/dev/null
            print_success "Blockfrost Preprod ID configured"
        else
            print_info "Skipping Blockfrost configuration"
        fi
        docker exec chainvote-app bash -c "sed -i 's|CARDANO_LUCID_NETWORK=.*|CARDANO_LUCID_NETWORK=preprod|' /var/www/html/.env" 2>/dev/null
        docker exec chainvote-app bash -c "sed -i 's|blockfrost.io/api/v0|cardano-preprod.blockfrost.io/api/v0|' /var/www/html/.env" 2>/dev/null
        docker exec chainvote-app bash -c "sed -i 's/CARDANO_NETWORK=.*/CARDANO_NETWORK=0/' /var/www/html/.env" 2>/dev/null
        ;;
    mainnet)
        echo "Enter your Blockfrost Mainnet Project ID:"
        echo "Get it from: https://blockfrost.io/dashboard"
        read -p "[leave empty for local dev only]: " bf_id
        if [ -n "$bf_id" ]; then
            docker exec chainvote-app bash -c "sed -i 's/BLOCKFROST_PROJECT_ID=.*/BLOCKFROST_PROJECT_ID=$bf_id/' /var/www/html/.env" 2>/dev/null
            docker exec chainvote-serverless sh -c "sed -i 's/BF_MAINNET_ID=.*/BF_MAINNET_ID=$bf_id/' /code/.env" 2>/dev/null
            print_success "Blockfrost Mainnet ID configured"
        else
            print_info "Skipping Blockfrost configuration"
        fi
        docker exec chainvote-app bash -c "sed -i 's|CARDANO_LUCID_NETWORK=.*|CARDANO_LUCID_NETWORK=mainnet|' /var/www/html/.env" 2>/dev/null
        docker exec chainvote-app bash -c "sed -i 's|blockfrost.io/api/v0|cardano-mainnet.blockfrost.io/api/v0|' /var/www/html/.env" 2>/dev/null
        docker exec chainvote-app bash -c "sed -i 's/CARDANO_NETWORK=.*/CARDANO_NETWORK=1/' /var/www/html/.env" 2>/dev/null
        ;;
esac

print_step "6" "Generating application keys..."
docker exec -u sail chainvote-app bash -c "cd /var/www/html && php artisan key:generate --force" 2>/dev/null
docker exec -u sail chainvote-app bash -c "cd /var/www/html && php artisan ciphersweet:generate-key --force" 2>/dev/null
print_success "Application keys generated"

print_step "7" "Running database migrations..."
docker exec -u sail chainvote-app bash -c "cd /var/www/html && php artisan migrate --force" 2>/dev/null
print_success "Database migrations completed"

print_step "8" "Seeding database..."
docker exec -u sail chainvote-app bash -c "cd /var/www/html && php artisan db:seed --class=RoleSeeder --force" 2>/dev/null
docker exec -u sail chainvote-app bash -c "cd /var/www/html && php artisan db:seed --class=AdminUserSeeder --force" 2>/dev/null
print_success "Database seeded"

print_step "9" "Installing frontend dependencies..."
docker exec -u sail chainvote-app bash -c "cd /var/www/html && yarn install --immutable" 2>/dev/null || docker exec -u sail chainvote-app bash -c "cd /var/www/html && yarn install" 2>/dev/null
print_success "Frontend dependencies installed"

print_step "10" "Building frontend assets..."
docker exec -u sail chainvote-app bash -c "cd /var/www/html && yarn build" 2>/dev/null
print_success "Frontend assets built"

print_step "11" "Fixing storage permissions..."
docker exec chainvote-app bash -c "chown -R sail:sail /var/www/html/storage /var/www/html/bootstrap/cache && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache" 2>/dev/null
print_success "Storage permissions fixed"

print_step "12" "Fixing WASM modules..."
mkdir -p application/node_modules/.vite/deps 2>/dev/null || true
cp -v application/node_modules/lucid-cardano/esm/src/core/libs/cardano_message_signing/cardano_message_signing_bg.wasm \
    application/node_modules/.vite/deps/ 2>/dev/null || true
cp -v application/node_modules/lucid-cardano/esm/src/core/libs/cardano_multiplatform_lib/cardano_multiplatform_lib_bg.wasm \
    application/node_modules/.vite/deps/ 2>/dev/null || true
print_success "WASM modules fixed"

print_header "Setup Complete!"
echo ""
echo -e "${GREEN}Services are now running:${NC}"
echo "  - Main App:        http://localhost:8080"
echo "  - Admin Dashboard: http://localhost:8080/admin/dashboard"
echo "  - Vite Dev Server: http://localhost:5173"
echo "  - Lucid API:       http://localhost:3000"
echo "  - MinIO Console:   http://localhost:9001"
echo "  - MinIO (S3):     http://localhost:9000"
echo ""
echo -e "${GREEN}Admin Credentials:${NC}"
echo "  Email:    admin@chainvote.local"
echo "  Password: ouroboros"
echo ""
echo "To stop services:  make down"
echo "To start services: make up"
echo "To view logs:      make logs"
echo ""
