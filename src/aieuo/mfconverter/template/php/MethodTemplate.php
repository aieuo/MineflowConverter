<?php

namespace aieuo\mfconverter\template\php;

use aieuo\mfconverter\template\Template;

class MethodTemplate extends Template {

    /** @var string */
    private $name;
    /* @var array */
    private $args;
    /** @var CodesTemplate */
    private $codes;

    public function __construct(string $name, array $args, CodesTemplate $codes, int $indent = 0) {
        parent::__construct($indent);
        $this->name = $name;
        $this->args = $args;
        $this->codes = $codes;
    }

    public function getLines(): array {
        return [
            'public function '.$this->name.'('.implode(", ", $this->args).') {',
            $this->codes->getLines(),
            '}'
        ];
    }
}