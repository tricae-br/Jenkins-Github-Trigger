<?php
namespace GPBT\Context;

class Success extends AbstractBuildFinish
{
    protected function getGithubCommentBody()
    {
        return $this->settings->github->comment->success;
    }
}