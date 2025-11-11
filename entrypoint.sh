#!/bin/bash

echo "✅ Ejecutando Flask (Python)..."
python3 pai_agent/app.py &

echo "✅ Ejecutando Apache (Laravel)..."
apache2-foreground
