<?php

namespace SamyraTaylor\AlbumHandler\Connectors\Shell;

readonly class ExecutedProcess
{
    public function __construct(
        public string $command,
        public array $output,
        public int $started,
        public int $stopped,
        public ?int $returnCode,
    ) {}

    public function output(): array
    {
        return $this->output;
    }

}