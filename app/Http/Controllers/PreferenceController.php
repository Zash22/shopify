<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Preference;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Auth;
use Laracasts\Flash\Flash;
use Illuminate\Support\Facades\DB;



class PreferenceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        if (isset ($_GET['shop'])) {
            $shop = $_GET['shop'];
        } else {
            $shop = 'mds-collivery.myshopify.com';
        }

        $userId = DB::table('tbl_usersettings')->where('store_name', $shop)->first();

//        $preference = Preference::find($userId->store_name);
        $preference = Preference::find($userId->store_name)->where('active', '1')->first();

        if (! $preference) {

            return view('preference.create');

        } else {

            return view('preference.index', compact('preference'));
        }

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('preference.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $preference = new Preference();

        $shop = 'mds-collivery.myshopify.com';

        $preference->shop = $shop;

        $preference->mds_user = $request->get('mds_user');
        $preference->mds_pass = $request->get('mds_pass');
        $preference->display_1 = $request->get('display_1');
        $preference->display_2 = $request->get('display_2');
        $preference->display_3 = $request->get('display_3');
        $preference->display_5 = $request->get('display_5');
        $preference->markup_1 = $request->get('markup_1');
        $preference->markup_2 = $request->get('markup_2');
        $preference->markup_3 = $request->get('markup_3');
        $preference->markup_5 = $request->get('markup_5');
        $preference->risk = $request->get('risk');
        $preference->active = 1;

        $preference->save();

        Flash::success('Preferences has been created');

        return view('preference.index', compact('preference'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
//
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {

        $shop = $request->get('shop');

        $preference = Preference::where('shop', $shop)->where('active', '1')->first();
        
        $preference->mds_user = $request->get('mds_user');
        $preference->mds_pass = $request->get('mds_pass');
        $preference->display_1 = $request->get('display_1');
        $preference->display_2 = $request->get('display_2');
        $preference->display_3 = $request->get('display_3');
        $preference->display_5 = $request->get('display_5');
        $preference->markup_1 = $request->get('markup_1');
        $preference->markup_2 = $request->get('markup_2');
        $preference->markup_3 = $request->get('markup_3');
        $preference->markup_5 = $request->get('markup_5');
        $preference->risk = $request->get('risk');
        $preference->active = 1;
        
        $preference->save();

        Flash::success('Preferences has been updated');

        return redirect()->route('preference.index', compact('preference'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function install ($shop) {
        
        $userId = DB::table('tbl_usersettings')->where('store_name', $shop)->first();

        $preference = Preference::find($userId->store_name)->where('active', '1')->first();
        
            return view('preference.create');


    }
}
