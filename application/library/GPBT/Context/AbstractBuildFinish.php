<?php
namespace GPBT\Context;

use GPBT\Model\PullRequest;

abstract class AbstractBuildFinish extends AbstractGitContext
{

    protected function locked()
    {
        return file_exists($this->settings->lock);
    }

    protected function unlock()
    {
        unlink($this->settings->lock);
    }

    protected function buildObject($pullRequestData)
    {
        $pullRequest = new PullRequest;
        $pullRequest->setData($pullRequestData);
        return $pullRequest;
    }

    protected function storePullRequest($number)
    {
        $owner       = $this->settings->github->owner;
        $repository  = $this->settings->github->repository;
        $data        = $this->client->api('pull_request')->show($owner, $repository, $number);
        $pullRequest = $this->buildObject($data);
        return (bool)file_put_contents(
            $this->settings->dataPath . DIRECTORY_SEPARATOR . $number,
            serialize($pullRequest)
        );
    }

    abstract protected function getGithubCommentBody();

    protected function commentOnPullRequest($number)
    {
        $owner      = $this->settings->github->owner;
        $repository = $this->settings->github->repository;
        $this->client->api('issue')->comments()->create(
            $owner,
            $repository,
            $number,
            array(
                'body' => $this->getGithubCommentBody(),
                'title' => 'Build Status'
            )
        );
    }

    public function __invoke()
    {
        if (!$this->locked()) {
            echo "Something already cleaned lock file", PHP_EOL;
            exit(250);
        }
        $lock = parse_ini_file($this->settings->lock);
        $this->storePullRequest($lock['PULL_REQUEST']);
        $this->commentOnPullRequest($lock['PULL_REQUEST']);
        $this->unlock();
        exit(0);
    }
}