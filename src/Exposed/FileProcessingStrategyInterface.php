<?php
declare(strict_types=1);

namespace Jfs\Uploader\Exposed;

interface FileProcessingStrategyInterface
{
    public function process(int $toStatus);
}
