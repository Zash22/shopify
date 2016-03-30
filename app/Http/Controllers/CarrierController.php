<?php

namespace App\Http\Controllers;


//if (session_start() == null) {


//}


use App\Http\Controllers\OrderController;
use App\Http\Requests;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\URL;
use phpish;
use phpish\http;
use phpish\shopify;
use App\Http\Controllers\Controller;


//
define('REDIRECT_URI', 'http://shopify.dev/oauth');
define('SHOPIFY_APP_SECRET', 'afd45aaec4a7e49da3e5d469881a724fc1f67872cc10a894d4e2c6ef36258e95');

session_start();


class CarrierController extends Controller
{
    public $order;

    public $shopify;


    public function __construct()
    {

        $this->order = new OrderController();

    }


    public function index()
    {


        $data = file_get_contents('php://input');

//        if (isset($headers['X-Shopify-Hmac-Sha256'])) {
//
//            $hmac_header = $headers['X-Shopify-Hmac-Sha256'];
//            $verified = $this->verify_webhook($data,$hmac_header);
//            error_log('Webhook verified: '.var_export($verified, true));
//        }else {
//            error_log('Webhook index not verified: '.var_export($data, true));
//
//            return json_encode(array('error' => array('code' => 400, 'msg' => 'There are no valid rates found for the supplied address!')));
//        }

        $data = "{\"rate\":{\"origin\":{\"country\":\"ZA\",\"postal_code\":\"2001\",\"province\":\"GT\",\"city\":\"Selby\",\"name\":null,\"address1\":\"58C Webber St\",\"address2\":null,\"address3\":null,\"phone\":\"0861637000\",\"fax\":null,\"address_type\":null,\"company_name\":\"MDS Collivery\"},\"destination\":{\"country\":\"ZA\",\"postal_code\":\"658931576\",\"province\":\"NC\",\"city\":null,\"name\":\"\",\"address1\":null,\"address2\":null,\"address3\":null,\"phone\":null,\"fax\":null,\"address_type\":null,\"company_name\":null},\"items\":[{\"name\":\"Short Sleeve T-Shirt\",\"sku\":\"01\",\"quantity\":1,\"grams\":500,\"price\":15000,\"vendor\":\"MDS Clothing\",\"requires_shipping\":true,\"taxable\":true,\"fulfillment_service\":\"manual\",\"properties\":null,\"product_id\":3671390657,\"variant_id\":10722030593}],\"currency\":\"ZAR\"}}";

//        $this->order->saveOrderJson($data);
        $rates = $this->order->getOrderRates($data);

        foreach ($rates as $key => $rate) {

            $rates[$key]["service_name"] = $rate['service'];
            $rates[$key]["service_code"] = $rate['service'];
            $rates[$key]["total_price"] = number_format($rate['price']['inc_vat'], (int)0, '.', ' ');
            $rates[$key]["currency"] = "ZAR";
            $rates[$key]["min_delivery_date"] = date('Y-m-d H:i:s');
            $rates[$key]["max_delivery_date"] = date('Y-m-d H:i:s', strtotime(date('Y-m-d') . ' +' . rand(1, 5) . ' days'));
        }


        $t = array('rates' => $rates);

        return $t;

    }


    public function verify_webhook($data, $hmac_header)
    {

        error_log('First test: ' . var_export($data, true));

        $calculated_hmac = base64_encode(hash_hmac('sha256', $data, SHOPIFY_APP_SECRET, true));
        return ($hmac_header == $calculated_hmac);


    }


