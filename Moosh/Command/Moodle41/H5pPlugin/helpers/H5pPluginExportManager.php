<?php

namespace Moosh\Command\Moodle41\H5pPlugin;

use \mod_hvp\framework;

/**
 * Class manages H5pPlugin data export
 * @author Michal Chruscielski <michalch775@gmail.com>
 */
class H5pPluginExportManager {

    /**
     * Exports H5p libraries and saves it to csv file with chosen name.
     * @param string $filename expected filename
     * @param bool $verbose verbose enabled
     * @return void
     */
    public function exportLibraries($filename, $verbose) {
        if($verbose) {
            mtrace("Attempting H5p library export");
        }

        $this->export($filename, $verbose, false);
    }

    /**
     * Exports H5p content types and saves it to csv file with chosen name.
     * @param string $filename expected filename
     * @param bool $verbose verbose enabled
     * @return void
     */
    public function exportContentTypes($filename, $verbose) {
        if($verbose) {
            mtrace("Attempting H5p content types export");
        }

        $this->export($filename, $verbose, true);
    }

    /**
     * Exports libraries/content types to csv and saves it
     * @param string $filename target csv file name
     * @param bool $verbose verbose enabled
     * @param bool $runnable mapping object types: 0 - libraries, 1 - content types
     * @return void
     */
    private function export($filename, $verbose, $runnable){
        global $CFG;

        // appending extension
        if(!string_ends_with($filename, '.csv') && !string_ends_with($filename, '.txt')) {
            $filename .= ".csv";
        }

        if($verbose) {
            mtrace("Loading H5p data attempt.");
        }

        if(!class_exists(framework::class)) {
            cli_error("H5p factory class cannot be loaded. Check if H5p plugin is properly installed and configured.");
        }

        // Using factory object instead of fetching from database. It provides actual data fetching,
        // which is used by moodle anyway.
        $core = framework::instance();

        $libraries = $core->h5pF->loadLibraries();

        if($verbose) {
            mtrace("H5p content loaded. Exporting to csv.");
        }

        require_once($CFG->libdir . '/csvlib.class.php');


        // our csv fields
        $fields = array(
            "title" => "Title",
            "restricted" => "Restricted",
            "version" => "Version",
        );

        // fields converted to contain only expected key names
        $expected_fields = array_keys($fields);

        // setting filename and writing csv header
        $csv_writer = new \csv_export_writer();
        $csv_writer->set_filename($filename);
        $csv_writer->add_data($fields);

        // appending libraries (or content types)
        $row_count = 0;
        foreach ($libraries as $librarytype) {
            foreach($librarytype as $library) {
                if ($verbose) {
                    mtrace("Exporting object.");
                }

                // in case of unexpected data structure we don't want any thrown errors
                if (!is_object($library)) {
                    mtrace("Cannot load one of the objects - skipping.");
                    continue;
                }

                // check object type
                // runnable 0 - library, runnable 1 - content type
                if ($library->runnable != (int)$runnable) {
                    if ($verbose) {
                        $expected_runnable = (int)$runnable;
                        mtrace("Skipping $library->title, cause of runnable: $library->runnable, expected: $expected_runnable.");
                    }

                    // skipping
                    continue;
                }

                if ($verbose) {
                    mtrace("Mapping object $library->title.");
                }

                // mapping to avoid duplicating expected fields values
                $row = $this->mapCsvRow($library, $expected_fields, $verbose);

                $csv_writer->add_data($row);
                $row_count++;

                if ($verbose) {
                    mtrace("Object $library->title mapped.");
                }
            }
        }

        if($verbose) {
            mtrace("Exporting csv to file.");
        }

        file_put_contents($filename, $csv_writer->print_csv_data(true));

        mtrace("Exported file $filename with $row_count elements.");

    }

    /**
     * Function maps object to array, which may be later used as csv row.
     * @param object $object_to_map object we want to map
     * @param string[] $expected_fields expected fields names
     * @param bool $verbose verbose mode
     * @return array mapped object
     */
    private function mapCsvRow($object_to_map, $expected_fields, $verbose)
    {
        $row = array();

        foreach($expected_fields as $object_key) {
            if(property_exists($object_to_map, $object_key)) {
                // directly mapping property
                $row[$object_key] = $object_to_map->$object_key;
            } else if($object_key === "version") {
                // version field must be formatted
                $row[$object_key] = $object_to_map->major_version .".". $object_to_map->minor_version .".". $object_to_map->patch_version;
            } else {
                // skipping field, adding empty string to preserve csv consistency
                $row[$object_key] = "";

                if($verbose) {
                    mtrace("Cannot map field $object_key for $object_to_map->title.");
                }
            }
        }

        return $row;
    }
}