<?php
namespace GPBT\Context;

use Github\Client;

abstract class AbstractContext
{
    protected $client;
    protected $settings;
    protected $option;

    public function __construct(Client $client, $settings, $option = null)
    {
        $this->client   = $client;
        $this->settings = $settings;
        $this->option   = $option;
    }

    abstract function __invoke();
}