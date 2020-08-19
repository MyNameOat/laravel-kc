<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DocTranfer extends Model
{
    //
    protected $table = 'doc_tranfers';

    protected $fillable = [
    	'code',
    	'detail',
    	'document_at',
    	'user_request',
    	'user_create',
    	'wh_out',
    	'wh_in',
        'approve_code',
        'approve_status'
    ];

    public function goods(){

        return $this->hasMany(DocTranferGood::class);

    }

    public function whIn(){

        return $this->beLongsTo(Wh::class,'wh_in');

    }

    public function whOut(){

        return $this->beLongsTo(Wh::class,'wh_out');

    }

}
