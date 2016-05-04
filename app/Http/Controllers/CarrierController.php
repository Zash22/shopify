<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Preference;
use RocketCode\Shopify;
use Illuminate\Support\Facades\DB;

class CarrierController extends Controller
{
    public $order;
    public $shopify;
    public $preference;



    public function __construct()
    {

        $this->order = new OrderController();
        $this->preference = new PreferenceController();
    }


    public function index()
    {

    }


    public function verify_webhook($data, $hmac_header)
    {
        error_log('First test: ' . var_export($data, true));

        $calculated_hmac = base64_encode(hash_hmac('sha256', $data, SHOPIFY_APP_SECRET, true));
        return ($hmac_header == $calculated_hmac);


    }


    public function store()
    {
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

        $data = file_get_contents('php://input');
        $rates = $this->order->getOrderRates($data);

        $headers = getallheaders();
        $shop = $headers['X-Shopify-Shop-Domain'];

        $app_settings = DB::table('tbl_appsettings')->first();
        $user_settings = DB::table('tbl_usersettings')->where('store_name', $shop)->first();
        $settings = Preference::where('shop' , '=' , $shop)->where('active','=','1')->get()->toArray();

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
                    "service_name" => $settings['0']['display_5'],
                    "service_code" => $rated[5]["service_code"],
//                    "total_price" => $rated[5]["total_price"] + (100 *    $settings['0']['markup_5'] / $rated['5']["total_price"] ) ,
                    "total_price" => $rated[5]["total_price"],
                    "currency" => "ZAR",
//                    "min_delivery_date" => $rated[5]["min_delivery_date"],
//                    "max_delivery_date" => $rated[5]["min_delivery_date"],
                    "min_delivery_date" => "2013-04-12 14:48:45 -0400",
                    "max_delivery_date" => "2013-04-12 14:48:45 -0400",

                ],
                [

                    "service_name" =>   $settings['0']['display_1'],
                    "service_code" =>  $rates[1]["service_code"],
//                    "total_price" => $rated[1]["total_price"] + (100 *    $settings['0']['markup_1'] / $rated[1]["total_price"] ) ,
                    "total_price" => $rated[1]["total_price"]  ,
                    "currency" => "ZAR",
//                    "min_delivery_date" =>   $rates[1]["min_delivery_date"],
//                    "max_delivery_date" =>   $rates[1]["min_delivery_date"],
                ],
                [

                    "service_name" =>  $settings['0']['display_2'],
                    "service_code" => $rates[2]["service_code"],
//                    "total_price" => $rated[2]["total_price"] + (100 *   $settings['0']['markup_2'] /  $rated[2]["total_price"] ) ,
                    "total_price" => $rated[2]["total_price"] ,
                    "currency" => "ZAR",
//                    "min_delivery_date" => $rates[2]["min_delivery_date"],
//                    "max_delivery_date" => $rates[2]["min_delivery_date"],
                    "min_delivery_date" => "2013-04-12 14:48:45 -0400",
                    "max_delivery_date" => "2013-04-12 14:48:45 -0400",
                ],
                [

                    "service_name" =>  $settings['0']['display_3'],
                    "service_code" => $rates[3]["service_code"],
//                    "total_price" => $rated[3]["total_price"] + (100 * $settings['0']['markup_3'] /  $rated[3]["total_price"] ) ,
                    "total_price" => $rated[3]["total_price"] ,
                    "currency" => "ZAR",
//                    "min_delivery_date" => $rates[3]["min_delivery_date"],
//                    "max_delivery_date" => $rates[3]["min_delivery_date"],
                    "min_delivery_date" => "2013-04-12 14:48:45 -0400",
                    "max_delivery_date" => "2013-04-12 14:48:45 -0400",
                ],

            ),

        );
        return $blue;
    }


    public function edit_carrier()
    {
        $app_settings = DB::table('tbl_appsettings')->first();

        $user_settings = DB::table('tbl_usersettings')
            ->where('store_name', $_GET['shop'])
            ->where ('active' , 1)
            ->first();

        $shopify = \App::make('ShopifyAPI', ['API_KEY' => $app_settings->api_key, 'API_SECRET' => $app_settings->shared_secret, 'SHOP_DOMAIN' => $_GET['shop'], 'ACCESS_TOKEN' => $user_settings->access_token]);
        
//        try
//        {
//            $call = $shopify->call(['URL' => '/admin/carrier_services/5623553.json', 'METHOD' => 'PUT', 'DATA' => ['carrier_service' => array
//            (
//                "name" => "Yes",
//                "callback_url" => "http://f9328586.ngrok.io/carrier",
//                "format" => "json",
//                "service_discovery" => "true",
//            )]]);
//        }
//        catch (Exception $e)
//        {
//            $call = $e->getMessage();
//        }

        try
        {
            $call = $shopify->call(['URL' => '/admin/webhooks.json', 'METHOD' => 'GET']);
        }
        catch (Exception $e)
        {
            $call = $e->getMessage();
        }

        echo '<pre>';
        var_dump($call);
        echo '</pre>';

//        # view all carrier services
//        $call = $shopify->call(['URL' => '/admin/carrier_services.json', 'METHOD' => 'GET']);
//        print_r($call);
    }
}
