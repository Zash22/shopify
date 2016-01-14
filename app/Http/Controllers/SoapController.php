<?php
namespace App\Http\Controllers;

use Artisaninweb\SoapWrapper\Facades\SoapWrapper;

class SoapController extends Controller {

    public function demo()
    {
        // Add a new service to the wrapper
        SoapWrapper::add(function ($service) {
            $service
                ->name('currency')
                ->wsdl('http://currencyconverter.kowabunga.net/converter.asmx?WSDL')
                ->trace(true);
        });

        $data = [
            'CurrencyFrom' => 'USD',
            'CurrencyTo'   => 'EUR',
            'RateDate'     => '2014-06-05',
            'Amount'       => '1000'
        ];

        // Using the added service
        SoapWrapper::service('currency', function ($service) use ($data) {
            var_dump($service->getFunctions());
            var_dump($service->call('GetConversionAmount', [$data])->GetConversionAmountResult);
        });
    }

}