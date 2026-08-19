#!/usr/bin/env bash
#
# release-zip.sh
#
# Creates a clean release ZIP of the YonksTEAM theme, excluding
# development-only files and directories.
#
# Usage:
#   bash release-zip.sh [tag]
#
# If no tag is provided, the script reads the version from style.css.
# The resulting ZIP is named  yonksteam-<tag>.zip  and placed in the
# project root.

set -euo pipefail

THEME_SLUG="${THEME:-yonksteam}"
THEME_DIR="wp-content/themes/${THEME_SLUG}"
PROJECT_ROOT="$(cd "$(dirname "$0")" && pwd)"

cd "$PROJECT_ROOT"

# Determine tag / version
if [ $# -ge 1 ]; then
    TAG="$1"
else
    # Fall back to Version: field in style.css
    TAG="$(grep -i '^Version:' "$THEME_DIR/style.css" | head -1 | sed 's/.*:[[:space:]]*//;s/[[:space:]]*$//' 2>/dev/null || echo "dev")"
fi

ZIP_NAME="${THEME_SLUG}-${TAG}.zip"
ZIP_PATH="${PROJECT_ROOT}/${ZIP_NAME}"

echo "==> Building release ZIP: ${ZIP_NAME}"

# Verify theme directory exists
if [ ! -d "$THEME_DIR" ]; then
    echo "ERROR: Theme directory not found at ${THEME_DIR}" >&2
    exit 1
fi

# Remove old ZIP if present
[ -f "$ZIP_PATH" ] && rm "$ZIP_PATH"

cd "$(dirname "$THEME_DIR")"

zip -r "${ZIP_PATH}" "$(basename "$THEME_DIR")" \
    -x "$(basename "$THEME_DIR")/node_modules/*" \
    -x "$(basename "$THEME_DIR")/src/*" \
    -x "$(basename "$THEME_DIR")/package.json" \
    -x "$(basename "$THEME_DIR")/package-lock.json" \
    -x "$(basename "$THEME_DIR")/postcss.config.js" \
    -x "$(basename "$THEME_DIR")/tailwind.config.js" \
    -x "$(basename "$THEME_DIR")/.gitignore"

echo "==> Done: ${ZIP_NAME}"
echo "    Size: $(du -h "${ZIP_PATH}" | cut -f1)"