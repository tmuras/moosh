#!/bin/bash
#
# Clears the Moodle database and dataroot, then restores from dump files.
# Supports both Moodle 5.1 and 5.2 via MOODLE_DIR environment variable.
# Expects dump.sql.gz and data.tar.gz in the test_data_setup directory.
#
# Usage:
#   bash clear.sh                                    # defaults to moodle51
#   MOODLE_DIR=/var/www/html/moodle52 bash clear.sh  # uses moodle52
#

set -euo pipefail

MOODLE_DIR="${MOODLE_DIR:-/var/www/html/moodle51}"
MOODLE_BASENAME="$(basename "$MOODLE_DIR")"
DB_NAME="$MOODLE_BASENAME"
DB_USER="root"
DB_PASS="a"
DB_HOST="localhost"
DATAROOT="/opt/data/$MOODLE_BASENAME"
SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"

# Use test_data_setup/ for moodle51 (legacy), test_data_setup_moodle52/ for others.
if [[ "$MOODLE_BASENAME" == "moodle51" ]]; then
    SETUP_DIR="$SCRIPT_DIR/test_data_setup"
else
    SETUP_DIR="$SCRIPT_DIR/test_data_setup_${MOODLE_BASENAME}"
fi
DUMP_FILE="$SETUP_DIR/dump.sql.gz"
DATA_FILE="$SETUP_DIR/data.tar.gz"

# Check dump files exist.
if [[ ! -f "$DUMP_FILE" ]]; then
    echo "ERROR: $DUMP_FILE not found. Run dump.sh first."
    exit 1
fi
if [[ ! -f "$DATA_FILE" ]]; then
    echo "ERROR: $DATA_FILE not found. Run dump.sh first."
    exit 1
fi

TOTAL_START=$SECONDS

echo "=== $MOODLE_BASENAME clear & restore ==="

# Drop and recreate database.
echo "Dropping and recreating database '$DB_NAME'..."
STEP_START=$SECONDS
mysql -u"$DB_USER" -p"$DB_PASS" -h"$DB_HOST" <<SQL
DROP DATABASE IF EXISTS \`$DB_NAME\`;
CREATE DATABASE \`$DB_NAME\` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
SQL
echo "Database recreated. ($((SECONDS - STEP_START))s)"

# Restore database from dump.
echo "Restoring database from dump.sql.gz..."
STEP_START=$SECONDS
gunzip -c "$DUMP_FILE" | mysql -u"$DB_USER" -p"$DB_PASS" -h"$DB_HOST" "$DB_NAME"
echo "Database restored. ($((SECONDS - STEP_START))s)"

# Clear dataroot.
echo "Clearing dataroot '$DATAROOT'..."
STEP_START=$SECONDS
sudo rm -rf "${DATAROOT:?}"/*
echo "Dataroot cleared. ($((SECONDS - STEP_START))s)"

# Restore dataroot from archive.
echo "Restoring dataroot from data.tar.gz..."
STEP_START=$SECONDS
sudo tar xzf "$DATA_FILE" -C "$(dirname "$DATAROOT")"
echo "Dataroot restored. ($((SECONDS - STEP_START))s)"

echo "=== Done in $((SECONDS - TOTAL_START))s ==="
