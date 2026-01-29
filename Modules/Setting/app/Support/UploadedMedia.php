<?php

namespace Modules\Setting\Support;

class UploadedMedia
{
    public function __construct(
        protected string $path,
        protected string $url,
        protected string $disk,
        protected int $size,
        protected string $mime,
        protected string $originalName
    ) {
    }

    public function path(): string
    {
        return $this->path;
    }

    public function url(): string
    {
        return $this->url;
    }

    public function disk(): string
    {
        return $this->disk;
    }

    public function size(): int
    {
        return $this->size;
    }

    public function mime(): string
    {
        return $this->mime;
    }

    public function originalName(): string
    {
        return $this->originalName;
    }
}
