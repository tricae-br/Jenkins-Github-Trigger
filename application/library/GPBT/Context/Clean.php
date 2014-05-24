<?php
namespace GPBT\Context;

class Clean extends AbstractContext
{
    public function __invoke()
    {
        if (file_exists($this->settings->lock)) {
            unlink($this->settings->lock);
        }
    }
}