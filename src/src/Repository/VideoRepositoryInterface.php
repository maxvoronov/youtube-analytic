<?php declare(strict_types=1);

namespace App\Repository;

use App\Entity\Video;

interface VideoRepositoryInterface
{
    public function get(string $id): Video;

    public function add(Video $video): void;

    public function save(Video $video, bool $upsert = true): void;

    public function remove(Video $video): void;
}
