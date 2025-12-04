<?php

namespace Jfs\Uploader\Exposed\Jobs;

interface BlurVideoJobInterface
{
    public function blur(string $id): void;
}
