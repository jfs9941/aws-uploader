<?php

namespace Jfs\Uploader\Exposed\Jobs;

interface StoreVideoToS3JobInterface
{
    public function store(string $id);
}
