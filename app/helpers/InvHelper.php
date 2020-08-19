<?php

namespace App\Helpers;

use Carbon\Carbon;
use App\DocTranfer;
use App\TranferOut;
use App\MenuAccess;
class InvHelper
{
	public static function countDocTranfer(){

		return DocTranfer::whereApproveStatus(0)->count();

	}

	public static function countTranferOut(){

		return TranferOut::whereApproveStatus(0)->count();

    }

}
