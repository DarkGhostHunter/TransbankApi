<?php

namespace DarkGhostHunter\TransbankApi\Transactions\Concerns;

trait HasSecrets
{
    /**
     * If it should hide secrets
     *
     * @var bool
     */
    protected $hideSecrets = true;

    /**
     * Show Secrets on serialization
     *
     * @return $this
     */
    public function showSecrets()
    {
        $this->hideSecrets = false;
        return $this;
    }

    /**
     * Hides Secrets on serialization
     *
     * @return $this
     */
    public function hideSecrets()
    {
        $this->hideSecrets = true;
        return $this;
    }

    /**
     * If the current instance is hiddng it's secrets
     *
     * @return bool
     */
    public function isHidingSecrets()
    {
        return $this->hideSecrets;
    }

}