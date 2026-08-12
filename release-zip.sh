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

THEME_DIR="wp-content/themes/yonksteam"
PROJECT_ROOT="$(cd "$(dirname "$0")" && pwd)"

cd "$PROJECT_ROOT"

# Determine tag / version
if [ $# -ge 1 ]; then
    TAG="$1"
else
    # Fall back to Version: field in style.css
    TAG="$(grep -i '^Version:' "$THEME_DIR/style.css" | head -1 | sed 's/.*:[[:space:]]*//;s/[[:space:]]*$//' 2>/dev/null || echo "dev")"
fi

ZIP_NAME="yonksteam-${TAG}.zip"
ZIP_PATH="${PROJECT_ROOT}/${ZIP_NAME}"

echo "==> Building release ZIP: ${ZIP_NAME}"

# Verify theme directory exists
if [ ! -d "$THEME_DIR" ]; then
    echo "ERROR: Theme directory not found at ${THEME_DIR}" >&2
    exit 1
fi

# Remove old ZIP if present
[ -f "$ZIP_PATH" ] && rm "$ZIP_PATH"

cd "$THEME_DIR"

zip -r "${ZIP_PATH}" . \
    -x "node_modules/*" \
    -x "src/*" \
    -x "package.json" \
    -x "package-lock.json" \
    -x "postcss.config.js" \
    -x "tailwind.config.js" \
    -x ".gitignore"

echo "==> Done: ${ZIP_NAME}"
echo "    Size: $(du -h "${ZIP_PATH}" | cut -f1)"