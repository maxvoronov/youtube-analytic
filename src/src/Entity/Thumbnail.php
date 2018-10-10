<?php declare(strict_types=1);

namespace App\Entity;

class Thumbnail
{
    protected $type;
    protected $imageUrl;
    protected $width;
    protected $height;

    public function __construct(string $type, string $imageUrl, int $width = 0, int $height = 0)
    {
        $this->type = $type;
        $this->imageUrl = $imageUrl;
        $this->width = $width;
        $this->height = $height;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getImageUrl(): string
    {
        return $this->imageUrl;
    }

    /**
     * @return int
     */
    public function getWidth(): int
    {
        return $this->width;
    }

    /**
     * @return int
     */
    public function getHeight(): int
    {
        return $this->height;
    }
}
