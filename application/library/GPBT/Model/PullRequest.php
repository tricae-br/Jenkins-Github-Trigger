<?php
namespace GPBT\Model;

class PullRequest extends AbstractModel
{
    protected $url;
    protected $id;
    protected $number;
    protected $title;
    protected $body;
    protected $state;
    protected $created_at;
    protected $updated_at;
    protected $head;
    protected $base;

    public function setData(array $data = array())
    {
        parent::setData($data);
        $this->created_at = new \DateTime($this->created_at);
        $this->updated_at = new \DateTime($this->updated_at);
        $head             = $this->head;
        $base             = $this->base;
        $this->head       = new Branch;
        $this->base       = new Branch;
        $this->head->setData($head);
        $this->base->setData($base);
    }

    public function close()
    {
        $this->state = 'closed';
    }

    public function open()
    {
        $this->state = 'open';
    }
}