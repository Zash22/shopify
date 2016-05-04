<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Preference;
use RocketCode\Shopify;
use Illuminate\Support\Facades\DB;
use App\Address;
use Laracasts\Flash;
use App\Http\Controllers\APIController;

class AddressController extends Controller
{
    public $order;
    public $shopify;
    public $preference;
    public $api;

    public function __construct()
    {


        $this->api = new APIController();
    }


    public function index()
    {

    }


    public function store($shopifyAddress, $shopifyAddressMD5)
    {
        
        $address = new Address();

        $address->shop = "mds-collivery-myshopify.com";
        $address->street = $shopifyAddress['street'];
        $address->suburb = $shopifyAddress['suburb'];
        $address->town = $shopifyAddress['town'];
        $address->location_type = $shopifyAddress['location_type'];
        $address->type = $shopifyAddress['type'];
        $address->shopify_hash = $shopifyAddressMD5;
//        $address->shopper_id = $shopifyAddress['shopper_id'];
        $address->shopper_id = 5;
        $address->save();
        return;
    }

    public function update($res , $shopifyAddressRetrieved , $shopifyAddressMD5) {

        $address = Address::where('shopify_hash', $shopifyAddressMD5)->first();

        $address->mds_address_id = $res['address_id'];
        $address->mds_contact_id = $res['contact_id'];
        $address->mds_hash = $res['mds_hash'];

        $address->shop = "mds-collivery-myshopify.com";
        $address->street = $shopifyAddressRetrieved['street'];
        $address->suburb = $shopifyAddressRetrieved['suburb'];
        $address->town = $shopifyAddressRetrieved['town'];
        $address->location_type = $shopifyAddressRetrieved['location_type'];
        $address->type = $shopifyAddressRetrieved['type'];
        $address->shopify_hash = $shopifyAddressMD5;

        $address->save();

        return;
    }

    public function processAddresses($shopifyAddress,$shop) {
//
//        [street] => 58C Webber St
//        [suburb] => Johannesburg
//        [town] => Selby
//        [location_type] => Mine
//        [type] => collection
//        [shopper_id] => mds-collivery-myshopify.com

        $shopifyAddressString =  $shopifyAddress['street'] . $shopifyAddress['suburb'] . $shopifyAddress['town']
            . $shopifyAddress['location_type'] . $shopifyAddress['shopper_id'];
        
        $shopifyAddressMD5 = hash('md5', $shopifyAddressString);
        $shopifyAddressMD5 = substr($shopifyAddressMD5, 0, 15);

        $verified = $this->checkCurrentAddress($shopifyAddress,$shopifyAddressMD5);
        
        $address['shop']= $shop;
        $address['shopper_id']= $verified['shopper_id'];
        $address['shopify_id']= $verified['shopify_id'];
        $address['mds_address_id']= $verified['mds_address_id'];
        $address['mds_contact_id']= $verified['mds_contact_id'];
        $address['street']= $shopifyAddress['street'];
        $address['suburb']= $shopifyAddress['suburb'];
        $address['town']= $shopifyAddress['town'];
        $address['location_type']= $shopifyAddress['location_type'];
        $address['type']= $shopifyAddress['type'];
        $address['mds_hash']= $verified['mds_hash'];
        $address['shopify_hash']= $verified['shopify_hash'];

        return $address;
    }

    public function checkCurrentAddress($shopifyAddress, $shopifyAddressMD5)  {

        $shopifyAddressRetrieved = Address::find($shopifyAddressMD5);

        $shopifyAddress['custom_id'] = " ";

        if (! $shopifyAddressRetrieved) { //if shopify database address is not saved locally

            $this->store($shopifyAddress, $shopifyAddressMD5); //save shopify database address locally
            $res = $this->api->addColliveryAddress($shopifyAddress); //add address to mds database
            $this->update($res,$shopifyAddressRetrieved,$shopifyAddressMD5); //update local address record with mds database values

        } elseif ($shopifyAddressRetrieved && ($shopifyAddressRetrieved->mds_hash == '0')) { //if mds database address is not saved locally
            $res = $this->api->addColliveryAddress($shopifyAddress); //add address to mds database
            $this->update($res,$shopifyAddressRetrieved,$shopifyAddressMD5); //update local address record with mds database values
            
        } elseif ($shopifyAddressRetrieved->mds_hash != '0') { //check to see if mds database address has been saved locally

            $mdsAddressRetrieved = $this->api->getAddressDetails($shopifyAddressRetrieved->mds_address_id);

            $mdsAddressRetrievedString =  $mdsAddressRetrieved['street'] . $mdsAddressRetrieved['suburb_name'] . $mdsAddressRetrieved['town_name']
                . $mdsAddressRetrieved['location_type'];

            $mdsAddressRetrievedMD5 = hash('md5', $mdsAddressRetrievedString);
            $mdsAddressRetrievedMD5 = substr($mdsAddressRetrievedMD5, 0, 15);

            if ($mdsAddressRetrievedMD5 != $shopifyAddressRetrieved->mds_hash) { //check to see if mds database address has changed
                $res = $this->api->addColliveryAddress($shopifyAddress); //add address to mds database
                $this->update($res,$shopifyAddressRetrieved,$shopifyAddressMD5); //update local address record with mds database values
            }
        }

        $shopifyAddressRetrieved = Address::where('shopify_hash', $shopifyAddressMD5)->first();

        return $shopifyAddressRetrieved;
    }
}
