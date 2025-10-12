#!/bin/bash

# Script de conversión de videos para Monkiosk
# Convierte videos a formato óptimo para reproducción en kiosks

set -e

# Colores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Función para mostrar ayuda
show_help() {
    echo -e "${BLUE}╔════════════════════════════════════════════════════════╗${NC}"
    echo -e "${BLUE}║   Conversor de Videos para Monkiosk                 ║${NC}"
    echo -e "${BLUE}╚════════════════════════════════════════════════════════╝${NC}"
    echo ""
    echo "Uso: $0 <archivo_entrada> [archivo_salida] [formato]"
    echo ""
    echo "Formatos disponibles:"
    echo "  mp4     - Convierte a MP4 (H.264 + AAC) - Recomendado"
    echo "  webm    - Convierte a WEBM (VP9 + Opus)"
    echo "  auto    - Detecta mejor formato (por defecto)"
    echo ""
    echo "Ejemplos:"
    echo "  $0 video.avi                    # Convierte a MP4 automáticamente"
    echo "  $0 video.avi output.mp4         # Convierte a MP4 con nombre específico"
    echo "  $0 video.mov output.webm webm   # Convierte a WEBM"
    echo ""
    echo "Requisitos:"
    echo "  - FFmpeg instalado (sudo apt install ffmpeg)"
    echo ""
}

# Verificar FFmpeg
check_ffmpeg() {
    if ! command -v ffmpeg &> /dev/null; then
        echo -e "${RED}✗ Error: FFmpeg no está instalado${NC}"
        echo -e "${YELLOW}Instala FFmpeg con: sudo apt install ffmpeg${NC}"
        exit 1
    fi
    echo -e "${GREEN}✓ FFmpeg encontrado${NC}"
}

# Obtener información del video
get_video_info() {
    local file=$1
    echo -e "${BLUE}Analizando video...${NC}"
    ffprobe -v quiet -print_format json -show_streams -show_format "$file"
}

# Convertir a MP4
convert_to_mp4() {
    local input=$1
    local output=$2
    
    echo -e "${BLUE}Convirtiendo a MP4 (H.264 + AAC)...${NC}"
    echo "Entrada: $input"
    echo "Salida: $output"
    echo ""
    
    ffmpeg -i "$input" \
        -c:v libx264 \
        -preset medium \
        -crf 23 \
        -c:a aac \
        -b:a 192k \
        -ar 48000 \
        -vf "scale=1920:1080:force_original_aspect_ratio=decrease,pad=1920:1080:(ow-iw)/2:(oh-ih)/2" \
        -y \
        "$output"
    
    if [ $? -eq 0 ]; then
        echo -e "${GREEN}✓ Conversión completada exitosamente${NC}"
        show_file_info "$output"
    else
        echo -e "${RED}✗ Error en la conversión${NC}"
        exit 1
    fi
}

# Convertir a WEBM
convert_to_webm() {
    local input=$1
    local output=$2
    
    echo -e "${BLUE}Convirtiendo a WEBM (VP9 + Opus)...${NC}"
    echo "Entrada: $input"
    echo "Salida: $output"
    echo ""
    
    ffmpeg -i "$input" \
        -c:v libvpx-vp9 \
        -b:v 5M \
        -c:a libopus \
        -b:a 192k \
        -vf "scale=1920:1080:force_original_aspect_ratio=decrease,pad=1920:1080:(ow-iw)/2:(oh-ih)/2" \
        -y \
        "$output"
    
    if [ $? -eq 0 ]; then
        echo -e "${GREEN}✓ Conversión completada exitosamente${NC}"
        show_file_info "$output"
    else
        echo -e "${RED}✗ Error en la conversión${NC}"
        exit 1
    fi
}

# Mostrar información del archivo
show_file_info() {
    local file=$1
    local size=$(du -h "$file" | cut -f1)
    echo ""
    echo -e "${GREEN}Información del archivo de salida:${NC}"
    echo "Nombre: $file"
    echo "Tamaño: $size"
    
    if command -v ffprobe &> /dev/null; then
        local duration=$(ffprobe -v error -show_entries format=duration -of default=noprint_wrappers=1:nokey=1 "$file")
        local duration_formatted=$(printf '%02d:%02d\n' $((${duration%.*}/60)) $((${duration%.*}%60)))
        echo "Duración: $duration_formatted"
    fi
}

# Main
main() {
    echo -e "${BLUE}════════════════════════════════════════════════════════${NC}"
    echo -e "${BLUE}    Conversor de Videos para Monkiosk v1.0             ${NC}"
    echo -e "${BLUE}════════════════════════════════════════════════════════${NC}"
    echo ""
    
    # Verificar argumentos
    if [ $# -eq 0 ] || [ "$1" == "-h" ] || [ "$1" == "--help" ]; then
        show_help
        exit 0
    fi
    
    # Verificar FFmpeg
    check_ffmpeg
    echo ""
    
    # Procesar argumentos
    local input_file=$1
    local output_file=$2
    local format=${3:-auto}
    
    # Verificar que el archivo de entrada existe
    if [ ! -f "$input_file" ]; then
        echo -e "${RED}✗ Error: El archivo '$input_file' no existe${NC}"
        exit 1
    fi
    
    # Determinar archivo de salida si no se especificó
    if [ -z "$output_file" ]; then
        local basename=$(basename "$input_file")
        local filename="${basename%.*}"
        output_file="${filename}.mp4"
    fi
    
    # Determinar formato si es auto
    if [ "$format" == "auto" ]; then
        if [[ "$output_file" =~ \.webm$ ]]; then
            format="webm"
        else
            format="mp4"
        fi
    fi
    
    # Realizar conversión
    case $format in
        mp4)
            convert_to_mp4 "$input_file" "$output_file"
            ;;
        webm)
            convert_to_webm "$input_file" "$output_file"
            ;;
        *)
            echo -e "${RED}✗ Formato no soportado: $format${NC}"
            echo "Usa 'mp4' o 'webm'"
            exit 1
            ;;
    esac
    
    echo ""
    echo -e "${GREEN}✓ Proceso completado${NC}"
    echo -e "${YELLOW}Siguiente paso: Copia el archivo a la carpeta kiosk_web/docs/${NC}"
}

# Ejecutar main
main "$@"
