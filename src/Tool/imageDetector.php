<?php

namespace Keven1024\CSDN\Tool;

class imageDetector
{
    private $byteCache = [];

    /**
     * Number of cached bytes
     *
     * @var int
     */
    private $byteCacheLen = 0;

    /**
     * Maximum number of bytes to cache
     *
     * @var int
     */
    private $maxByteCacheLen = 0;

    /**
     * Path to the given file
     *
     * @var string
     */
    private $file = '';

    /**
     * Hash of the given file
     *
     * @var string
     */
    private $fileHash = '';

    public function setImageURL($image_url)
    {
        if (empty($image_url)) {
            return null;
        }
        $fileHash = $this->getHash($image_url);

        if ($this->fileHash !== $fileHash) {
            $this->byteCache = [];
            $this->byteCacheLen = 0;
            $this->maxByteCacheLen = 4096;
            $this->file = $image_url;
            $this->fileHash = $fileHash;

            $this->createImageURLCache();
        }
        return $this;
    }

    protected function createImageURLCache(): void
    {
        if (!empty($this->byteCache)) {
            return;
        }
        if (empty($this->file)) {
            return;
        }

        $data = file_get_contents($this->file, false, null, 0, $this->maxByteCacheLen);
        foreach (str_split($data) as $i => $char) {
            $this->byteCache[$i] = ord($char);
        }
        $this->byteCacheLen = count($this->byteCache);
    }

    function getFileType(): array
    {
        if (empty($this->byteCache)) {
            return [];
        }
        // Perform check
        if ($this->checkForBytes([0xFF, 0xD8, 0xFF])) {
            return [
                'ext' => 'jpg',
                'mime' => 'image/jpeg'
            ];
        }

        if ($this->checkForBytes([0x89, 0x50, 0x4E, 0x47, 0x0D, 0x0A, 0x1A, 0x0A])) {
            return [
                'ext' => 'png',
                'mime' => 'image/png'
            ];
        }

        if ($this->checkForBytes([0x47, 0x49, 0x46])) {
            return [
                'ext' => 'gif',
                'mime' => 'image/gif'
            ];
        }

        if ($this->checkForBytes([0x57, 0x45, 0x42, 0x50], 8)) {
            return [
                'ext' => 'webp',
                'mime' => 'image/webp'
            ];
        }
        return [
            'ext' => null,
            'mime' => null
        ];
    }

    protected function checkForBytes(array $bytes, int $offset = 0, array $mask = []): bool
    {
        if (empty($bytes) || empty($this->byteCache)) {
            return false;
        }

        // make sure we have numeric indices
        $bytes = array_values($bytes);

        foreach ($bytes as $i => $byte) {
            if (!empty($mask)) {
                if (
                    !isset($this->byteCache[$offset + $i], $mask[$i]) ||
                    $byte !== ($mask[$i] & $this->byteCache[$offset + $i])
                ) {
                    return false;
                }
            } elseif (!isset($this->byteCache[$offset + $i]) || $this->byteCache[$offset + $i] !== $byte) {
                return false;
            }
        }

        return true;
    }

    public function getHash(string $str): string
    {
        return hash('crc32b', $str);
    }


}
