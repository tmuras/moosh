<?php

namespace Moosh;


class Performance {
    public $startreadable;
    public $starttime;
    public $startptimes;
    public $serverloadstart;

    public $endreadable;
    public $endtime;
    public $endptimes;
    public $serverloadend;

    public $startmemorytotal;
    public $startmemorytotalreal;
    public $endmemorytotal;
    public $endmemorytotalreal;
    public $memorypeak;
    public $memorypeakreal;

    public $realtime;
    public $ticks;

    public function start() {
        $this->startreadable = date(DATE_ATOM);
        $this->starttime = microtime(true);
        $this->startptimes = posix_times();
        $this->serverloadstart = $this->get_load();
        $this->startmemorytotal = memory_get_usage();
        $this->startmemorytotalreal = memory_get_usage(true);
    }

    public function stop() {
        $this->endreadable = date(DATE_ATOM);
        $this->endtime = microtime(true);
        $this->endptimes = posix_times();
        $this->serverloadend = $this->get_load();
        $this->realtime = $this->endtime - $this->starttime;
        $this->endmemorytotal = memory_get_usage();
        $this->endmemorytotalreal = memory_get_usage(true);
        $this->memorypeak = memory_get_peak_usage();
        $this->memorypeakreal = memory_get_peak_usage(true);

        $this->stopptimes = posix_times();
        $info = array();
        foreach ($this->stopptimes as $key => $val) {
            $info[$key] = $this->stopptimes[$key] - $this->startptimes[$key];
        }
        $this->ticks = "Ticks: $info[ticks] user: $info[utime] sys: $info[stime] cuser: $info[cutime] csys: $info[cstime] ";
    }

    private function get_load() {
        if (is_readable('/proc/loadavg') && $serverload = @file('/proc/loadavg')) {
            return trim($serverload[0]);
        }
        return null;
    }

    public function summary() {
        $content = array();
        $content[] = "*** PERFORMANCE INFORMATION ***";
        $content[] = "Run from " . $this->startreadable . " to " . $this->endreadable;
        $content[] = sprintf("Real time run %.3f seconds", $this->realtime);
        $content[] = "Server load before running the command: " . $this->serverloadstart;
        $content[] = "Server load after: " . $this->serverloadend;
        $content[] = $this->ticks;
        $content[] = "Memory use before command run (internal/real): {$this->startmemorytotal}/{$this->startmemorytotalreal} ".  '(' . $this->bytes_format($this->startmemorytotal). '/' . $this->bytes_format($this->startmemorytotalreal) . ')';
        $content[] = "Memory use after:  {$this->endmemorytotal}/{$this->endmemorytotalreal} ".  '(' . $this->bytes_format($this->endmemorytotal). '/' . $this->bytes_format($this->endmemorytotalreal) . ')';
        $content[] = "Memory peak: {$this->memorypeak}/{$this->memorypeakreal} " . ' (' . $this->bytes_format($this->memorypeak). '/' . $this->bytes_format($this->memorypeakreal) . ')';
        $content[] = "*******************************";
        return implode("\n", $content) . "\n";
    }

    protected function bytes_format($bytes) {
        $units = array('B' => 0, 'KB' => 1, 'MB' => 2, 'GB' => 3, 'TB' => 4);

        $value = 0;
        if ($bytes > 0) {
            $pow = floor(log($bytes) / log(1024));
            $unit = array_search($pow, $units);
            $value = ($bytes / pow(1024, floor($units[$unit])));
        }

        return sprintf('%.2f ' . $unit, $value);
    }

}