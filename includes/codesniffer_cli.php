<?php

class codesniffer_cli extends PHP_CodeSniffer_CLI {

    private $report = 'full';
    private $reportfile = null;

    /** Constructor */
    public function __construct() {
        // Horrible, cannot be set programatically.
        $this->errorSeverity = 1;
        $this->warningSeverity = 1;
    }

    /** Set the report to use */
    public function setReport($report) {
        $this->report = $report;
    }

    /** Set the reportfile to use */
    public function setReportFile($reportfile) {
        $this->reportfile = $reportfile;
    }

    /* Overload method to inject our settings */
    public function getCommandLineValues() {

        // Inject our settings to defaults.
        $defaults = array_merge(
            $this->getDefaults(),
            array(
                'reports' => array($this->report => $this->reportfile),
            )
        );
        return $defaults;
    }
}
