#!/bin/bash
#
# Dumps the moodle51 database and data directory into compressed archives.
# Output: dump.sql.gz and data.tar.gz in the current directory.
#

set -euo pipefail

DB_NAME="moodle51"
DB_USER="root"
DB_PASS="a"
DB_HOST="localhost"
DATAROOT="/opt/data/moodle51"

echo "=== Moodle 5.2 backup ==="

echo "Dumping database '$DB_NAME'..."
mysqldump -u"$DB_USER" -p"$DB_PASS" -h"$DB_HOST" "$DB_NAME" | gzip > dump.sql.gz
echo "Created dump.sql.gz ($(du -h dump.sql.gz | cut -f1))"

echo "Archiving dataroot '$DATAROOT'..."
rm -rf $DATAROOT/sessions/*  # Exclude session files
tar czf data.tar.gz -C "$(dirname "$DATAROOT")" "$(basename "$DATAROOT")"
echo "Created data.tar.gz ($(du -h data.tar.gz | cut -f1))"

echo "=== Done ==="
