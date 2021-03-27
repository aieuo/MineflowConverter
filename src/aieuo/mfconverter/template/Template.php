<?php

namespace aieuo\mfconverter\template;

abstract class Template {

    /* @var int */
    private $baseIndent;

    public function __construct(int $baseIndent) {
        $this->baseIndent = $baseIndent;
    }

    protected function formatLines(array $codes, int $indent = 0): string {
        $result = [];
        foreach ($codes as $code) {
            if (is_array($code)) {
                $result[] = $this->formatLines($code, $indent + 1);
            } else {
                $result[] = str_repeat(" ", $indent * 4).$code;
            }
        }
        return implode("\n", $result);
    }

    public function format(): string {
        return $this->formatLines($this->getLines(), $this->getBaseIndent());
    }

    public function getBaseIndent(): int {
        return $this->baseIndent;
    }

    abstract public function getLines(): array;
}