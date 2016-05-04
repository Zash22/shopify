<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
/* The database table used by the model.
*
* @var string
*/
    protected $table = 'orders';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
//    protected $guarded = ['order_id'];

protected $fillable = ['order_id', 'user_id', 'cp_id', 'dp_id', 'service_id', 'waybill_id', 'order_created', 'collivery_created', 'collivery_status'];

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'order_id';

    public $timestamps = false;

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['order_created'];


//    public function user()
//    {
//
}
