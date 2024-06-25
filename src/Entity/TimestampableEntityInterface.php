<?php

namespace App\Entity;

interface TimestampableEntityInterface
{
    public function getCreatedAt(): \DateTimeInterface;
    public function getUpdatedAt(): \DateTimeInterface;
}
