<?php

namespace Tsukasa\Orm\SlugFields;

use Tsukasa\Orm\Fields\CharField;
use Tsukasa\Traits\SlugifyTrait;

abstract class AbstractSlugField extends CharField
{
    use SlugifyTrait;

    /**
     * @var string
     */
    public $source = 'name';
}
