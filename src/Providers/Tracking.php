<?php
namespace LapayGroup\RussianPost\Providers;

use LapayGroup\RussianPost\Singleton;

class Tracking
{
    use Singleton;

    protected $wsdl = 'https://tracking.russianpost.ru';
    protected $login = '';
    protected $password = '';

    public $client = false;

    function __construct($service, $timeout = 60, $login=false, $password=false)
    {
        if($service != 'pack') {
            $this->wsdl .= '/rtm34?wsdl';
            $soapVersion = SOAP_1_2;
        } else {
            $this->wsdl .= '/fc?wsdl';
            $soapVersion = SOAP_1_1;
        }

        $this->client = new \SoapClient($this->wsdl, array(
                'trace' => 1,
                'soap_version' => $soapVersion,
                'use' => SOAP_LITERAL,
                'style' => SOAP_DOCUMENT,
                'connection_timeout'=>$timeout
            )
        );
    }
}