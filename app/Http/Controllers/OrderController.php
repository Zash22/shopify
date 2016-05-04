<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Requests;
use Mds\Collivery;
use RocketCode\Shopify;
use Illuminate\Support\Facades\DB;
use App\Order;
use Laracasts\Flash\Flash;
use App\Http\Controllers\AddressController;




class OrderController extends Controller
{
    public $api;
    public $carrier;
    public $collivery;
    public $address;

    public function __construct()
    {

        $this->api = new APIController();

        if (isset($_GET['shop'])) {

            $shop = $_GET['shop'];
        }else {
            $headers = getallheaders();

            if (isset($headers['X-Shopify-Shop-Domain'])) {

                $shop = $headers['X-Shopify-Shop-Domain'];
            }elseif (isset($headers['Referer'])) {

                $text = explode ('=', $headers['Referer']);
                $shops = explode ('&', $text['1']);
                $shop = $shops['0'];
            }
        }

        $user_settings = DB::table('preference')->where('shop', $shop)->where('active','=','1')->first();


        $settings = array(
            'app_name'      => "SHOPIFY",
            'app_url'       => $user_settings->shop,
            'app_version'   => "1.0",
            'user_email'    => $user_settings->mds_user,
            'user_password' => $user_settings->mds_pass,
        );

        $this->collivery = new Collivery($settings);

        $this->address = new AddressController();

    }

    public function index()
    {

        $headers = getallheaders();
//        $shop = $headers['X-Shopify-Shop-Domain'];

        $shop = $_GET['shop'];

        $app_settings = DB::table('tbl_appsettings')->first();
        $user_settings = DB::table('tbl_usersettings')->where('store_name', $shop)->where('active','=','1')->first();

        $shopify = \App::make('ShopifyAPI', ['API_KEY' => $app_settings->api_key, 'API_SECRET' => $app_settings->shared_secret, 'SHOP_DOMAIN' => $shop, 'ACCESS_TOKEN' => $user_settings->access_token]);

        $ids = $_GET['ids'];


        foreach ($ids as $id) {

            try {
                $call = $shopify->call(['URL' => '/admin/orders/' . $id . '.json', 'METHOD' => 'GET']);

            } catch (Exception $e) {
                $call = $e->getMessage();
            }

            $yolos[] = $call;
        }
        return view('order.index', compact('yolos'));
    }

    public function saveOrderJson($json)
    {
        echo $json;
        return $json;
    }

    public function getOrderRates($json)
    {
        $order[] = json_decode($json,true);

        $colliveryArray = $this->buildValidateArray($order);

        $services = $this->api->getServices();

//        print_r($services);
//        die();

        foreach ($services as $serviceid => $service) {

            $colliveryArray['service'] = $serviceid;
            $validated = $this->api->validateCollivery($colliveryArray);

            $price = $validated['0']['price']['inc_vat'];
            $rates[$validated['0']['service']]["service_name"] = $service;
            $rates[$validated['0']['service']]["service_code"] = $service;
            $rates[$validated['0']['service']]["total_price"] = $price;
            $rates[$validated['0']['service']]["currency"] = "ZAR";
            $rates[$validated['0']['service']]["min_delivery_date"] = $validated['0']['delivery_time'];
            $rates[$validated['0']['service']]["max_delivery_date" ]= $validated['0']['delivery_time'] + 72;

        }

        return $rates;
    }

    public function buildValidateArray($order)
    {
        $orderItems = $order['0']['rate']['items'];
        $shop = "mds-collivery-myshopify.com";

        $orderCollectionAddress = array (
            "street" => $order['0']['rate']['origin']['address1'],
            "suburb" => "President Park",
            "town" => "Johannesburg",
            "location_type" => "Mine",
//            "shopify_id" => $order['0']['rate']['origin']['id'],
            "shopper_id" => 3,
            "type" => "collection",
            "cellphone" => $order['0']['rate']['origin']['phone'],
            "email" => "ohNo@test.com",
            "full_name" => "Collection",
            
        );

        $orderDeliveryAddress = array (
            "street" => $order['0']['rate']['destination']['address1'],
            "suburb" => "Akasia",
            "town" => "Pretoria",
            "location_type" => "Private House",
//            "shopify_id" => $order['0']['rate']['destination']['id'],
            "type" => "destination",
//            "shopper_id" => $order['0']['rate']['destination']['name'],
            "shopper_id" => 6,
//            "cellphone" => $order['0']['rate']['destination']['phone'],
            "cellphone" => $order['0']['rate']['origin']['phone'],
            "email" => "ohNo@test.com",
            "full_name" => "Delivery",


        );

        $collectionAddress = $this->address->processAddresses($orderCollectionAddress,$shop);
        $deliveryAddress = $this->address->processAddresses($orderDeliveryAddress,$shop);

        $colliveryParams['collivery_to'] = (int)$deliveryAddress['mds_address_id'];
        $colliveryParams['contact_to'] = (int)$deliveryAddress['mds_contact_id'];
        $colliveryParams['collivery_from'] = (int)$collectionAddress['mds_address_id'];
        $colliveryParams['contact_from'] = (int)$collectionAddress['mds_contact_id'];
        $colliveryParams['collivery_type'] = 2;

        $dimensions = $this->getProductDimensions($orderItems);

        foreach ($dimensions as $dimension) {

            for ($i = 0; $i < $dimension['qty']; $i++) {
                $colliveryParams['parcels'][] = array(
                    'weight' => $dimension['dimensions']['weight'] / 1000,
                    'height' => $dimension['dimensions']['height'],
                    'width' => $dimension['dimensions']['width'],
                    'length' => $dimension['dimensions']['length']
                );
            }
        }

        return $colliveryParams;
    }

