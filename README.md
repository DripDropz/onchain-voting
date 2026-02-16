# DripDropz Open Source On-Chain Voting

## Description

The goal of this code is to provide information, a framework, and ultimately a full-stack solution to users and 
organizations seeking to conduct governance or voting on the Cardano blockchain.

## Rationale

Key features that have informed design decisions of this platform include:

- **Public Auditability**: All pertinent information concerning a vote should be fully transparent and publicly auditable.
- **Vote Security**: Proof of voter participation intent using on-chain assets.
- **Ease of Use**: As easy as possible to maximize voter participation.

## License

Creative Commons Attribution 4.0 International License. See [LICENSE](LICENSE.md) for details.

---

# Local Development Setup

## Prerequisites

- Docker & Docker Compose
- Make

## Quick Start

### Automated Setup (Recommended)

```bash
make init
```

### Manual Setup

```bash
# Start services
make up

# Install dependencies
docker exec chainvote-app bash -c "cd /var/www/html && composer install --ignore-platform-reqs --no-interaction"

# Generate keys
docker exec chainvote-app bash -c "cd /var/www/html && php artisan key:generate --force"
docker exec chainvote-app bash -c "cd /var/www/html && php artisan ciphersweet:generate-key --force"

# Run migrations
docker exec chainvote-app bash -c "cd /var/www/html && php artisan migrate --force"

# Seed database
docker exec chainvote-app bash -c "cd /var/www/html && php artisan db:seed --class=RoleSeeder --force"
docker exec chainvote-app bash -c "cd /var/www/html && php artisan db:seed --class=AdminUserSeeder --force"

# Build frontend
docker exec chainvote-app bash -c "cd /var/www/html && yarn install && yarn build"
```

## Services

| Service | Container Name | URL | Description |
|---------|---------------|-----|-------------|
| App | chainvote-app | http://localhost:8080 | Main Laravel application |
| Worker | chainvote-worker | - | Laravel Horizon queue worker |
| Lucid | chainvote-lucid | http://localhost:3000 | Cardano Lucid API |
| Serverless | chainvote-serverless | - | Serverless functions |
| Database | chainvote-db | localhost:5432 | PostgreSQL |
| Redis | redis | localhost:6379 | Redis cache |
| MinIO | minio | localhost:9000/9001 | S3-compatible storage |

## Default Admin Credentials

- **Username:** `chainvote`
- **Password:** `ouroboros`

## Configuration

The setup wizard prompts for:

1. **Cardano Network** - Preview / Preprod / Mainnet
2. **Blockfrost Project ID** - Based on selected network
3. **App URL** - Default: http://localhost:8080

### Environment Variables

Key variables in `application/.env`:

- `BLOCKFROST_PROJECT_ID` - Blockfrost API key
- `CARDANO_NETWORK` - 0=preview, 1=preprod, 2=mainnet
- `DB_HOST` - Should match container name (e.g., `chainvote.db`)
- `REDIS_HOST` - Redis host
- `AWS_ENDPOINT` - MinIO endpoint

## Makefile Commands

| Command | Description |
|---------|-------------|
| `make init` | Run interactive setup wizard |
| `make up` | Start all services |
| `make down` | Stop all services |
| `make restart` | Restart all services |
| `make logs` | View app logs |
| `make logs-worker` | View worker logs |
| `make logs-lucid` | View lucid logs |
| `make migrate` | Run migrations |
| `make seed` | Seed database |
| `make build` | Build frontend |
| `make wasm` | Copy WASM modules |
| `make clean` | Remove all containers and volumes |

## Tech Stack

### Backend
- PHP 8.5
- Laravel 11.x
- Laravel Horizon
- Laravel Sanctum
- PostgreSQL 17

### Frontend
- Vue.js 3
- Vite
- Tailwind CSS
- Inertia.js

### Infrastructure
- Docker
- MinIO (S3 storage)
- Redis

## Troubleshooting

```bash
# Check container status
docker ps

# View logs
make logs

# Reset everything
make clean
make init
```
