<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Address extends Model {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'address';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = ['shopify_hash'];

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'shopify_hash';

    public $timestamps = false;


//    public function user()
//    {
//        return $this->belongsTo('App\User', 'user_id');
//    }
    
}
