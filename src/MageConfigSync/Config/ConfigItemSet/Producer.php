<?php

namespace MageConfigSync\Config\ConfigItemSet;

interface Producer
{
    public function generateConfigItemSet();
}