    public function store()
    {


        $data = file_get_contents('php://input');

//        if (isset($headers['X-Shopify-Hmac-Sha256'])) {
//
//            $hmac_header = $headers['X-Shopify-Hmac-Sha256'];
//            $verified = $this->verify_webhook($data,$hmac_header);
//            error_log('Webhook verified: '.var_export($verified, true));
//        }else {
//            error_log('Webhook not verified: '.var_export($data, true));
//
//            return json_encode(array('error' => array('code' => 400, 'msg' => 'There are no valid rates found for the supplied address!')));
//        }
////
//        $methods = array();
//

//        $blue = array(
//
//            'rates' => array
//            (
//
//                [
//                    "service_name" => " Fhh8888888ss",
//                    "service_code" => "nds",
//                    "total_price" => number_format(785.99, (int)0, '.', ' '),
//                    "currency" => "ZAR",
//                    "min_delivery_date" => date('Y-m-d H:i:s'),
//                    "max_delivery_date" => date('Y-m-d H:i:s', strtotime(date('Y-m-d') . ' +' . rand(1, 5) . ' days')),
//
//
//                ],
//                [
//                    "service_name" => " sdggfgss",
//                    "service_code" => "nds",
//                    "total_price" => number_format(785.99, (int)0, '.', ' '),
//                    "currency" => "ZAR",
//                    "min_delivery_date" => date('Y-m-d H:i:s'),
//                    "max_delivery_date" => date('Y-m-d H:i:s', strtotime(date('Y-m-d') . ' +' . rand(1, 5) . ' days')),
//
//                ],
//
//            ),
//
//        );
//
//        return $blue;
////
//
//
////
////
////        $oh = json_decode($data, true);
////        error_log('JSON: ' . var_export($data, true));
//
//    //    return $_SESSION;
//
//        $rates = array();
//        $rated = array();
////
        $rates = $this->order->getOrderRates($data);
//
//        return $rates;


        //  $rates = json_decode($rates,true);


        foreach ($rates as $key => $rate) {

            $rated[$key]["service_name"] = $rate['service_name'];
            $rated[$key]["service_code"] = $rate['service_code'];
            $rated[$key]["total_price"] = $rate['total_price'];
            $rated[$key]["currency"] = "ZAR";
            $rated[$key]["min_delivery_date"] = $rate["min_delivery_date"];
            $rated[$key]["max_delivery_date"] = $rate["max_delivery_date"];
        }


        $blue = array(

            'rates' => array(
                [
                    "service_name" => $rated[5]["service_name"],
                    "service_code" => $rated[5]["service_code"],
                    "total_price" => $rated[5]["total_price"],
                    "currency" => "ZAR",
                    "min_delivery_date" => $rated[5]["min_delivery_date"],
                    "max_delivery_date" => $rated[5]["min_delivery_date"],


                ],
//                [
//
//                    "service_name" =>  $rates[1]["service_name"],
//                    "service_code" =>  $rates[1]["service_code"],
//                    "total_price" => $rates[1][ "total_price"],
//                    "currency" => "ZAR",
//                    "min_delivery_date" =>   $rates[1]["min_delivery_date"],
//                    "max_delivery_date" =>   $rates[1]["min_delivery_date"],
//                ],
                [

                    "service_name" => $rates['2']["service_name"],
                    "service_code" => $rates['2']["service_code"],
                    "total_price" => $rates['2']["total_price"],
                    "currency" => "ZAR",
                    "min_delivery_date" => $rates['2']["min_delivery_date"],
                    "max_delivery_date" => $rates['2']["min_delivery_date"],
                ],
                [

                    "service_name" => $rates[3]["service_name"],
                    "service_code" => $rates[3]["service_code"],
                    "total_price" => $rates[3]["total_price"],
                    "currency" => "ZAR",
                    "min_delivery_date" => $rates[3]["min_delivery_date"],
                    "max_delivery_date" => $rates[3]["min_delivery_date"],
                ],

            ),

        );

        return $blue;
        
    }


    public function edit_carrier()
    {

        $shopify = shopify\client($_GET['shop'], SHOPIFY_APP_API_KEY, $_SESSION['oauth_token']);


        try {
            
            $product = $shopify('PUT /admin/carrier_services/5337473.json', array(), array
            (
                'carrier_service' => array
                (
                    "name" => "Second MDS",
                    "callback_url" => "http://431f37dd.ngrok.io/carrier",
                    "format" => "json",
                    "service_discovery" => "true",
                )
            ));

            print_r($product);
        } catch (shopify\ApiException $e) {
            # HTTP status code was >= 400 or response contained the key 'errors'
            echo $e;
            print_r($e->getRequest());
            print_r($e->getResponse());
        } catch (shopify\CurlException $e) {
            # cURL error
            echo $e;
            print_r($e->getRequest());
            print_r($e->getResponse());
        }

        $shopify = shopify\client($_GET['shop'], SHOPIFY_APP_API_KEY, $_SESSION['oauth_token']);
        $carriers = $shopify('GET /admin/carrier_services.json');
        print_r($carriers);
        
    }


    public function getProductDimensions($id)
    {
        $shop = "mds-collivery.myshopify.com";


        $shopify = shopify\client($shop, SHOPIFY_APP_API_KEY, $_SESSION['oauth_token']);


        try {

            $tags = $shopify('GET /admin/products/' . $id . '.json?fields=tags');

            foreach ($tags as $h => $tag) {

                $dimension = $tag;

            }

            return $dimension;
        } catch (shopify\ApiException $e) {
            # HTTP status code was >= 400 or response contained the key 'errors'
            echo $e;
            print_r($e->getRequest());
            print_r($e->getResponse());
        } catch (shopify\CurlException $e) {
            # cURL error
            echo $e;
            print_r($e->getRequest());
            print_r($e->getResponse());
        }

    }


}
