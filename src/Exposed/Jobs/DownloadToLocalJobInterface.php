<?php

namespace Jfs\Uploader\Exposed\Jobs;

interface DownloadToLocalJobInterface
{
    public function download(string $id): void;
}
