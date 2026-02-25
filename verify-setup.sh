#!/bin/bash
# Script de verificación - Componente de Convenios React

echo "================================================"
echo "✅ CHECKLIST DE VERIFICACIÓN"
echo "✅ Componente de Convenios React"
echo "================================================"
echo ""

# Colores para output
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Función para verificar archivo
check_file() {
    if [ -f "$1" ]; then
        echo -e "${GREEN}✓${NC} $1"
        return 0
    else
        echo -e "${RED}✗${NC} FALTA: $1"
        return 1
    fi
}

# Función para verificar directorio
check_dir() {
    if [ -d "$1" ]; then
        echo -e "${GREEN}✓${NC} $1/"
        return 0
    else
        echo -e "${RED}✗${NC} FALTA DIRECTORIO: $1/"
        return 1
    fi
}

echo "${YELLOW}📦 Verificando archivos React...${NC}"
check_file "resources/js/components/ConvenioForm.jsx"
check_file "resources/js/convenioCreate.jsx"
check_file "resources/js/utils/paisesLocalStorage.js"
check_file "resources/js/ejemplos/ejemplo-inicializar-paises.js"

echo ""
echo "${YELLOW}📄 Verificando vistas Blade...${NC}"
check_file "resources/views/convenios/create-react.blade.php"

echo ""
echo "${YELLOW}📚 Verificando documentación...${NC}"
check_file "docs/COMPONENTE_CONVENIO_REACT.md"
check_file "docs/INICIO_RAPIDO_CONVENIO_REACT.md"
check_file "docs/EJEMPLO_INTEGRACION_LARAVEL.php"
check_file "docs/EJEMPLOS_VISUALES_CONVENIO.md"
check_file "docs/RESUMEN_EJECUTIVO.md"
check_file "docs/ARCHIVO_RESUMEN.md"

echo ""
echo "${YELLOW}⚙️ Verificando configuración...${NC}"
if grep -q "convenioCreate.jsx" vite.config.js; then
    echo -e "${GREEN}✓${NC} vite.config.js - Actualizado"
else
    echo -e "${RED}✗${NC} vite.config.js - NO CONTIENE convenioCreate.jsx"
fi

echo ""
echo "================================================"
echo "📋 PRÓXIMOS PASOS"
echo "================================================"
echo ""
echo "1. Compilar con Vite:"
echo "   ${YELLOW}npm run dev${NC}"
echo ""
echo "2. Inicializar países (en consola navegador):"
echo "   ${YELLOW}import { inicializarPaisesLocalStorage } from ...${NC}"
echo "   ${YELLOW}inicializarPaisesLocalStorage()${NC}"
echo ""
echo "3. Actualizar controlador:"
echo "   ${YELLOW}app/Http/Controllers/ConvenioController.php${NC}"
echo "   Cambiar: view('convenios.create-react', ...)"
echo ""
echo "4. Acceder a:"
echo "   ${YELLOW}http://localhost:8000/convenios/create${NC}"
echo ""
echo "================================================"
echo "📖 DOCUMENTACIÓN"
echo "================================================"
echo ""
echo "Comienza con: ${YELLOW}docs/INICIO_RAPIDO_CONVENIO_REACT.md${NC}"
echo ""
echo "================================================"
