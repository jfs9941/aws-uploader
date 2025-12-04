<?php

namespace Jfs\Uploader\Exposed\Jobs;

interface GenerateThumbnailJobInterface
{
    public function generate(string $id);
}
