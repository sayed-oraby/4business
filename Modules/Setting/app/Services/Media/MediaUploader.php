<?php

namespace Modules\Setting\Services\Media;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Intervention\Image\Drivers\Imagick\Driver as ImagickDriver;
use Intervention\Image\ImageManager;
use Modules\Setting\Support\UploadedMedia;
use RuntimeException;

class MediaUploader
{
    protected ImageManager $images;

    public function __construct($images = null)
    {
        $this->images = $images instanceof ImageManager
            ? $images
            : $this->createImageManager();
    }

    /**
     * Store the uploaded file on disk with optional optimization.
     *
     * @param  array{disk?: string, directory?: string, max_width?: int, max_height?: int, quality?: int, visibility?: string}  $options
     */
    public function upload(UploadedFile $file, string $directory, array $options = []): UploadedMedia
    {
        $diskName = $options['disk'] ?? 'public';
        $disk = Storage::disk($diskName);
        $directory = trim($directory, '/');
        $filename = $this->generateFilename($file);
        $path = $directory !== '' ? $directory.'/'.$filename : $filename;

        if ($this->isImage($file)) {
            $binary = $this->processImage($file, $options);
            $disk->put($path, $binary, [
                'visibility' => $options['visibility'] ?? Filesystem::VISIBILITY_PUBLIC,
            ]);
        } else {
            $disk->putFileAs($directory, $file, $filename, [
                'visibility' => $options['visibility'] ?? Filesystem::VISIBILITY_PUBLIC,
            ]);
        }

        return new UploadedMedia(
            path: $path,
            url: $disk->url($path),
            disk: $diskName,
            size: $disk->size($path),
            mime: $file->getClientMimeType(),
            originalName: $file->getClientOriginalName()
        );
    }

    /**
     * Simple chunked upload helper.
     *
     * Chunks are written to storage/app/chunks/{uploadId} and merged
     * once the last chunk arrives. Returns the assembled temp path.
     */
    public function appendChunk(UploadedFile $chunk, string $uploadId, int $index, int $totalChunks): ?string
    {
        $tempDirectory = storage_path('app/chunks/'.$uploadId);
        if (! is_dir($tempDirectory)) {
            mkdir($tempDirectory, 0755, true);
        }

        $chunkPath = $tempDirectory.'/chunk_'.$index;
        $chunk->move($tempDirectory, 'chunk_'.$index);

        if ($index + 1 < $totalChunks) {
            return null;
        }

        $assembled = $tempDirectory.'/assembled';
        $output = fopen($assembled, 'ab');

        if (! $output) {
            throw new RuntimeException('Unable to assemble upload chunks.');
        }

        for ($i = 0; $i < $totalChunks; $i++) {
            $part = $tempDirectory.'/chunk_'.$i;
            $input = fopen($part, 'rb');
            stream_copy_to_stream($input, $output);
            fclose($input);
            @unlink($part);
        }

        fclose($output);
        @rmdir($tempDirectory);

        return $assembled;
    }

    protected function processImage(UploadedFile $file, array $options): string
    {
        $image = $this->images->read($file->getPathname());
        $maxWidth = $options['max_width'] ?? 800;
        $maxHeight = $options['max_height'] ?? null;

        if ($maxWidth && $image->width() > $maxWidth) {
            $image->scaleDown(width: $maxWidth, height: $maxHeight);
        }

        $extension = strtolower($file->getClientOriginalExtension() ?: 'png');
        $quality = $options['quality'] ?? 85;

        return (string) $image->encodeByExtension($extension, quality: $quality);
    }

    protected function isImage(UploadedFile $file): bool
    {
        return str_starts_with($file->getClientMimeType(), 'image/');
    }

    protected function generateFilename(UploadedFile $file): string
    {
        $extension = strtolower($file->getClientOriginalExtension() ?: 'bin');

        return Str::uuid()->toString().'.'.$extension;
    }

    protected function createImageManager(): ImageManager
    {
        $driver = strtolower((string) config('image.driver', 'gd'));
        $driverInstance = $driver === 'imagick'
            ? new ImagickDriver()
            : new GdDriver();

        return new ImageManager($driverInstance);
    }
}
