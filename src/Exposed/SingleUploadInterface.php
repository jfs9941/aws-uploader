<?php
declare(strict_types=1);

namespace Jfs\Uploader\Exposed;

interface SingleUploadInterface
{
    public function getFile();

    public function options();
}
