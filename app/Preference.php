<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Preference extends Model {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'preference';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = ['shop'];

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'shop';

    public $timestamps = false;


//    public function user()
//    {
//        return $this->belongsTo('App\User', 'user_id');
//    }
    
}
