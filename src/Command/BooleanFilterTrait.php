<?php
/**
 * moosh2 — Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh2\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;

/**
 * Adds --is and --is-not boolean filter options to a command.
 *
 * Usage:
 *   moosh course:list --is visible --is-not empty
 *
 * Classes using this trait must implement supportedBooleanFlags().
 */
trait BooleanFilterTrait
{
    /**
     * Return supported boolean flag names with descriptions.
     *
     * @return array<string, string> e.g. ['visible' => 'Course is visible', 'empty' => 'Course has no content']
     */
    abstract protected function supportedBooleanFlags(): array;

    /**
     * Register --is and --is-not options on the command.
     */
    protected function configureBooleanFilters(Command $command): void
    {
        $flags = implode(', ', array_keys($this->supportedBooleanFlags()));

        $command
            ->addOption('is', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, "Include items matching a flag ($flags)")
            ->addOption('is-not', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, "Exclude items matching a flag ($flags)");
    }

    /**
     * Parse --is and --is-not into a map of flag => bool|null.
     *
     * @return array<string, bool|null> true = --is, false = --is-not, null = unspecified
     *
     * @throws \InvalidArgumentException on unknown flags or contradictions
     */
    protected function parseBooleanFilters(InputInterface $input): array
    {
        $supported = $this->supportedBooleanFlags();
        $isValues = $input->getOption('is');
        $isNotValues = $input->getOption('is-not');

        // Validate flag names.
        foreach ($isValues as $flag) {
            if (!array_key_exists($flag, $supported)) {
                throw new \InvalidArgumentException("Unknown flag '$flag' for --is. Supported: " . implode(', ', array_keys($supported)));
            }
        }
        foreach ($isNotValues as $flag) {
            if (!array_key_exists($flag, $supported)) {
                throw new \InvalidArgumentException("Unknown flag '$flag' for --is-not. Supported: " . implode(', ', array_keys($supported)));
            }
        }

        // Detect contradictions.
        $contradictions = array_intersect($isValues, $isNotValues);
        if ($contradictions) {
            throw new \InvalidArgumentException("Flag(s) '" . implode("', '", $contradictions) . "' cannot appear in both --is and --is-not");
        }

        // Build result map.
        $result = [];
        foreach ($supported as $flag => $description) {
            if (in_array($flag, $isValues, true)) {
                $result[$flag] = true;
            } elseif (in_array($flag, $isNotValues, true)) {
                $result[$flag] = false;
            } else {
                $result[$flag] = null;
            }
        }

        return $result;
    }
}
