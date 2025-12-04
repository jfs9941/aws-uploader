<?php

namespace Jfs\Uploader\Exposed\Jobs;

interface CompressJobInterface
{
    public function compress(string $id);
}
