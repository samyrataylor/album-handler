<?php

namespace SamyraTaylor\AlbumHandler\Connectors\Shell;

class BackgroundProcess extends Process
{
    protected(set) string $pipeOutput = '/dev/null';

    protected(set) int $pid;

    public function pipeOutput(?string $output): static
    {
        $this->pipeOutput = $output ?? '/dev/null';
        return $this;
    }

    public function output(): array
    {
        $this->updateState();
        return parent::output();
    }

    public function updateState(): static
    {
        $this->loadOutput();
        if (!is_null($this->timeStarted) && is_null($this->timeStopped) && posix_getpgid($this->pid) === false) {
            $this->timeStopped = time();

            if ($this->outputFileExists()) {
                unlink($this->pipeOutput);
            }
        }
        return $this;
    }

    protected function loadOutput(): void
    {
        if ($this->outputFileExists()) {
            $this->output = explode(PHP_EOL, trim(file_get_contents($this->pipeOutput)));
        }
    }

    protected function outputFileExists(): bool
    {
        return $this->hasOutput() && file_exists($this->pipeOutput);
    }

    public function hasOutput(): bool
    {
        return $this->pipeOutput !== '/dev/null';
    }

    public function stopped(): bool
    {
        $this->updateState();
        return parent::stopped();
    }

    public function kill(): static
    {
        if ($this->running()) {
            posix_kill($this->pid, SIGTERM);
            usleep(10000);
            $this->updateState();
        }
        return $this;
    }

    public function running(): bool
    {
        $this->updateState();
        return parent::running();
    }

    public function getExecutedProcess(): ?ExecutedProcess
    {
        if (!$this->started()) {
            $this->run();
        }

        if ($this->executed()) {
            return new ExecutedProcess(
                $this->realCommand,
                $this->output,
                $this->timeStarted,
                $this->timeStopped,
                $this->returnCode,
            );
        }

        return null;
    }

    public function started(): bool
    {
        $this->updateState();
        return parent::started();
    }

    public function run(): static
    {
        if (!$this->executed() && !$this->running()) {
            $this->pid = (int)$this->execute(true)[0];
            $this->timeStarted = time();
        }

        return $this;
    }

    public function executed(): bool
    {
        $this->updateState();
        return parent::executed();
    }

    protected function buildCommand(): string
    {
        return sprintf('%s > "%s" 2>&1 & echo $!', $this->command, $this->pipeOutput);
    }


}