    public function getProductDimensions(array $items)
    {
        $headers = getallheaders();
        $shop = $headers['X-Shopify-Shop-Domain'];

        $app_settings = DB::table('tbl_appsettings')->first();
        $user_settings = DB::table('tbl_usersettings')->where('store_name', $shop)->where('active','1')->first();

        $shopify = \App::make('ShopifyAPI', ['API_KEY' => $app_settings->api_key, 'API_SECRET' => $app_settings->shared_secret, 'SHOP_DOMAIN' => $shop, 'ACCESS_TOKEN' => $user_settings->access_token]);

        foreach ($items as $item) {
           $id =  $item['product_id'];

            try {
                $tags = $shopify->call(['URL' => '/admin/products/' . $id . '.json?fields=tags', 'METHOD' => 'GET']);

                foreach ($tags as $tag) {

                    $ggg = explode ( ',' , $tag->tags );

                    foreach ($ggg as $gg) {

                        $gg = trim($gg);
                        $g = explode ('x', $gg );

                        if (count($g) == 3) {
                            $dimension[$id]['dimensions']['length'] = $g['0'];
                            $dimension[$id]['dimensions']['width'] = $g['1'];
                            $dimension[$id]['dimensions']['height'] = $g['2'];
                        }
                    }

                    $dimension[$id]['dimensions']['weight'] = $item['grams'];
                    $dimension[$id]['qty'] = $item['quantity'];
                }

            } catch (Exception $e) {
                $tags = $e->getMessage();
            }
        }

        return $dimension;
    }



    public function verify_webhook($data, $hmac_header)
    {

        error_log('createOrder: ' . var_export($data, true));
        $calculated_hmac = base64_encode(hash_hmac('sha256', $data, SHOPIFY_APP_SECRET, true));
        return ($hmac_header == $calculated_hmac);


    }

    public function store()
    {
        $orders = new Order;

        $headers = getallheaders();
        $shop = $headers['X-Shopify-Shop-Domain'];
        $userId = DB::table('tbl_usersettings')->where('store_name', $shop)->where('active','=','1')->first();

        $data = file_get_contents('php://input');
//        if (isset($headers['X-Shopify-Hmac-Sha256'])) {
//
//            $hmac_header = $headers['X-Shopify-Hmac-Sha256'];
//            $verified = $this->verify_webhook($data, $hmac_header);
//            error_log('Webhook verified: ' . var_export($verified, true));
//        } else {
//            error_log('Webhook not verified: ' . var_export($data, true));
//
//            return json_encode(array('error' => array('code' => 400, 'msg' => 'store')));
//        }

        $data = json_decode($data, true);


        $this->address->processAddresses();

        $orders->order_id = $data['id'];
        $orders->user_id = $userId->id;
        $orders->cp_id = 1329920;
        $orders->dp_id = 1190306;
//        $order->service_id = $data['shipping_lines']['0']['code'];
        $orders->service_id = 5;
//        $data->waybill_id = $request->get('Status');
//        $data->collivery_created = $request->get('Status');
        $orders->order_created = Carbon::createFromFormat(\DateTime::W3C, $data['created_at']);

        $orders->save();

        return;

    }

    public function show($id) {

        $shop = 'mds-collivery.myshopify.com';

        $app_settings = DB::table('tbl_appsettings')->first();
        $user_settings = DB::table('tbl_usersettings')->where('store_name', $shop)->where('active','=','1')->first();

        $shopify = \App::make('ShopifyAPI', ['API_KEY' => $app_settings->api_key, 'API_SECRET' => $app_settings->shared_secret, 'SHOP_DOMAIN' => $shop, 'ACCESS_TOKEN' => $user_settings->access_token]);

            try {
                $order[] = $shopify->call(['URL' => '/admin/orders/' . $id . '.json', 'METHOD' => 'GET']);

            } catch (Exception $e) {
                $order = $e->getMessage();
            }

//      $order = DB::table('orders')->where('order_id', $id)->first();
        $order[] = Order::find($id);
        
        echo "<pre>";
        print_r($order);
        echo "</pre>";
        die();

        return view('order.show', compact('order'));

    }

    public function edit($id) {

    }

    public function update(Request $request, $id)
    {
        $order = Order::find($id);

        Flash::success('Vehicle has been updated');

        return $this->show($id);
    }

  



}
