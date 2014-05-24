<?php
namespace GPBT\Context;

use GPBT\Model\PullRequest;

class Trigger extends AbstractGitContext
{
    protected function pullRequestFilter($pr)
    {
        $pattern = str_replace(array('.', '-'), array('\.', '\-'), $this->settings->targetBranch);
        $pattern = str_replace('*', '.*', $pattern);
        $pattern = "/^{$pattern}$/";
        if (preg_match($pattern, $pr['base']['ref'])) {
            return true;
        }
    }

    protected function fetchFromGithub()
    {
        $owner      = $this->settings->github->owner;
        $repository = $this->settings->github->repository;
        return $this->client->api('pull_request')->all($owner, $repository, 'open');
    }

    protected function validate()
    {
        return !file_exists($this->settings->lock);
    }

    public function notifyJenkins($branch)
    {
        $settings = $this->settings;
        $url = sprintf(
            "%s/job/%s/buildWithParameters?token=%s&%s=%s",
            $settings->jenkins->url,
            $settings->jenkins->project,
            $settings->jenkins->token,
            $settings->jenkins->branchVariable,
            $branch
        );
        $curl     = curl_init($url);
        curl_exec($curl);
        $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        return (bool)($http_status >= 200 && $http_status < 400);
    }

    protected function setLock($data)
    {
        file_put_contents($this->settings->lock, $data);
    }

    protected function buildObject($pullRequestData)
    {
        $pullRequest = new PullRequest;
        $pullRequest->setData($pullRequestData);
        return $pullRequest;
    }
    protected function pullRequestAlreadyBuilt($number)
    {
        return file_exists($this->settings->dataPath.DIRECTORY_SEPARATOR.$number);
    }

    protected function willBuild(PullRequest $candidate)
    {
        if(!$this->pullRequestAlreadyBuilt($candidate->number)){
            return true;
        }
        /** @var PullRequest $built */
        $built = unserialize(file_get_contents($this->settings->dataPath.DIRECTORY_SEPARATOR.$candidate->number));
        if($candidate->head->sha == $built->head->sha){
            return false;
        }
        return true;
    }
    public function __invoke()
    {
        if (!$this->validate()) {
            echo "Already on a Build Process", PHP_EOL;
            exit(0);
        }
        $openPullRequests = $this->fetchFromGithub();
        $prs              = array_filter($openPullRequests, array($this, 'pullRequestFilter'));
        $pullRequestData  = current($prs);
        if (!$pullRequestData) {
            echo 'Not eligible Pull Requests found', PHP_EOL;
            exit(0);
        }
        $pullRequest = $this->buildObject($pullRequestData);
        if(!$this->willBuild($pullRequest)){
            echo 'Build already Passed', PHP_EOL;
            exit(0);
        }
        $branch = $pullRequest->head->ref;
        if ($this->notifyJenkins($branch)) {
            $this->setLock("BRANCH={$branch}\nPULL_REQUEST={$pullRequest->number}");
            return $branch;
        }
        echo 'Problem requesting Jenkins', PHP_EOL;
        exit(251);
    }
}