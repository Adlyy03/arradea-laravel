#!/bin/bash

# Arradea Marketplace - Development Helper Script
# Usage: ./dev.sh [command]

case "$1" in
    start)
        echo "🚀 Starting Arradea Marketplace..."
        echo ""
        echo "Starting Vite dev server..."
        npm run dev &
        echo ""
        echo "Starting Laravel server..."
        php artisan serve
        ;;
    
    build)
        echo "🔨 Building assets for production..."
        npm run build
        echo "✅ Build complete!"
        ;;
    
    fresh)
        echo "🔄 Fresh installation..."
        php artisan migrate:fresh --seed
        echo "✅ Database refreshed!"
        ;;
    
    cache)
        echo "🧹 Clearing all caches..."
        php artisan cache:clear
        php artisan config:clear
        php artisan view:clear
        php artisan route:clear
        echo "✅ Cache cleared!"
        ;;
    
    optimize)
        echo "⚡ Optimizing for production..."
        composer install --optimize-autoloader --no-dev
        npm run build
        php artisan config:cache
        php artisan route:cache
        php artisan view:cache
        echo "✅ Optimization complete!"
        ;;
    
    test)
        echo "🧪 Running tests..."
        php artisan test
        ;;
    
    format)
        echo "✨ Formatting code..."
        ./vendor/bin/pint
        echo "✅ Code formatted!"
        ;;
    
    install)
        echo "📦 Installing dependencies..."
        composer install
        npm install
        cp .env.example .env
        php artisan key:generate
        echo "✅ Installation complete!"
        echo ""
        echo "Next steps:"
        echo "1. Configure database in .env"
        echo "2. Run: ./dev.sh fresh"
        echo "3. Run: ./dev.sh start"
        ;;
    
    *)
        echo "Arradea Marketplace - Development Helper"
        echo ""
        echo "Usage: ./dev.sh [command]"
        echo ""
        echo "Commands:"
        echo "  start     - Start development servers (Vite + Laravel)"
        echo "  build     - Build assets for production"
        echo "  fresh     - Fresh database migration + seed"
        echo "  cache     - Clear all caches"
        echo "  optimize  - Optimize for production"
        echo "  test      - Run tests"
        echo "  format    - Format code with Pint"
        echo "  install   - Install dependencies"
        echo ""
        ;;
esac
