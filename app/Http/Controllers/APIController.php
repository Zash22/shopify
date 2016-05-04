<?php
namespace App\Http\Controllers;
use Mds\Collivery;
use phpish\http\Exception;
use Mds\Cache;

class APIController extends Controller {

    private $error = array();
    protected $registry;
    public $collivery;
  //  public $cache;


    public function __construct()
    {
        $settings = array(
            'app_name'      => "SHOPIFY ",
            'app_url'       => "MDS_shop",
            'app_version'   => "1.0",
            'user_email'    => "api@collivery.co.za",
            'user_password' => "api123"
        );

        $this->collivery = new Collivery($settings);
        //$this->cache = new Cache();

    }

    public function demo()
    {
        $towns =  $this->collivery->getTowns();
        return $towns;
    }

    public function getDefaultAddress()
    {
        $defaultAddress['address_id'] = $this->collivery->getDefaultAddressId();
        $contacts[] = $this->collivery->getContacts((int)$defaultAddress['address_id']);

        $contact = array_pop($contacts);
        $defaultAddress['contact_id'] = $contact['contact_id'];

        return $defaultAddress;

    }

    public function getServices()
    {
        $services =  $this->collivery->getServices();
        return $services;
    }

    public function getPrice($array)
    {

        $price = $this->collivery->getPrice($array);

        return $price;

    }

    public function validateCollivery(array $array)

    {
        $validated[] = $this->collivery->validate($array);

        return $validated;
    }

    public function addColliveryAddress(array $array)
    {

        $towns = $this->collivery->getTowns();
        $location_types = $this->collivery->getLocationTypes();

        if ( ! is_numeric($array['town'])) {
            $town_id = (int) array_search($array['town'], $towns);
        } else {
            $town_id = $array['town'];
        }

        $suburbs = $this->collivery->getSuburbs($town_id);

        $custom_id = $array['custom_id'];

        if ( ! is_numeric($array['suburb'])) {
            $suburb_id = (int) array_search($array['suburb'], $suburbs);
        } else {
            $suburb_id = $array['suburb'];
        }

        if ( ! is_numeric($array['location_type'])) {
            $location_type_id = (int) array_search($array['location_type'], $location_types);
        } else {
            $location_type_id = $array['location_type'];
        }

        if (empty($array['location_type']) || ! isset($location_types[ $location_type_id ])) {
//            throw new Exception("Invalid location type");

            die("location");
        }

        if (empty($array['town']) || ! isset($towns[ $town_id ])) {
//            throw new Exception("Invalid town");
            die("town");

        }

        if (empty($array['suburb']) || ! isset($suburbs[ $suburb_id ])) {
//            throw new Exception("Invalid suburb");
            die("sub");

        }

        if (empty($array['cellphone']) || ! is_numeric($array['cellphone'])) {
//            throw new Exception("Invalid cellphone number");
            die("cell");

        }

        if (empty($array['email']) || ! filter_var($array['email'], FILTER_VALIDATE_EMAIL)) {
//            throw new Exception("Invalid email address");
            die("mail");

        }

        $newAddress = array(
            'company_name'         => ( ! empty($array['company_name'])) ? $array['company_name'] : '',
            'building'         => ( ! empty($array['building'])) ? $array['building'] : '',
            'street'        => $array['street'],
            'location_type' => $location_type_id,
            'suburb_id'     => $suburb_id,
            'town_id'       => $town_id,
            'full_name'     => $array['full_name'],
            'phone'         => ( ! empty($array['phone'])) ? $array['phone'] : '',
            'cellphone'     => $array['cellphone'],
            'custom_id'     => $custom_id,
            'email'         => $array['email'],
        );

        $mdsAddressString =  $array['street'] . $array['suburb'] . $array['town']
            . $array['location_type'] . $array['email'];

        $mdsAddressMD5 = hash('md5', $mdsAddressString);
        $mdsAddressMD5 = substr($mdsAddressMD5, 0, 15);
        // Before adding an address lets search MDS and see if we have already added this address
        $searchAddresses = $this->searchAndMatchAddress(
            array(
                'custom_id' => $custom_id,
                'suburb_id' => $suburb_id,
                'town_id'   => $town_id,
            ),
            $newAddress,
            $mdsAddressString
        );
        
        if (is_array($searchAddresses)) {

            return $searchAddresses;
        } else {
//            $this->cache->forget('addresses');
//            $this->cache->forget('contacts');

            return $this->collivery->addAddress($newAddress, $mdsAddressMD5);
        }
    }

    public function searchAndMatchAddress(array $filters, array $newAddress, $mdsAddressMD5)
    {



        $searchAddresses = $this->collivery->getAddresses($filters);
        if ( ! empty($searchAddresses)) {
            $match = true;

            $matchAddressFields = array(
                'street'        => 'street',
                'location_type' => 'location_type',
                'suburb_id'     => 'suburb_id',
                'town_id'       => 'town_id',
                'custom_id'     => 'custom_id',
            );

            foreach ($searchAddresses as $address) {
                foreach ($matchAddressFields as $mdsField => $newField) {
                    if ($address[ $mdsField ] != $newAddress[ $newField ]) {
                        $match = false;
                    }
                }

                if ($match) {
                    if ( ! isset($address['contact_id'])) {
                        $contacts = $this->collivery->getContacts($address['address_id']);
                        list($contact_id) = array_keys($contacts);
                        $address['contact_id'] = $contact_id;
                    }
                    $address['mds_hash'] = $mdsAddressMD5;
                    return $address;
                }
            }
        } else {
            $this->collivery->clearErrors();
        }

        return false;
    }

    public function getAddressDetails ($addressId) {
        
        $address = $this->collivery->getAddress($addressId);
        return $address;

    }






}