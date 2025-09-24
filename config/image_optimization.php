<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Configurações de Otimização de Imagem
    |--------------------------------------------------------------------------
    |
    | Estas configurações controlam como as imagens são otimizadas durante
    | o upload. Você pode ajustar a qualidade, dimensões máximas e tamanho
    | máximo do arquivo para atender às suas necessidades.
    |
    */

    // Largura máxima em pixels (0 = sem limite)
    'max_width' => env('IMAGE_MAX_WIDTH', 1920),

    // Altura máxima em pixels (0 = sem limite)
    'max_height' => env('IMAGE_MAX_HEIGHT', 1080),

    // Qualidade da imagem (1-100, onde 100 é a melhor qualidade)
    'quality' => env('IMAGE_QUALITY', 85),

    // Tamanho máximo do arquivo em KB (0 = sem limite)
    'max_file_size' => env('IMAGE_MAX_FILE_SIZE', 2048),

    // Formato de saída (jpeg, png, webp)
    'output_format' => env('IMAGE_OUTPUT_FORMAT', 'jpeg'),

    // Manter proporção da imagem
    'maintain_aspect_ratio' => env('IMAGE_MAINTAIN_ASPECT_RATIO', true),

    // Aplicar otimização apenas se necessário
    'optimize_only_if_needed' => env('IMAGE_OPTIMIZE_ONLY_IF_NEEDED', true),
];
