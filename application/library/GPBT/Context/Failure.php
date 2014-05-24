<?php
namespace GPBT\Context;

class Failure extends AbstractBuildFinish
{
    protected function getGithubCommentBody()
    {
        return $this->settings->github->comment->failure;
    }
}