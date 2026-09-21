<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;

final class FileUploader
{
    public function __construct(
        private readonly string $uploadDirectory,
    ) {}

    public function upload(UploadedFile $file): string
    {
        $extension = $file->getClientOriginalExtension();

        $filename = sprintf(
            'image_%s_%s.%s',
            date('YmdHis'),
            bin2hex(random_bytes(4)),
            $extension
        );

        $file->move(
            $this->uploadDirectory,
            $filename
        );

        return $filename;
    }

    public function delete(string $filename): void
    {
        $path = $this->uploadDirectory . '/' . $filename;

        if (is_file($path)) {
            unlink($path);
        }
    }
}
