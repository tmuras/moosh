#!/bin/bash
#
# Drops everything, reinstalls Moodle from scratch, populates test data,
# and creates a fresh dump (dump.sql.gz + data.tar.gz).
#

set -euo pipefail

DB_NAME="moodle51"
DB_USER="root"
DB_PASS="a"
DB_HOST="localhost"
DATAROOT="/opt/data/moodle51"
MOODLE_DIR="/var/www/html/moodle52"
WWWROOT="http://localhost/moodle51/public"
SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
ADMIN_USER="admin"
ADMIN_PASS="a"
ADMIN_EMAIL="admin@example.com"
SITE_FULLNAME="Moodle 5.2 Dev"
SITE_SHORTNAME="moodle51"
PHP=/usr/bin/php

echo "=== Moodle 5.2 full reinstall ==="

# 1. Drop and recreate database.
echo "Dropping and recreating database '$DB_NAME'..."
mysql -u"$DB_USER" -p"$DB_PASS" -h"$DB_HOST" <<SQL
DROP DATABASE IF EXISTS \`$DB_NAME\`;
CREATE DATABASE \`$DB_NAME\` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
SQL
echo "Database recreated."

# 2. Clear dataroot.
echo "Clearing dataroot '$DATAROOT'..."
sudo rm -rf "${DATAROOT:?}"/*
echo "Dataroot cleared."

# 3. Install Moodle.
echo "Installing Moodle..."
sudo -u www-data $PHP "$MOODLE_DIR/admin/cli/install_database.php" \
    --adminuser="$ADMIN_USER" \
    --adminpass="$ADMIN_PASS" \
    --adminemail="$ADMIN_EMAIL" \
    --fullname="$SITE_FULLNAME" \
    --shortname="$SITE_SHORTNAME" \
    --agree-license
echo "Moodle installed."

# 4. Run test data setup.
echo "Populating test data..."
sudo -u www-data $PHP "$SCRIPT_DIR/setup_testdata.php" $MOODLE_DIR
echo "Test data created."

# 5. Create dump (run from SCRIPT_DIR so files land here).
echo "Creating dump..."
cd "$SCRIPT_DIR"
bash "$SCRIPT_DIR/../dump.sh"

echo "=== Full reinstall complete ==="
