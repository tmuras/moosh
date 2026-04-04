<?php
/**
 * moosh2 — Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh2\Command\Webservice;

use Moosh2\Command\BaseHandler;
use Moosh2\Output\VerboseLogger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * webservice:call implementation for Moodle 5.1.
 */
class WebserviceCall52Handler extends BaseHandler
{
    public function configureCommand(Command $command): void
    {
        $command
            ->addArgument('function', InputArgument::REQUIRED, 'Webservice function name (e.g. core_webservice_get_site_info)')
            ->addArgument('params', InputArgument::IS_ARRAY | InputArgument::OPTIONAL, 'Parameters as key=value pairs')
            ->addOption('token', null, InputOption::VALUE_REQUIRED, 'Webservice token')
            ->addOption('post', null, InputOption::VALUE_NONE, 'Use POST instead of GET')
            ->addOption('format', 'f', InputOption::VALUE_REQUIRED, 'Response format: json or xml', 'json')
            ->addOption('raw-params', null, InputOption::VALUE_REQUIRED, 'Raw query string parameters')
            ->addOption('pretty', null, InputOption::VALUE_NONE, 'Pretty-print JSON response');
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        global $CFG;

        $verbose = new VerboseLogger($output);

        $function = $input->getArgument('function');
        $paramPairs = $input->getArgument('params');
        $token = $input->getOption('token');
        $usePost = $input->getOption('post');
        $format = $input->getOption('format');
        $rawParams = $input->getOption('raw-params');
        $pretty = $input->getOption('pretty');

        if ($token === null) {
            $output->writeln('<error>A webservice token is required (--token).</error>');
            return Command::FAILURE;
        }

        if (!in_array($format, ['json', 'xml'], true)) {
            $output->writeln("<error>Invalid format '$format'. Use 'json' or 'xml'.</error>");
            return Command::FAILURE;
        }

        // Build the base URL.
        $baseUrl = $CFG->wwwroot . '/webservice/rest/server.php';

        // Build query parameters.
        $queryParams = [
            'wstoken' => $token,
            'wsfunction' => $function,
            'moodlewsrestformat' => $format,
        ];

        $queryString = http_build_query($queryParams, '', '&');

        // Add user-supplied parameters.
        if (!empty($paramPairs)) {
            foreach ($paramPairs as $pair) {
                $queryString .= '&' . $pair;
            }
        }

        if ($rawParams !== null) {
            $queryString .= '&' . $rawParams;
        }

        $verbose->step("Calling $function");
        $verbose->info("URL: $baseUrl");

        // Execute the request.
        if ($usePost) {
            $verbose->info('Method: POST');
            $ch = curl_init($baseUrl);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $queryString);
        } else {
            $verbose->info('Method: GET');
            $ch = curl_init($baseUrl . '?' . $queryString);
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($response === false) {
            $output->writeln("<error>cURL error: $error</error>");
            return Command::FAILURE;
        }

        $verbose->info("HTTP status: $httpCode");

        // Pretty-print JSON if requested.
        if ($pretty && $format === 'json') {
            $decoded = json_decode($response);
            if ($decoded !== null) {
                $response = json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            }
        }

        echo $response . PHP_EOL;

        // Check for error in JSON response.
        if ($format === 'json') {
            $decoded = json_decode($response, true);
            if (is_array($decoded) && isset($decoded['exception'])) {
                return Command::FAILURE;
            }
        }

        return Command::SUCCESS;
    }
}
