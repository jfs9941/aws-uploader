<?php

namespace Jfs\Uploader\Exposed\Jobs;

interface GenerateThumbnailForVideoInterface
{
    public function generate(string $id): void;
}
