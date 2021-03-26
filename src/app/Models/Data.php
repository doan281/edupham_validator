<?php

namespace Edupham\Validator\App\Models;

class Data
{
    public function getMobifonePattern()
    {
        $mobifone_pattern = '^8490\d{7}$|^0?90\d{7}$|^90\d{7}$|';
        $mobifone_pattern .= '^8493\d{7}$|^0?93\d{7}$|^93\d{7}$|';
        $mobifone_pattern .= '^8470\d{7}$|^0?70\d{7}$|^70\d{7}$|';
        $mobifone_pattern .= '^8476\d{7}$|^0?76\d{7}$|^76\d{7}$|';
        $mobifone_pattern .= '^8477\d{7}$|^0?77\d{7}$|^77\d{7}$|';
        $mobifone_pattern .= '^8478\d{7}$|^0?78\d{7}$|^78\d{7}$|';
        $mobifone_pattern .= '^8479\d{7}$|^0?79\d{7}$|^79\d{7}$|';
        $mobifone_pattern .= '^8489\d{7}$|^0?89\d{7}$|^89\d{7}$';
        $mobifone_pattern = '/' . $mobifone_pattern . '/';
        return $mobifone_pattern;
    }

    public function getVietnamobilePattern()
    {

    }

    public function getViettelPattern()
    {

    }

    public function getVinaphonePattern()
    {

    }

    public function getVnLandlinePattern()
    {

    }

    public function getVnMobilePattern()
    {

    }
}