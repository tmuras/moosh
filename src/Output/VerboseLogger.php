<?php
/**
 * moosh2 — Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh2\Output;

use Symfony\Component\Console\Output\OutputInterface;

/**
 * Provides rich, styled verbose output for tracing moosh2 execution.
 *
 * All methods are no-ops when verbosity is below VERBOSE (-v).
 * Uses Symfony Console formatting tags for coloured, structured output.
 */
final class VerboseLogger
{
    private OutputInterface $output;
    private float $startTime;

    public function __construct(OutputInterface $output)
    {
        $this->output = $output;
        $this->startTime = microtime(true);
    }

    /**
     * Whether verbose output is active.
     */
    public function isEnabled(): bool
    {
        return $this->output->isVerbose();
    }

    /**
     * Print a section header with a decorative border.
     */
    public function section(string $title): void
    {
        if (!$this->isEnabled()) {
            return;
        }

        $this->output->writeln('');
        $this->output->writeln(sprintf('<fg=cyan;options=bold>  ┌─ %s ─┐</>', $title));
    }

    /**
     * Print an action step — the main "what we're doing now" indicator.
     */
    public function step(string $message): void
    {
        if (!$this->isEnabled()) {
            return;
        }

        $elapsed = $this->elapsed();
        $this->output->writeln(sprintf(
            '  <fg=yellow>▸</> <options=bold>%s</> <fg=gray>[%s]</>',
            $message,
            $elapsed,
        ));
    }

    /**
     * Print a success/completion indicator.
     */
    public function done(string $message): void
    {
        if (!$this->isEnabled()) {
            return;
        }

        $this->output->writeln(sprintf('  <fg=green>✔</> %s', $message));
    }

    /**
     * Print a detail/info line (indented under the current step).
     */
    public function info(string $message): void
    {
        if (!$this->isEnabled()) {
            return;
        }

        $this->output->writeln(sprintf('    <fg=blue>ℹ</> <fg=white>%s</>', $message));
    }

    /**
     * Print a key-value detail.
     */
    public function detail(string $key, string $value): void
    {
        if (!$this->isEnabled()) {
            return;
        }

        $this->output->writeln(sprintf(
            '    <fg=gray>│</> <fg=white>%s:</> <fg=bright-cyan>%s</>',
            $key,
            $value,
        ));
    }

    /**
     * Print a warning.
     */
    public function warn(string $message): void
    {
        if (!$this->isEnabled()) {
            return;
        }

        $this->output->writeln(sprintf('  <fg=yellow>⚠</> <fg=yellow>%s</>', $message));
    }

    /**
     * Print a skip/not-applicable note.
     */
    public function skip(string $message): void
    {
        if (!$this->isEnabled()) {
            return;
        }

        $this->output->writeln(sprintf('  <fg=gray>⊘ %s</>', $message));
    }

    /**
     * Print a closing border for a section.
     */
    public function end(): void
    {
        if (!$this->isEnabled()) {
            return;
        }

        $elapsed = $this->elapsed();
        $this->output->writeln(sprintf('  <fg=cyan>└─ done in %s ─┘</>', $elapsed));
    }

    /**
     * Format elapsed time since logger creation.
     */
    private function elapsed(): string
    {
        $ms = (microtime(true) - $this->startTime) * 1000;

        if ($ms < 1000) {
            return sprintf('%.0fms', $ms);
        }

        return sprintf('%.2fs', $ms / 1000);
    }
}
