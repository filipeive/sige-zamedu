#!/bin/bash

# Script de Deploy Automático do ZAMEDU SIGE
# Servidor de Produção Oracle Cloud

set -e

SERVER_USER="ubuntu"
SERVER_IP="146.235.224.99"
SSH_KEY="$HOME/.ssh/oracle-2025"
REMOTE_APP_DIR="/var/www/zamedu"
REPO_URL="git@github.com:filipeive/visionarios-school-system.git"

echo "==================================================================="
echo "   🚀 Deploy Automático — ZAMEDU SIGE (Produção)"
echo "==================================================================="
echo ""
echo "Servidor: $SERVER_USER@$SERVER_IP"
echo "Diretório Remoto: $REMOTE_APP_DIR"
echo "Repositório: $REPO_URL"
echo ""

# Verificar chave SSH
if [ ! -f "$SSH_KEY" ]; then
    echo "❌ ERRO: Chave SSH não encontrada em $SSH_KEY"
    exit 1
fi

echo "📦 Compilando assets do Vite localmente..."
npm run build

echo "🔐 Testando conexão SSH com o servidor..."
ssh -i "$SSH_KEY" -o ConnectTimeout=10 "$SERVER_USER@$SERVER_IP" "echo '✅ Conexão SSH bem-sucedida!'"

echo "🎨 Sincronizando assets de frontend (Vite build) com o servidor..."
ssh -i "$SSH_KEY" "$SERVER_USER@$SERVER_IP" "sudo mkdir -p /var/www/zamedu/public/build && sudo chown -R ubuntu:www-data /var/www/zamedu/public/build"
scp -i "$SSH_KEY" -r public/build/* "$SERVER_USER@$SERVER_IP:/var/www/zamedu/public/build/"

echo "🖼️ Sincronizando imagens oficiais (Logo e Cartaz da Escola dos Visionários)..."
ssh -i "$SSH_KEY" "$SERVER_USER@$SERVER_IP" "sudo mkdir -p /var/www/zamedu/public/images && sudo chown -R ubuntu:www-data /var/www/zamedu/public/images"
scp -i "$SSH_KEY" -r public/images/* "$SERVER_USER@$SERVER_IP:/var/www/zamedu/public/images/"

echo "📦 Executando rotina de deploy no servidor remoto..."
ssh -i "$SSH_KEY" "$SERVER_USER@$SERVER_IP" << 'ENDSSH'
set -e

# Criar base de dados no MySQL
mysql -ufdsms -pfdsadmin -e "CREATE DATABASE IF NOT EXISTS zamedu_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" || true

# Criar diretório se não existir
sudo mkdir -p /var/www/zamedu
sudo chown -R ubuntu:www-data /var/www/zamedu

if [ ! -d "/var/www/zamedu/.git" ]; then
    echo "📥 Clonando repositório..."
    git clone git@github.com:filipeive/visionarios-school-system.git /var/www/zamedu
else
    echo "🔄 Atualizando repositório..."
    cd /var/www/zamedu
    git fetch origin
    git reset --hard origin/main
fi

cd /var/www/zamedu

# Configurar permissões
sudo chown -R ubuntu:www-data /var/www/zamedu
sudo chmod -R 775 /var/www/zamedu/storage /var/www/zamedu/bootstrap/cache

# Instalar dependências Composer
echo "🎼 Instalando dependências Composer..."
composer install --no-dev --optimize-autoloader --no-interaction

# Criar ou atualizar ficheiro .env para produção
echo "⚙️ Configurando .env para produção..."
cat > .env << 'ENVEOF'
APP_NAME="Escola dos Visionários"
APP_ENV=production
APP_KEY=base64:3WAeZowb2U8CefDgtdfbzeD0TIvaUrphK94tbxIblhc=
APP_DEBUG=false
APP_URL=http://146.235.224.99/zamedu

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=zamedu_db
DB_USERNAME=fdsms
DB_PASSWORD=fdsadmin

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120
ENVEOF

# Executar migrations e seeders de dados iniciais (Zerar e recriar com a Escola dos Visionários)
echo "🗄️ Zerando e recriando a base de dados para a Escola dos Visionários (Quelimane & Alto Molócuè)..."
php artisan migrate:fresh --force --seed

# Otimizar caches Laravel
echo "⚡ Otimizando caches..."
php artisan config:cache
php artisan route:clear
php artisan view:cache
php artisan event:cache

# Criar symlinks para assets legados se existirem
cd /var/www/zamedu/public/build/assets
if [ -f "app-DF3WTqNP.css" ] && [ ! -f "app.css" ]; then
    ln -sf app-DF3WTqNP.css app.css
fi
if [ -f "app-CWmVnECS.js" ] && [ ! -f "app.js" ]; then
    ln -sf app-CWmVnECS.js app.js
fi
cd /var/www/zamedu

# Recarregar Nginx com segurança
echo "🌐 Recarregando Nginx..."
sudo nginx -t
sudo systemctl reload nginx

# Recarregar PHP-FPM se ativo
sudo systemctl reload php8.3-fpm 2>/dev/null || true

echo "✅ Deploy do ZAMEDU em produção concluído com sucesso!"
ENDSSH

echo ""
echo "==================================================================="
echo "   🎉 DEPLOY CONCLUÍDO COM SUCESSO!"
echo "==================================================================="
echo "Aceda a: http://146.235.224.99"
echo ""
