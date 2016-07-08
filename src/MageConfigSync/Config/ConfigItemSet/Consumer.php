<?php

namespace MageConfigSync\Config\ConfigItemSet;

use MageConfigSync\Config\ConfigItemSet;

interface Consumer
{
    public function processConfigItemSet(ConfigItemSet $configItemSet);
}