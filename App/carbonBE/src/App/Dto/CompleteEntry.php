<?php

namespace App\Dto;

use App\Entity\Factor\Factor;
use App\Entity\Gas\Gas;
use App\Enum\Consumption;
use App\Enum\GasActivity;
use Core\Entity\User\User;

class CompleteEntry
{
    private User $user;

    private Factor $factor;

    private Gas $gas;

    private ?GasActivity $gasActivity = null;

    private ?Consumption $consumption = null;

    private float $factorAmount;

    private float $gasValue;

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getFactor(): Factor
    {
        return $this->factor;
    }

    public function setFactor(Factor $factor): void
    {
        $this->factor = $factor;
    }

    public function getGas(): Gas
    {
        return $this->gas;
    }

    public function setGas(Gas $gas): self
    {
        $this->gas = $gas;

        return $this;
    }

    public function getGasActivity(): ?GasActivity
    {
        return $this->gasActivity;
    }

    public function setGasActivity(?GasActivity $gasActivity): self
    {
        $this->gasActivity = $gasActivity;

        return $this;
    }

    public function getConsumption(): ?Consumption
    {
        return $this->consumption;
    }

    public function setConsumption(?Consumption $consumption): self
    {
        $this->consumption = $consumption;

        return $this;
    }

    public function getFactorAmount(): float
    {
        return $this->factorAmount;
    }

    public function setFactorAmount(float $factorAmount): self
    {
        $this->factorAmount = $factorAmount;

        return $this;
    }

    public function getGasValue(): float
    {
        return $this->gasValue;
    }

    public function setGasValue(float $gasValue): self
    {
        $this->gasValue = $gasValue;

        return $this;
    }
}
