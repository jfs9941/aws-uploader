<?php

namespace Jfs\Uploader\Exposed\Jobs;

interface BlurJobInterface
{
    public function blur(string $id): void;
}
