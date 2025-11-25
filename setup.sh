#!/usr/bin/env bash

# ==============================================================================
# HungerHop Setup Script
# ==============================================================================
# This script helps you set up the HungerHop Laravel application with Docker
# ==============================================================================

set -e

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[0;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Functions
print_header() {
    echo -e "${BLUE}================================${NC}"
    echo -e "${BLUE}$1${NC}"
    echo -e "${BLUE}================================${NC}"
}

print_success() {
    echo -e "${GREEN}✓ $1${NC}"
}

print_warning() {
    echo -e "${YELLOW}⚠ $1${NC}"
}

print_error() {
    echo -e "${RED}✗ $1${NC}"
}

# Check if command exists
command_exists() {
    command -v "$1" >/dev/null 2>&1
}

# Main setup
main() {
    print_header "HungerHop Setup"
    
    # Check prerequisites
    echo -e "\n${BLUE}Checking prerequisites...${NC}"
    
    if ! command_exists docker; then
        print_error "Docker is not installed. Please install Docker first."
        exit 1
    fi
    print_success "Docker is installed"
    
    if ! command_exists docker-compose; then
        print_error "Docker Compose is not installed. Please install Docker Compose first."
        exit 1
    fi
    print_success "Docker Compose is installed"
    
    # Create .env file
    echo -e "\n${BLUE}Setting up environment file...${NC}"
    if [ ! -f .env ]; then
        cp .env.docker .env
        print_success "Created .env file from .env.docker"
        
        # Generate APP_KEY
        echo -e "\n${BLUE}Generating APP_KEY...${NC}"
        APP_KEY=$(docker run --rm dunglas/frankenphp:latest-php8.2-alpine php -r "echo 'base64:'.base64_encode(random_bytes(32)).PHP_EOL;")
        
        if [[ "$OSTYPE" == "darwin"* ]]; then
            # macOS
            sed -i '' "s|APP_KEY=.*|APP_KEY=$APP_KEY|g" .env
        else
            # Linux
            sed -i "s|APP_KEY=.*|APP_KEY=$APP_KEY|g" .env
        fi
        print_success "Generated APP_KEY: $APP_KEY"
        
    else
        print_warning ".env file already exists, skipping..."
    fi
    
    # Build and start containers
    echo -e "\n${BLUE}Building Docker images...${NC}"
    docker-compose build
    print_success "Docker images built"
    
    echo -e "\n${BLUE}Starting containers...${NC}"
    docker-compose up -d
    print_success "Containers started"
    
    # Wait for MySQL to be ready
    echo -e "\n${BLUE}Waiting for MySQL to be ready...${NC}"
    for i in {1..30}; do
        if docker-compose exec -T mysql mysqladmin ping -h localhost -u root -proot --silent 2>/dev/null; then
            print_success "MySQL is ready"
            break
        fi
        echo -n "."
        sleep 2
    done
    echo ""
    
    # Run migrations
    echo -e "\n${BLUE}Running database migrations...${NC}"
    docker-compose exec -T app php artisan migrate --force
    print_success "Migrations completed"
    
    # Seed database
    echo -e "\n${BLUE}Seeding database...${NC}"
    docker-compose exec -T app php artisan db:seed --force
    print_success "Database seeded"
    
    # Create storage link
    echo -e "\n${BLUE}Creating storage link...${NC}"
    docker-compose exec -T app php artisan storage:link || true
    print_success "Storage link created"
    
    # Generate JWT secret
    echo -e "\n${BLUE}Generating JWT secret...${NC}"
    docker-compose exec -T app php artisan jwt:secret --force || true
    print_success "JWT secret generated"
    
    # Show status
    echo -e "\n${BLUE}Container status:${NC}"
    docker-compose ps
    
    # Final message
    echo -e "\n${GREEN}================================${NC}"
    echo -e "${GREEN}✓ Setup Complete!${NC}"
    echo -e "${GREEN}================================${NC}"
    echo -e ""
    echo -e "${YELLOW}Application:${NC} http://localhost:8080"
    echo -e "${YELLOW}Health Check:${NC} http://localhost:8080/up"
    echo -e "${YELLOW}Mailpit UI:${NC} http://localhost:8025"
    echo -e ""
    echo -e "${BLUE}Useful commands:${NC}"
    echo -e "  docker-compose logs -f          # View logs"
    echo -e "  docker-compose exec app sh      # Access container shell"
    echo -e "  docker-compose restart app      # Restart application"
    echo -e "  docker-compose down             # Stop all containers"
    echo -e ""
    echo -e "${YELLOW}⚠ Don't forget to update the following in .env:${NC}"
    echo -e "  - STRIPE_KEY and STRIPE_SECRET (for payments)"
    echo -e "  - MAIL_* settings (for production email)"
    echo -e "  - Database passwords (for production)"
    echo -e ""
}

# Run main function
main "$@"
