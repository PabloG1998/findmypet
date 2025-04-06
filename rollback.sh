#!/bin/bash

# Encuentra todos los commits donde se eliminaron archivos index.php
deleted_files=$(git log --diff-filter=D --summary | grep delete | grep 'index.php' | awk '{print $4}')

# Recorre cada archivo eliminado
for file in $deleted_files; do
    # Encuentra el commit donde se eliminó el archivo
    commit_hash=$(git log --diff-filter=D -- "$file" | grep '^commit' | head -n 1 | awk '{print $2}')
    # Restaura el archivo desde el commit anterior
    git checkout "${commit_hash}^" -- "$file"
    echo "Archivo restaurado: $file"
done

