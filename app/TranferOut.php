<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TranferOut extends Model
{
    //
    protected $table = 'tranfer_outs';

    protected $fillable = [
		'code',
        'tranfer_code',
        'detail',
        'wh_out',
        'wh_in',
        'user_tranfer',
        'tranfer_date',
        'document_at',
        'approve_status'
    ];

    public function goods(){

    	return $this->hasMany(TranferOutGood::class);

    }

    public function whOut(){

        return $this->beLongsTo(Wh::class,'wh_out');

    }

    public function whIn(){

        return $this->beLongsTo(Wh::class,'wh_in');

    }
}
