<?php

namespace App\Http\Controllers\Auth;

use App\User;
use Validator;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;

use Illuminate\Support\Facades\Request;

use RocketCode\Shopify\ShopifyServiceProvider;
use RocketCode\Shopify;

//use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Session;



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

    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'logout']);
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
            session(['shop'=> $_GET['shop']]);
            $shop = $_GET['shop'];
        }

        if (Session::has('access_token')) {
            return view('welcome');
        }else {
            auth($shop);
        }

    }

    public function auth($shop){
        $app_settings = DB::table('app_setting')->first();


        $shopify = App::make('ShopifyAPI', ['API_KEY' => $app_settings->api_key, 'API_SECRET' => $app_settings->shared_secret, 'SHOP_DOMAIN' => $shop, 'ACCESS_TOKEN' => '']);

        $permission_url = $shopify->installURL (
            [
                'permissions' => array ('read_content',
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
                    'read_shipping'),

                'redirect' => $app_settings->redirect_url
            ]
        );

        return view('auth.escaprIFrame', ['installURL' => $permission_url]);
    }


    public function authCallback() {

        if (isset($_GET['code'])) {
            $shopify = App::make('ShopifyAPI', ['API_KEY' => $app_settings->api_key, 'API_SECRET' => $app_settings->shared_secret, 'SHOP_DOMAIN' => $shop, 'ACCESS_TOKEN' => '']);

            $accessToken = $shopify->getAccessToken($_GET['code']);

            DB::table('user_setting')->insert (
                array(
                    'store_name' => $shop,
                    'access_token' =>$accessToken
                )
            );

            session(['shop'=>$shop,'access_token' => $accessToken]);

            return redirect('/');
        }


    }
}
