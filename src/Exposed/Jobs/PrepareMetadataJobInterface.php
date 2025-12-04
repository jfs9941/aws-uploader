<?php

namespace Jfs\Uploader\Exposed\Jobs;

interface PrepareMetadataJobInterface
{
    public function prepareMetadata(string $id): void;
}
