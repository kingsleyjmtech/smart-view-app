#!/bin/bash

# Check if the zip file exists
# shellcheck disable=SC2012
# shellcheck disable=SC2035
ZIP_FILE=$(ls *.zip | head -n 1)
echo "Found ZIP File: $ZIP_FILE"

if [ -z "$ZIP_FILE" ]; then
  echo "No ZIP file found. Exiting..."
  exit 1
fi

# Uncomment the following line if you want to confirm before deployment
echo "You are about to deploy $ZIP_FILE to the prod server. Are you sure? (yes/no)"
read -r CONFIRM

if [ "$CONFIRM" != "yes" ]; then
  echo "Deployment cancelled."
  exit 1
fi

CURRENT_DIR=$(pwd)
APP_NAME="name-of-app-prod"
HOME_DIR="/home/example"
APP_DIR="$HOME_DIR/__app/$APP_NAME"
DEPLOY_DIR="$HOME_DIR/deploy.example.com"
PUBLIC_DIR="$HOME_DIR/app.example.com"
BACKUP_DIR="$APP_DIR/backup/$(date +%Y-%m-%d_%H-%M-%S)"

DEPLOY_DIR_SYMLINK="$DEPLOY_DIR/$APP_NAME"
LATEST_DIR_SYMLINK="$APP_DIR/latest"

# Check if the home directory exists
if [ ! -d "$HOME_DIR" ]; then
  echo "Home directory $HOME_DIR does not exist. Exiting..."
  exit 1
fi

# Check if the app directory exists
if [ ! -d "$APP_DIR" ]; then
  echo "App directory $APP_DIR does not exist. Exiting..."
  exit 1
fi

# Check if the deploy directory exists
if [ ! -d "$DEPLOY_DIR" ]; then
  echo "Deploy directory $DEPLOY_DIR does not exist. Exiting..."
  exit 1
fi

# Check if the public directory exists
if [ ! -d "$PUBLIC_DIR" ]; then
  echo "Public directory $PUBLIC_DIR does not exist. Exiting..."
  exit 1
fi

# Check if .env file exists
if [ ! -f "$APP_DIR/.env" ]; then
  echo ".env file does not exist in $APP_DIR. Exiting..."
  exit 1
fi

# Check if index.php file exists
if [ ! -f "$APP_DIR/index.php" ]; then
  echo "index.php file does not exist in $APP_DIR. Exiting..."
  exit 1
fi

# Check if the backup directory exists
if [ -d "$BACKUP_DIR" ]; then
  echo "Backup directory $BACKUP_DIR already exists. Exiting..."
  exit 1
fi

# Create backup directory
echo "Create backup directory $BACKUP_DIR..."
mkdir -p "$BACKUP_DIR" || exit

# Print deployment details
echo "Deploying to prod server..."
echo "Current directory: $CURRENT_DIR"
echo "Home directory: $HOME_DIR"
echo "App directory: $APP_DIR"
echo "Deploy directory: $DEPLOY_DIR"
echo "Public directory: $PUBLIC_DIR"
echo "Backup directory: $BACKUP_DIR"
echo "Deploy directory symlink: $DEPLOY_DIR_SYMLINK"
echo "Latest directory symlink: $LATEST_DIR_SYMLINK"

# Unzip the file
echo "Unzipping $ZIP_FILE to $CURRENT_DIR..."
unzip -o "$ZIP_FILE" -d "$CURRENT_DIR" || exit

# Copy the .env file
echo "Copying the .env file to current directory..."
cp "$APP_DIR/.env" "$CURRENT_DIR/.env" || exit

# Create symlink to deploy directory
echo "Create symlink to deploy directory..."
ln -sfn "$CURRENT_DIR" "$DEPLOY_DIR_SYMLINK"

# Change directory to deploy directory
cd "$DEPLOY_DIR_SYMLINK" || exit

# Run database migrations
echo "Run database migrations..."
php artisan migrate --force || exit

# Run database seeders
 echo "Run database seeders..."
 php artisan db:seed --force || exit

# Clear cache
echo "Clear cache..."
php artisan cache:clear || exit

# Clear config cache
echo "Clear config cache..."
php artisan config:clear || exit

# Clear route cache
echo "Clear route cache..."
php artisan route:clear || exit

# Clear view cache
echo "Clear view cache..."
php artisan view:clear || exit

# Remove symlink to deploy directory
echo "Remove symlink to deploy directory..."
if [ -d "$DEPLOY_DIR_SYMLINK" ]; then
    echo "$DEPLOY_DIR_SYMLINK exists. Removing it..."
    rm "$DEPLOY_DIR_SYMLINK"
else
    echo "$DEPLOY_DIR_SYMLINK does not exist."
fi

# Backup app directory
echo "Backup public directory..."

# Move all visible files and directories
for file in "$PUBLIC_DIR"/*; do
    mv "$file" "$BACKUP_DIR" || exit
done

# Move hidden files and directories
for file in "$PUBLIC_DIR"/.*; do
    if [ "$file" = "$PUBLIC_DIR/." ] || [ "$file" = "$PUBLIC_DIR/.." ]; then
        continue
    fi
    mv "$file" "$BACKUP_DIR" || exit
done

# Copy public directory to public directory
echo "Copy public directory to $PUBLIC_DIR..."
cp -r "$CURRENT_DIR/public/." "$PUBLIC_DIR" || exit

# Copy index.php to public directory
echo "Copy index.php to $PUBLIC_DIR..."
cp "$APP_DIR/index.php" "$PUBLIC_DIR" || exit

# Remove symlink to latest directory
echo "Remove symlink to latest directory..."
if [ -d "$LATEST_DIR_SYMLINK" ]; then
    echo "$LATEST_DIR_SYMLINK exists. Removing it..."
    rm "$LATEST_DIR_SYMLINK" || exit
else
    echo "$LATEST_DIR_SYMLINK does not exist."
fi

# Create symlink to latest directory
echo "Create symlink to latest directory..."
ln -sfn "$CURRENT_DIR" "$LATEST_DIR_SYMLINK"

# Print success message
echo "Deployment complete."
