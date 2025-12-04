<?php

namespace Jfs\Uploader\Exposed\Jobs;

interface MediaEncodeJobInterface
{
    public function encode(string $id, string $username, $forceCheckAccelerate);
}
