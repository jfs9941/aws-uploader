<?php

namespace Jfs\Uploader\Exposed\Jobs;

interface StoreToS3JobInterface
{
    public function store(string $id): void;
}
