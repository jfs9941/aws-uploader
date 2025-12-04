<?php

namespace Jfs\Uploader\Exposed\Jobs;

interface WatermarkTextJobInterface
{
    public function putWatermark(string $id, string $username);
}
