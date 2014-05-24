<?php
namespace GPBT\Context;

use GPBT\Model\PullRequest;

class Current extends AbstractContext
{
    public function __invoke()
    {
        return file_get_contents($this->settings->lock)?:null;
    }
}