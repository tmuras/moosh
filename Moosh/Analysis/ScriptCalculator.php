<?php


namespace Moosh\Analysis;


class ScriptCalculator
{
    private $globalcount = 0;
    private $name;
    private $daily = [];
    private $hourly = [];
    private $cwd;

    public function __construct($name, \DateTime $from, \DateTime $to)
    {
        $this->name = $name;
        $this->cwd = getcwd();

        // Fill in daily and hourly statistics with "0"s so we have no gaps.
        $day = new \DateInterval('P1D');
        $time = clone $from;
        while($time <= $to) {
            $dayindex = $time->format('z');
            $this->daily[$dayindex] = ['count' => 0, 'sum' => 0, 'max' => 0, 'date' => clone $time];
            $time->add($day);
        }
        
        $hour = new \DateInterval('PT1H');
        $time = clone $from;
        while($time <= $to) {
            $hourindex = $time->format('Y-m-d:H');
            $this->hourly[$hourindex] = ['count' => 0, 'sum' => 0, 'max' => 0, 'date' => clone $time];
            $time->add($hour);
        }
    }

    /**
     * Add the value of $users
     *
     * @param $date
     * @param $users
     */
    public function add(\DateTime $datetime, $requests)
    {
        // Global average.
        $this->globalcount += $requests;

        // Let's store each day separately.
        // z - The day of the year 0 - 365.
        $dayyear = $datetime->format('z');

        if (!isset($this->daily[$dayyear])) {
            $this->daily[$dayyear] = ['count' => 0, 'sum' => 0, 'max' => 0, 'date' => $datetime];
        }
        $this->daily[$dayyear]['count']++;
        $this->daily[$dayyear]['sum'] += $requests;

        // Each hour separately
        $hour = $datetime->format('Y-m-d:H');
        if (!isset($this->hourly[$hour])) {
            $this->hourly[$hour] = ['count' => 0, 'sum' => 0, 'max' => 0, 'date' => $datetime];
        }
        $this->hourly[$hour]['count']++;
        $this->hourly[$hour]['sum'] += $requests;

    }

    public function get_days() {
        return $this->daily;
    }

    public function get_hours() {
        return $this->hourly;
    }

    public function get_stats()
    {
        ksort($this->daily);
        ksort($this->hourly);

        echo "Total number of requests: " . $this->globalcount . "\n";
        $this->dump_csv_files();
    }

    public function dump_csv_files()
    {
        $filepath = $this->cwd . '/' . str_replace('/', '_', $this->name) . '-requests-per-day.csv';
        $csvfile = fopen($filepath, 'w');
        if (!$csvfile) {
            cli_error("Can't open '$filepath' for writing");
        }
        // Header.
        fputcsv($csvfile, ['date', 'number of requests']);
        foreach ($this->daily as $day) {
            fputcsv($csvfile, [$day['date']->format('Y-m-d'), $day['sum']]);
        }
        fclose($csvfile);

        $filepath = $this->cwd . '/' . str_replace('/', '_', $this->name) . '-requests-per-hour.csv';
        $csvfile = fopen($filepath, 'w');
        if (!$csvfile) {
            cli_error("Can't open '$filepath' for writing");
        }
        // Header.
        fputcsv($csvfile, ['date', 'number of requests']);
        foreach ($this->hourly as $hour) {
            fputcsv($csvfile, [$hour['date']->format('Y-m-d H'), $hour['sum']]);
        }
        fclose($csvfile);

    }


}