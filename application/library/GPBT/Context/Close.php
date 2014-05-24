<?php
namespace GPBT\Context;

use GPBT\Model\PullRequest;

class Close extends AbstractGitContext
{
    protected function findPullRequest($id)
    {
        $pullRequestData = $this->client->api('pull_request')->show('tricae-br', 'master', $id);
        if (!$pullRequestData) {
            return;
        }
        $pullRequest = new PullRequest;
        $pullRequest->setData($pullRequestData);
        return $pullRequest;
    }

    protected function backToIntegration()
    {
        $git = $this->getGitCommand();
        shell_exec("$git reset --hard");
        shell_exec("$git checkout integration");
        shell_exec("$git pull");
    }

    protected function closePullRequest(PullRequest $pullRequest)
    {
        $uri      = "repos/tricae-br/master/pulls/$pullRequest->number";
        $body     = json_encode(array('state' => 'closed'));
        $data     = $this->client->getHttpClient()->patch($uri, $body);
        $response = $data->getBody()->getCustomData('default');
        if ($response) {
            $pullRequest->close();
        }
    }

    protected function cleanLock()
    {
        unlink($this->settings->lock);
    }

    public function __invoke()
    {
        $this->backToIntegration();
        $pullRequest = $this->findPullRequest($this->option);
        $this->closePullRequest($pullRequest);
        if ($pullRequest->state == 'closed') {
            $this->cleanLock();
            return "Pull Request #{$pullRequest->number} successfully closed";
        }
        echo "Something wrong: Pull Request #{$pullRequest->number} still open", PHP_EOL;
        die(252);
    }
}