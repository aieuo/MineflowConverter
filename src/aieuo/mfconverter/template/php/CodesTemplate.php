<?php

namespace aieuo\mfconverter\template\php;

use aieuo\mfconverter\template\Template;

class CodesTemplate extends Template {

    /* @var array */
    private $codes;

    public function __construct(array $codes, int $indent = 0) {
        parent::__construct($indent);
        $this->codes = $codes;
    }

    public function getLines(): array {
        return $this->codes;
    }
}