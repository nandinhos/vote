<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class ImageOptimizationService
{
    private ImageManager $manager;

    protected int $maxWidth;

    protected int $maxHeight;

    protected int $quality;

    protected int $maxFileSize; // KB

    protected string $outputFormat;

    protected bool $maintainAspectRatio;

    protected bool $optimizeOnlyIfNeeded;

    public function __construct()
    {
        $this->manager = new ImageManager(new Driver);
        $this->maxWidth = config('image_optimization.max_width', 1920);
        $this->maxHeight = config('image_optimization.max_height', 1080);
        $this->quality = config('image_optimization.quality', 85);
        $this->maxFileSize = config('image_optimization.max_file_size', 2048);
        $this->outputFormat = config('image_optimization.output_format', 'jpeg');
        $this->maintainAspectRatio = config('image_optimization.maintain_aspect_ratio', true);
        $this->optimizeOnlyIfNeeded = config('image_optimization.optimize_only_if_needed', true);
    }

    /**
     * Otimiza uma imagem enviada via upload
     */
    public function optimizeUploadedImage(UploadedFile $file, string $directory = 'photos'): string
    {
        // Carregar a imagem
        $image = $this->manager->read($file->getPathname());

        // Redimensionar se necessário
        if ($image->width() > $this->maxWidth || $image->height() > $this->maxHeight) {
            if ($this->maintainAspectRatio) {
                $image->scale(
                    width: $this->maxWidth,
                    height: $this->maxHeight
                );
            } else {
                $image->resize($this->maxWidth, $this->maxHeight);
            }
        }

        // Gerar nome único para o arquivo com formato correto
        $extension = $this->outputFormat === 'jpeg' ? 'jpg' : $this->outputFormat;
        $filename = Str::uuid().'.'.$extension;
        $path = $directory.'/'.$filename;

        // Converter para o formato especificado e aplicar compressão
        switch ($this->outputFormat) {
            case 'png':
                $optimizedImage = $image->toPng();
                break;
            case 'webp':
                $optimizedImage = $image->toWebp($this->quality);
                break;
            default: // jpeg
                $optimizedImage = $image->toJpeg($this->quality);
                break;
        }

        // Salvar no storage
        Storage::disk('public')->put($path, $optimizedImage);

        return $path;
    }

    /**
     * Otimiza uma imagem existente no storage
     */
    public function optimizeExistingImage(string $imagePath): string
    {
        if (! Storage::disk('public')->exists($imagePath)) {
            throw new \Exception("Imagem não encontrada: {$imagePath}");
        }

        // Carregar a imagem do storage
        $imageContent = Storage::disk('public')->get($imagePath);
        $image = $this->manager->read($imageContent);

        // Redimensionar se necessário
        if ($image->width() > $this->maxWidth || $image->height() > $this->maxHeight) {
            $image->scale(
                width: $this->maxWidth,
                height: $this->maxHeight
            );
        }

        // Converter para JPEG e aplicar compressão
        $optimizedImage = $image->toJpeg($this->quality);

        // Sobrescrever a imagem original
        Storage::disk('public')->put($imagePath, $optimizedImage);

        return $imagePath;
    }

    /**
     * Verifica se uma imagem precisa ser otimizada
     */
    public function needsOptimization(UploadedFile $file): bool
    {
        if (! $this->optimizeOnlyIfNeeded) {
            return true;
        }

        // Verifica tamanho do arquivo
        if ($file->getSize() > ($this->maxFileSize * 1024)) {
            return true;
        }

        // Verifica dimensões da imagem
        $imageInfo = getimagesize($file->getPathname());
        if ($imageInfo) {
            [$width, $height] = $imageInfo;
            if ($width > $this->maxWidth || $height > $this->maxHeight) {
                return true;
            }
        }

        return false;
    }

    /**
     * Obtém informações sobre uma imagem
     */
    public function getImageInfo(UploadedFile $file): array
    {
        $imageInfo = getimagesize($file->getPathname());

        return [
            'original_size' => $file->getSize(),
            'original_size_mb' => round($file->getSize() / 1024 / 1024, 2),
            'width' => $imageInfo[0] ?? null,
            'height' => $imageInfo[1] ?? null,
            'mime_type' => $imageInfo['mime'] ?? null,
            'needs_optimization' => $this->needsOptimization($file),
        ];
    }

    /**
     * Configura os parâmetros de otimização
     */
    public function setOptimizationParams(
        ?int $maxWidth = null,
        ?int $maxHeight = null,
        ?int $quality = null,
        ?int $maxFileSize = null
    ): self {
        if ($maxWidth) {
            $this->maxWidth = $maxWidth;
        }
        if ($maxHeight) {
            $this->maxHeight = $maxHeight;
        }
        if ($quality) {
            $this->quality = $quality;
        }
        if ($maxFileSize) {
            $this->maxFileSize = $maxFileSize;
        }

        return $this;
    }

    /**
     * Obtém os parâmetros atuais de otimização
     */
    public function getOptimizationParams(): array
    {
        return [
            'max_width' => $this->maxWidth,
            'max_height' => $this->maxHeight,
            'quality' => $this->quality,
            'max_file_size_kb' => $this->maxFileSize,
        ];
    }
}
