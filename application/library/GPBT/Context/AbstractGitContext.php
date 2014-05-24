<?php
namespace GPBT\Context;

abstract class AbstractGitContext extends AbstractContext
{
    protected function getGitCommand()
    {
        $path = $this->settings->workspace;
        return "/usr/bin/git --git-dir=$path/.git --work-tree=$path";
    }
}