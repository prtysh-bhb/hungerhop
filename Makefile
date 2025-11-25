# ==============================================================================
# Makefile for HungerHop Laravel Application
# ==============================================================================
# Usage:
#   make help          - Show available commands
#   make dev           - Start development environment
#   make prod          - Start production environment
#   make build         - Build Docker images
#   make down          - Stop all containers
#   make logs          - View logs
# ==============================================================================

.PHONY: help dev prod build down logs clean install test

# Default target
.DEFAULT_GOAL := help

# Colors
BLUE := \033[0;34m
GREEN := \033[0;32m
YELLOW := \033[0;33m
RED := \033[0;31m
NC := \033[0m # No Color

# Docker Compose commands
DOCKER_COMPOSE := docker-compose
DOCKER_COMPOSE_PROD := docker-compose -f docker-compose.yml -f docker-compose.prod.yml

##@ Help
help: ## Display this help message
	@echo "$(BLUE)HungerHop Laravel Application - Docker Management$(NC)"
	@echo ""
	@awk 'BEGIN {FS = ":.*##"; printf "$(YELLOW)Usage:$(NC)\n  make $(GREEN)<target>$(NC)\n"} /^[a-zA-Z_-]+:.*?##/ { printf "  $(GREEN)%-15s$(NC) %s\n", $$1, $$2 } /^##@/ { printf "\n$(YELLOW)%s$(NC)\n", substr($$0, 5) } ' $(MAKEFILE_LIST)

##@ Development
dev: ## Start development environment
	@echo "$(BLUE)Starting development environment...$(NC)"
	$(DOCKER_COMPOSE) up -d
	@echo "$(GREEN)✓ Development environment started$(NC)"
	@echo "$(YELLOW)Application:$(NC) http://localhost:8080"
	@echo "$(YELLOW)Mailpit UI:$(NC) http://localhost:8025"
	@make status

dev-build: ## Build and start development environment
	@echo "$(BLUE)Building and starting development environment...$(NC)"
	$(DOCKER_COMPOSE) up -d --build
	@echo "$(GREEN)✓ Development environment built and started$(NC)"
	@make status

dev-fresh: down build dev migrate-fresh seed ## Fresh development setup (rebuild, migrate, seed)
	@echo "$(GREEN)✓ Fresh development environment ready$(NC)"

##@ Production
prod: ## Start production environment
	@echo "$(BLUE)Starting production environment...$(NC)"
	$(DOCKER_COMPOSE_PROD) up -d
	@echo "$(GREEN)✓ Production environment started$(NC)"
	@make status

prod-build: ## Build and start production environment
	@echo "$(BLUE)Building and starting production environment...$(NC)"
	$(DOCKER_COMPOSE_PROD) build --no-cache
	$(DOCKER_COMPOSE_PROD) up -d
	@echo "$(GREEN)✓ Production environment built and started$(NC)"
	@make status

prod-deploy: prod-build migrate optimize ## Full production deployment
	@echo "$(GREEN)✓ Production deployment complete$(NC)"

##@ Docker Management
build: ## Build Docker images
	@echo "$(BLUE)Building Docker images...$(NC)"
	$(DOCKER_COMPOSE) build
	@echo "$(GREEN)✓ Docker images built$(NC)"

rebuild: ## Rebuild Docker images without cache
	@echo "$(BLUE)Rebuilding Docker images...$(NC)"
	$(DOCKER_COMPOSE) build --no-cache
	@echo "$(GREEN)✓ Docker images rebuilt$(NC)"

down: ## Stop all containers
	@echo "$(BLUE)Stopping containers...$(NC)"
	$(DOCKER_COMPOSE) down
	@echo "$(GREEN)✓ Containers stopped$(NC)"

down-volumes: ## Stop and remove containers with volumes
	@echo "$(RED)⚠ This will delete all data in volumes!$(NC)"
	@read -p "Are you sure? (y/N): " confirm && [ $$confirm = y ] || exit 1
	$(DOCKER_COMPOSE) down -v
	@echo "$(GREEN)✓ Containers and volumes removed$(NC)"

restart: ## Restart all containers
	@echo "$(BLUE)Restarting containers...$(NC)"
	$(DOCKER_COMPOSE) restart
	@echo "$(GREEN)✓ Containers restarted$(NC)"

restart-app: ## Restart only app container
	$(DOCKER_COMPOSE) restart app
	@echo "$(GREEN)✓ App container restarted$(NC)"

status: ## Show container status
	@echo "$(BLUE)Container Status:$(NC)"
	@$(DOCKER_COMPOSE) ps

##@ Logs
logs: ## View logs from all containers
	$(DOCKER_COMPOSE) logs -f

logs-app: ## View app logs
	$(DOCKER_COMPOSE) logs -f app

logs-mysql: ## View MySQL logs
	$(DOCKER_COMPOSE) logs -f mysql

logs-redis: ## View Redis logs
	$(DOCKER_COMPOSE) logs -f redis

logs-queue: ## View queue worker logs
	$(DOCKER_COMPOSE) logs -f queue

logs-scheduler: ## View scheduler logs
	$(DOCKER_COMPOSE) logs -f scheduler

##@ Laravel Commands
shell: ## Access app container shell
	$(DOCKER_COMPOSE) exec app sh

artisan: ## Run artisan command (e.g., make artisan cmd="migrate")
	$(DOCKER_COMPOSE) exec app php artisan $(cmd)

tinker: ## Open Laravel Tinker
	$(DOCKER_COMPOSE) exec app php artisan tinker

migrate: ## Run database migrations
	@echo "$(BLUE)Running migrations...$(NC)"
	$(DOCKER_COMPOSE) exec app php artisan migrate --force
	@echo "$(GREEN)✓ Migrations complete$(NC)"

migrate-fresh: ## Fresh migration (drop all tables)
	@echo "$(RED)⚠ This will drop all database tables!$(NC)"
	@read -p "Are you sure? (y/N): " confirm && [ $$confirm = y ] || exit 1
	$(DOCKER_COMPOSE) exec app php artisan migrate:fresh --force
	@echo "$(GREEN)✓ Fresh migration complete$(NC)"

migrate-rollback: ## Rollback last migration
	$(DOCKER_COMPOSE) exec app php artisan migrate:rollback --force

seed: ## Seed database
	@echo "$(BLUE)Seeding database...$(NC)"
	$(DOCKER_COMPOSE) exec app php artisan db:seed --force
	@echo "$(GREEN)✓ Database seeded$(NC)"

##@ Cache Management
cache-clear: ## Clear all caches
	@echo "$(BLUE)Clearing all caches...$(NC)"
	$(DOCKER_COMPOSE) exec app php artisan optimize:clear
	@echo "$(GREEN)✓ Caches cleared$(NC)"

config-clear: ## Clear config cache
	$(DOCKER_COMPOSE) exec app php artisan config:clear

route-clear: ## Clear route cache
	$(DOCKER_COMPOSE) exec app php artisan route:clear

view-clear: ## Clear view cache
	$(DOCKER_COMPOSE) exec app php artisan view:clear

optimize: ## Optimize application for production
	@echo "$(BLUE)Optimizing application...$(NC)"
	$(DOCKER_COMPOSE) exec app php artisan optimize
	@echo "$(GREEN)✓ Application optimized$(NC)"

##@ Testing
test: ## Run tests
	$(DOCKER_COMPOSE) exec app php artisan test

test-coverage: ## Run tests with coverage
	$(DOCKER_COMPOSE) exec app php artisan test --coverage

phpstan: ## Run PHPStan static analysis
	$(DOCKER_COMPOSE) exec app ./vendor/bin/phpstan analyse --memory-limit=2G

pint: ## Run Laravel Pint (code style fixer)
	$(DOCKER_COMPOSE) exec app ./vendor/bin/pint

pint-test: ## Test code style without fixing
	$(DOCKER_COMPOSE) exec app ./vendor/bin/pint --test

quality: ## Run all quality checks
	@echo "$(BLUE)Running quality checks...$(NC)"
	@make pint-test
	@make phpstan
	@make test
	@echo "$(GREEN)✓ All quality checks passed$(NC)"

##@ Database
db-shell: ## Access MySQL shell
	$(DOCKER_COMPOSE) exec mysql mysql -u root -p$(DB_ROOT_PASSWORD)

db-backup: ## Backup database to file
	@echo "$(BLUE)Backing up database...$(NC)"
	@mkdir -p backups
	$(DOCKER_COMPOSE) exec mysql mysqldump -u root -p$(DB_ROOT_PASSWORD) $(DB_DATABASE) > backups/backup-$$(date +%Y%m%d-%H%M%S).sql
	@echo "$(GREEN)✓ Database backed up to backups/$(NC)"

db-restore: ## Restore database from file (e.g., make db-restore file=backup.sql)
	@echo "$(BLUE)Restoring database from $(file)...$(NC)"
	$(DOCKER_COMPOSE) exec -T mysql mysql -u root -p$(DB_ROOT_PASSWORD) $(DB_DATABASE) < $(file)
	@echo "$(GREEN)✓ Database restored$(NC)"

##@ Queue Management
queue-work: ## Run queue worker manually
	$(DOCKER_COMPOSE) exec app php artisan queue:work --verbose

queue-restart: ## Restart queue worker
	@echo "$(BLUE)Restarting queue worker...$(NC)"
	$(DOCKER_COMPOSE) restart queue
	@echo "$(GREEN)✓ Queue worker restarted$(NC)"

queue-failed: ## Show failed queue jobs
	$(DOCKER_COMPOSE) exec app php artisan queue:failed

queue-retry: ## Retry all failed queue jobs
	$(DOCKER_COMPOSE) exec app php artisan queue:retry all

queue-monitor: ## Monitor queue
	$(DOCKER_COMPOSE) exec app php artisan queue:monitor

##@ Cleanup
clean: ## Clean up Docker resources
	@echo "$(BLUE)Cleaning up Docker resources...$(NC)"
	docker system prune -f
	@echo "$(GREEN)✓ Docker resources cleaned$(NC)"

clean-all: ## Clean up all Docker resources (including volumes)
	@echo "$(RED)⚠ This will remove all unused Docker resources!$(NC)"
	@read -p "Are you sure? (y/N): " confirm && [ $$confirm = y ] || exit 1
	docker system prune -af --volumes
	@echo "$(GREEN)✓ All Docker resources cleaned$(NC)"

##@ Composer & NPM
composer-install: ## Install PHP dependencies
	$(DOCKER_COMPOSE) exec app composer install

composer-update: ## Update PHP dependencies
	$(DOCKER_COMPOSE) exec app composer update

composer-require: ## Install composer package (e.g., make composer-require pkg="vendor/package")
	$(DOCKER_COMPOSE) exec app composer require $(pkg)

npm-install: ## Install Node dependencies
	$(DOCKER_COMPOSE) exec app npm install

npm-build: ## Build frontend assets
	$(DOCKER_COMPOSE) exec app npm run build

npm-dev: ## Build frontend assets for development
	$(DOCKER_COMPOSE) exec app npm run dev

##@ Security & Permissions
fix-permissions: ## Fix storage and cache permissions
	@echo "$(BLUE)Fixing permissions...$(NC)"
	$(DOCKER_COMPOSE) exec app chown -R www-data:www-data storage bootstrap/cache
	$(DOCKER_COMPOSE) exec app chmod -R 775 storage bootstrap/cache
	@echo "$(GREEN)✓ Permissions fixed$(NC)"

key-generate: ## Generate APP_KEY
	$(DOCKER_COMPOSE) exec app php artisan key:generate --force

jwt-secret: ## Generate JWT secret
	$(DOCKER_COMPOSE) exec app php artisan jwt:secret --force

storage-link: ## Create storage symlink
	$(DOCKER_COMPOSE) exec app php artisan storage:link

##@ Monitoring
stats: ## Show Docker container statistics
	docker stats

top: ## Show running processes in containers
	$(DOCKER_COMPOSE) top

inspect-app: ## Inspect app container
	docker inspect $$($(DOCKER_COMPOSE) ps -q app)

health: ## Check application health
	@curl -f http://localhost:8080/up && echo "$(GREEN)✓ Application is healthy$(NC)" || echo "$(RED)✗ Application is not responding$(NC)"

##@ Installation
install: ## Complete fresh installation
	@echo "$(BLUE)Installing HungerHop...$(NC)"
	@if [ ! -f .env ]; then \
		echo "$(YELLOW)Creating .env file...$(NC)"; \
		cp .env.docker .env; \
		echo "$(YELLOW)⚠ Please update .env with your configuration$(NC)"; \
		echo "$(YELLOW)⚠ Generate APP_KEY with: make key-generate$(NC)"; \
	fi
	@make dev-build
	@make migrate
	@make seed
	@make storage-link
	@echo "$(GREEN)✓ Installation complete!$(NC)"
	@echo "$(YELLOW)Application:$(NC) http://localhost:8080"
	@echo "$(YELLOW)Mailpit UI:$(NC) http://localhost:8025"

uninstall: down-volumes clean ## Complete uninstallation
	@echo "$(GREEN)✓ Uninstallation complete$(NC)"
