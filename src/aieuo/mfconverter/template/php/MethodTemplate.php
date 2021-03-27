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
    /* @var array */
    private $uses;
    /** @var bool */
    private $static;

    public function __construct(string $name, array $args, CodesTemplate $codes, array $uses = [], bool $isStatic = false, int $indent = 0) {
        parent::__construct($indent);
        $this->name = $name;
        $this->args = $args;
        $this->codes = $codes;
        $this->uses = $uses;
        $this->static = $isStatic;
    }

    public function addCode(string $code, array $uses = []): void {
        $this->codes->addLine($code);
        foreach ($uses as $use) {
            $this->uses[] = $use;
        }
        $this->uses = array_unique($this->uses);
    }

    public function getUseClasses(): array {
        return $this->uses;
    }

    public function getLines(): array {
        return [
            'public'.($this->static ? " static" : "").' function '.$this->name.'('.implode(", ", $this->args).') {',
            $this->codes->getLines(),
            '}'
        ];
    }
}