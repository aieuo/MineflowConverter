<?php

namespace aieuo\mfconverter\template\php;

use aieuo\mfconverter\template\Template;

class ClassTemplate extends Template {

    /** @var string */
    private $namespace;
    /** @var string */
    private $name;
    /* @var string[] */
    private $uses;
    /** @var MethodTemplate[] */
    private $methods;
    /* @var string|null */
    private $extends;
    /* @var array */
    private $implements;

    /**
     * @param string $namespace
     * @param string $name
     * @param string[] $uses
     * @param MethodTemplate[] $methods
     * @param string|null $extends
     * @param string[] $implements
     * @param int $indent
     */
    public function __construct(string $namespace, string $name, array $uses, array $methods, ?string $extends = null, array $implements = [], int $indent = 0) {
        parent::__construct($indent);
        $this->namespace = $namespace;
        $this->name = $name;
        $this->uses = $uses;
        $this->methods = $methods;
        $this->extends = $extends;
        $this->implements = $implements;
    }

    public function getLines(): array {
        $lines = [];
        $lines[] = "<?php";
        $lines[] = "";
        $lines[] = "namespace {$this->namespace};";
        $lines[] = "";
        foreach ($this->uses as $use) {
            $lines[] = "use {$use};";
        }
        $lines[] = "";

        $extends = empty($this->extends) ? "" : (" extends ".$this->extends);
        $implements = empty($this->implements) ? "" : (" implements ".implode(", ", $this->implements));
        $lines[] = 'class '.$this->name.$extends.$implements.' {';

        foreach ($this->methods as $method) {
            $lines[] = $method->getLines();
        }
        $lines[] = "}";
        return $lines;
    }
}