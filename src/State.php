<?php

namespace CipherBsk;

class State
{
    private ?int $rand = null; // Can now store a closure
    private ?int $max = null;

    // Getter and setter for `rand`
    public function getRand(): ?int
    {
        if ($this->rand === null) {
            throw new \RuntimeException("Rand is not set.");
        }
        return $this->rand;
    }

    public function setRand(int $rand): void
    {
        $this->rand = $rand;
    }
    
    // Getter and setter for `max`
    public function getMax(): ?int
    {
        return $this->max;
    }

    public function setMax(?int $max): void
    {
        $this->max = $max;
    }
    
    public function reset(): void
    {
        $this->rand = null;
        $this->max = null;
    }
}
