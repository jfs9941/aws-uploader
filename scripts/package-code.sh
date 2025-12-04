#!/bin/bash

# Script to package obfuscated code into code.zip for distribution
# This creates a zip file with the obfuscated code in a 'code' folder

set -e

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(dirname "$SCRIPT_DIR")"
OBFUSCATED_DIR="$PROJECT_ROOT/dist/yakpro-po/obfuscated"
ZIP_FILE="$PROJECT_ROOT/code.zip"
TEMP_DIR=$(mktemp -d)

echo "📦 Packaging obfuscated code..."

# Check if obfuscated directory exists
if [ ! -d "$OBFUSCATED_DIR" ]; then
    echo "❌ Error: Obfuscated code directory not found: $OBFUSCATED_DIR"
    echo "💡 Run obfuscation first: ./obfuscate.sh"
    exit 1
fi

# Create code directory in temp
CODE_DIR="$TEMP_DIR/code"
mkdir -p "$CODE_DIR"

# Copy obfuscated files to code directory
echo "📂 Copying obfuscated files..."
cp -r "$OBFUSCATED_DIR"/* "$CODE_DIR/"

# Create zip file
echo "🗜️  Creating code.zip..."
cd "$TEMP_DIR"
zip -r "$ZIP_FILE" code/ > /dev/null

# Clean up temp directory
rm -rf "$TEMP_DIR"

echo "✅ Successfully created: $ZIP_FILE"
echo "📊 File size: $(du -h "$ZIP_FILE" | cut -f1)"

