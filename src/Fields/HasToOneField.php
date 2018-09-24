<?php

namespace Tsukasa\Orm\Fields;

class HasToOneField extends HasManyField
{
    public function getValue()
    {
        return $this->getManager()->limit(1)->get();
    }
}