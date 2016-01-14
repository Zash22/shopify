<?php
namespace App\Http\Controllers;
use Artisaninweb\SoapWrapper\Facades\SoapWrapper;

class APIController extends Controller {

    public function demo()
    {
        // Add a new service to the wrapper
        SoapWrapper::add(function ($service) {
            $service
                ->name('MDS')
                ->wsdl('http://www.collivery.co.za/wsdl/v2')
                ->trace(true);
        });

        $data = [
            'app_name'      => 'Shopify', // Application Name
            'app_version'   => '1.0',            // Application Version
            'app_host'      => 'shopify.dev', // Framework/CMS name and version, eg 'Wordpress 3.8.1 WooCommerce 2.0.20' / 'Joomla! 2.5.17 VirtueMart 2.0.26d'
            'app_url'       => 'shopify.dev', // URL your site is hosted on

        ];


        // Using the added service
        SoapWrapper::service('MDS', function ($service) use ($data) {
            var_dump($service->getFunctions());
            var_dump($service->call('authenticate',['api@collivery.co.za', 'api123']));
            var_dump($service->call('get_location_types',$data)->get_location_typesResult);

        });
    }

}