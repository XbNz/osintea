#!/bin/bash

# Fix all library paths to use @loader_path

echo "Fixing library paths..."

# Fix each library's ID (the name it reports for itself)
for lib in *.dylib; do
    echo "Setting ID for $lib"
    install_name_tool -id "@loader_path/$lib" "$lib" 2>/dev/null
done

# Fix libxml2's ICU dependency (special case - version mismatch)
echo "Fixing ICU version for libxml2..."
install_name_tool -change /opt/homebrew/opt/icu4c@76/lib/libicuuc.76.dylib @loader_path/libicuuc.77.dylib libxml2.2.dylib

# Now fix all the dependencies
echo "Fixing dependencies..."

# Function to fix dependencies for a library
fix_deps() {
    local lib="$1"
    echo "  Fixing $lib..."
    
    # Get all homebrew dependencies
    otool -L "$lib" | grep "/opt/homebrew" | awk '{print $1}' | while read dep; do
        # Get the base name of the dependency
        dep_base=$(basename "$dep")
        
        # Handle special cases where the name might be different
        local_dep="$dep_base"
        
        # Check if we have this file
        if [ -f "$local_dep" ]; then
            install_name_tool -change "$dep" "@loader_path/$local_dep" "$lib" 2>/dev/null
        else
            echo "    WARNING: $local_dep not found for $dep"
        fi
    done
}

# Fix all libraries
for lib in *.dylib; do
    fix_deps "$lib"
done

echo "Done! Verifying..."

# Quick verification
echo -e "\nLibraries still with external dependencies:"
for lib in *.dylib; do
    if otool -L "$lib" | grep -q "/opt/homebrew"; then
        echo "  $lib still has homebrew dependencies:"
        otool -L "$lib" | grep "/opt/homebrew"
    fi
done

echo -e "\nTo re-sign the libraries (removes warnings), run:"
echo "codesign --force --sign - *.dylib"
