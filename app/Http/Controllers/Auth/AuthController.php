<?php

namespace App\Http\Controllers\Auth;

use App\User;
use Validator;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use Illuminate\Support\Facades\Request;
use RocketCode\Shopify;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\PreferenceController;

class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Registration & Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users, as well as the
    | authentication of existing users. By default, this controller uses
    | a simple trait to add these behaviors. Why don't you explore it?
    |
    */

    use AuthenticatesAndRegistersUsers, ThrottlesLogins;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = '/';
    public $preference;


    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'logout']);

        $this->preference = new PreferenceController();
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|confirmed|min:6',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array $data
     * @return User
     */
    protected function create(array $data)
    {
        return User::create([
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);
    }

    public function access(Request $request)
    {
        $shop = "";

        if (isset($_GET['shop'])) {
            session(['shop' => $_GET['shop']]);
            $shop = $_GET['shop'];
        }

        $user_settings = DB::table('tbl_usersettings')
            ->where('store_name', $shop)
            ->where ('active' , 1)
            ->first();

        if (Session::has('access_token') && $user_settings) {

            return view('welcome');
        } else {
            $this->auth($shop);
        }
    }

    public function auth($shop)
    {
        $app_settings = DB::table('tbl_appsettings')->first();

        $shopify = \App::make('ShopifyAPI', ['API_KEY' => $app_settings->api_key, 'API_SECRET' => $app_settings->shared_secret, 'SHOP_DOMAIN' => $shop, 'ACCESS_TOKEN' => '']);

            $permission_url = $shopify->installURL(
                [
                    'permissions' => array('read_content',
                        'write_content',
                        'read_themes',
                        'write_themes',
                        'read_products',
                        'write_products',
                        'read_customers',
                        'write_customers',
                        'read_orders',
                        'write_orders',
                        'read_script_tags',
                        'write_script_tags',
                        'read_fulfillments',
                        'write_fulfillments',
                        'read_shipping',
                        'write_shipping'),

                    'redirect' => $app_settings->redirect_url
                ]
            );

            echo("<script> top.location.href='$permission_url'</script>");
    }


    public function authCallback()
    {

        $app_settings = DB::table('tbl_appsettings')->first();

        $shop = "";

        if (isset($_GET['shop'])) {
            session(['shop' => $_GET['shop']]);
            $shop = $_GET['shop'];
        }

        if (isset($_GET['code'])) {
            $shopify = \App::make('ShopifyAPI', ['API_KEY' => $app_settings->api_key, 'API_SECRET' => $app_settings->shared_secret, 'SHOP_DOMAIN' => $shop, 'ACCESS_TOKEN' => '']);

            $accessToken = $shopify->getAccessToken($_GET['code']);

            DB::table('tbl_usersettings')->insert(
                array(
                    'store_name' => $shop,
                    'access_token' => $accessToken,
                    'active' => '1'
                )
            );

            session(['shop' => $shop, 'access_token' => $accessToken]);
            $this->registerWebhooks($app_settings,$shop,$accessToken);
            $this->registerCarrier($app_settings,$shop,$accessToken);

        }

        return view('preference.create');
    }
    
    public function uninstall () {

        $headers = getallheaders();
        $shop = $headers['X-Shopify-Shop-Domain'];


        DB::table('tbl_usersettings')
            ->where('store_name', $shop)
            ->where ('active' , 1)
            ->update('active', 0);

        DB::table('preference')
            ->where('shop', $shop)
            ->where ('active' , 1)
            ->update('active', 0);

    }

    public function registerWebhooks($app_settings,$shop,$accessToken) {

        try
        {
            $shopify = \App::make('ShopifyAPI', ['API_KEY' => $app_settings->api_key, 'API_SECRET' => $app_settings->shared_secret, 'SHOP_DOMAIN' => $shop, 'ACCESS_TOKEN' => $accessToken]);

            $call = $shopify->call(['URL' => '/admin/webhooks.json', 'METHOD' => 'POST', 'DATA' => ['webhook' => array
            (
                "topic" => "app/uninstalled",
                "address" => "https://d642e20e.ngrok.io/uninstall",
                "format" => "json",
            )]]);

        }
        catch (Exception $e)
        {
            $call = $e->getMessage();
        }

        try
        {
            $shopify = \App::make('ShopifyAPI', ['API_KEY' => $app_settings->api_key, 'API_SECRET' => $app_settings->shared_secret, 'SHOP_DOMAIN' => $shop, 'ACCESS_TOKEN' => $accessToken]);

            $call = $shopify->call(['URL' => '/admin/webhooks.json', 'METHOD' => 'POST', 'DATA' => ['webhook' => array
            (
                "topic" => "orders/create",
                "address" => "https://d642e20e.ngrok.io/order",
                "format" => "json",
            )]]);

        }
        catch (Exception $e)
        {
            $call = $e->getMessage();
        }

        return;
    }

    public function registerCarrier($app_settings,$shop,$accessToken) {

        try {

            $shopify = \App::make('ShopifyAPI', ['API_KEY' => $app_settings->api_key, 'API_SECRET' => $app_settings->shared_secret, 'SHOP_DOMAIN' => $shop, 'ACCESS_TOKEN' => $accessToken]);

            $call = $shopify->call(['URL' => '/admin/carrier_services.json', 'METHOD' => 'POST', 'DATA' => ['carrier_service' => array
            (
                "name" => "Second MDS",
                "callback_url" => "https://d642e20e.ngrok.io/carrier",
                "format" => "json",
                "service_discovery" => "true",
            )]]);



        } catch (Exception $e) {
            $call = $e->getMessage();
        }
    }
}
