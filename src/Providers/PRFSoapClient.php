<?php
namespace LapayGroup\RussianPost\Providers;

class PRFSoapClient extends \SoapClient {

    function __doRequest($request, $location, $action, $version, $oneWay = 0) {
        $request = str_replace('xmlns:ns1="http://russianpost.org/operationhistory"', 'xmlns:ns1="http://russianpost.org/operationhistory" xmlns:oper="http://russianpost.org/operationhistory" xmlns:data="http://russianpost.org/operationhistory/data" xmlns:data1="http://www.russianpost.org/RTM/DataExchangeESPP/Data"', $request);
        return parent::__doRequest($request, $location, $action, $version, $oneWay = 0);
    }
}