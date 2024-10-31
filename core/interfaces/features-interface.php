<?php
namespace MetSalesCountdown\Core\Interfaces;

interface Features_Interface {
    public function name(): string;

    public function init(): void;
}