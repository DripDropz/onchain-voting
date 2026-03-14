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
| Lucid | chainvote-lucid | http://localhost:3000 | Cardano Lucid API (NestJS) |
| Serverless | chainvote-serverless | http://localhost:3000 | Serverless Lucid functions |
| Database | chainvote-db | localhost:5432 | PostgreSQL 17 |
| Redis | onchain-voting-redis-1 | localhost:6379 | Redis cache |
| MinIO | onchain-voting-minio-1 | localhost:9000/9001 | S3-compatible storage |

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
- `DB_HOST` - Database host (e.g., `chainvote.db`)
- `REDIS_HOST` - Redis host (e.g., `redis`)
- `CARDANO_LUCID_ENDPOINT` - Lucid API endpoint (e.g., `http://lucid:3000`)
- `AWS_ENDPOINT` - MinIO endpoint (e.g., `http://minio:9000`)
- `MINIO_ENDPOINT` - MinIO endpoint (e.g., `http://minio:9000`)

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

### Cardano Integration
- Lucid Cardano (backend API via NestJS)
- Blockfrost API

### Infrastructure
- Docker
- Node.js 18+
- NestJS
- MinIO (S3 storage)
- Redis

## Troubleshooting

```bash
# Check container status
docker compose ps

# View logs
make logs

# Restart a specific service
docker compose restart worker

# Access container shell
make sh

# Run artisan commands
make artisan migrate
make artisan db:seed

# Reset everything
make clean
make init
```

### Common Issues

**Worker not starting:**
- Ensure `SUPERVISOR_PHP_USER=sail` is set in docker-compose.yml

**Redis connection errors:**
- Ensure `REDIS_HOST=redis` in `.env` (not `chainvote.redis`)

**Lucid API not accessible:**
- Ensure `CARDANO_LUCID_ENDPOINT=http://lucid:3000` in `.env`

**MinIO/S3 errors:**
- Ensure `MINIO_ENDPOINT=http://minio:9000` in `.env`

**Frontend build warnings about browserslist:**
- Run `docker exec chainvote-app bash -c "cd /var/www/html && yarn dedupe"`
