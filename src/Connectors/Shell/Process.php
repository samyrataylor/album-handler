<?php

namespace SamyraTaylor\AlbumHandler\Connectors\Shell;

class Process
{
    protected(set) ?int $timeStarted = null;
    protected(set) ?int $timeStopped = null;
    protected(set) string $realCommand;
    protected array $output = [];

    protected(set) ?int $returnCode;

    public function __construct(
        protected(set) string $command,
    ) {}

    public function runTime(): ?int
    {
        if ($this->executed()) {
            return $this->timeStopped - $this->timeStarted;
        }
        return null;
    }

    public function executed(): bool
    {
        return $this->started() && $this->stopped();
    }

    public function started(): bool
    {
        return !is_null($this->timeStarted);
    }

    public function stopped(): bool
    {
        return !is_null($this->timeStopped);
    }

    public function output(): array
    {
        return $this->output;
    }

    public function getExecutedProcess(): ?ExecutedProcess
    {
        $this->run();
        return new ExecutedProcess(
            $this->realCommand,
            $this->output,
            $this->timeStarted,
            $this->timeStopped,
            $this->returnCode,
        );
    }

    public function run(): static
    {
        if (!$this->executed() && !$this->running()) {
            $this->timeStarted = time();

            $result = $this->execute();
            $this->returnCode = $result['returnCode'];
            $this->output = $result['output'];

            $this->timeStopped = time();
        }

        return $this;
    }

    public function running(): bool
    {
        return $this->started() && !$this->stopped();
    }

    protected function execute(bool $outputOnly = false): array
    {
        $this->realCommand ??= $this->buildCommand();

        exec($this->realCommand, $output, $returnCode);

        if($outputOnly) {
            return $output;
        }

        return [
            'returnCode' => $returnCode,
            'output' => $output,
        ];
    }

    protected function buildCommand(): string
    {
        return sprintf('%s 2>&1', $this->command);
    }
}