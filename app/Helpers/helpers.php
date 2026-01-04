<?php

use App\Models\ReferralSetting;

function getCommission()
{
    $get_commission = 0;
    $getdata = ReferralSetting::where('level', '1')->first();
    if ($getdata != "") {
        $get_commission = $getdata->percentage;
    }
    return $get_commission;
}
