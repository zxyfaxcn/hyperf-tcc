<?php

namespace Hyperf\Tcc;

interface TccInterface
{
    public function try();
    public function confirm();
    public function cancel();
}