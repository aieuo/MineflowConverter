<?php

namespace aieuo\mfconverter\converter;

use aieuo\mfconverter\Main;
use pocketmine\plugin\PluginLogger;

abstract class Converter {

    /* @var Main */
    private $owner;
    /* @var string */
    private $baseDir;
    /** @var string[] */
    private $templates = [];

    public function __construct(Main $owner, string $dir) {
        $this->owner = $owner;
        $this->baseDir = $dir;
        if (!file_exists($this->baseDir)) @mkdir($this->baseDir, 0777, true);
    }

    protected function getLogger(): PluginLogger {
        return $this->owner->getLogger();
    }

    public function getBaseDir(): string {
        return $this->baseDir;
    }
}