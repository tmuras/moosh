<?php
/**
 * moosh webservice-call --token <token> [--params <params>] <function>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle23\Webservice;
use Moosh\MooshCommand;

class WebserviceCall extends MooshCommand
{
	
    public function __construct()
    {
        parent::__construct('call', 'webservice');
        
        $this->addOption('t|token:', 'token');
        $this->addOption('p|params:', 'params');

        $this->addArgument('function');
    }

    public function execute()
    {
        global $CFG;

        $options = $this->expandedOptions;
        
        $sUrl = $CFG->wwwroot.'/webservice/rest/server.php';
        
        $aQueryParams = array();
        $aQueryParams['wsfunction'] = $this->arguments[0];
        
        if (!empty($options['token'])) {
			$aQueryParams['wstoken'] = $options['token'];        	
        }
        
        $sQueryParams = http_build_query($aQueryParams, null, '&');
        
        if (!empty($options['params'])) {
        	$sQueryParams .= '&'.$options['params'];
        }
                
        $sUrl .= '?'.$sQueryParams;
        
        $rCurl = curl_init($sUrl);
        curl_setopt($rCurl, CURLOPT_HEADER, 0);
        curl_setopt($rCurl, CURLOPT_RETURNTRANSFER, 1);
        echo curl_exec($rCurl);
        curl_close($rCurl);
    }
}
