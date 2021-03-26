<?php

namespace Edupham\Validator;

use Edupham\Validator\App\Rules\UpperCaseRule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

class ValidatorServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $package_name = 'validator';

        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', $package_name);
        $this->loadValidator($package_name);
        $this->loadValidatorExtend($package_name);
        $this->vendorPublish($package_name);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/validator.php', 'validator');
    }

    /**
     * Vendor publish module
     *
     * @param $package_name
     */
    private function vendorPublish($package_name)
    {
        $this->publishes([
            __DIR__ . '/../config/'.$package_name.'.php' => config_path($package_name.'.php')
        ], $package_name.'-config');

        $this->publishes([
            __DIR__ . '/../resources/lang' => resource_path('lang/vendor/'.$package_name)
        ], $package_name.'-lang');
    }

    /**
     * Load validator extend
     *
     * @param $package_name
     */
    private function loadValidator($package_name)
    {
        if (isset($this->app['validator'])) {
            foreach ($this->getRules() as $name => $rule) {
                $this->app['validator']->extend($name, $rule, $rule->message());
            }
        }
    }

    /**
     * Make rule item
     *
     * @return array'
     */
    private function getRules()
    {
        return [
            'upper_case' => $this->app->make(UpperCaseRule::class),
        ];
    }

    /**
     *
     * @param $package_name
     */
    private function loadValidatorExtend($package_name)
    {
        /**
         * Validate username
         */
        Validator::extend('username', function ($attribute, $value, $parameters, $validator) {
            $pattern = '/^[a-zA-Z]{1,}[a-zA-Z0-9_.]{5,31}$/';
            if (preg_match($pattern, $value)) {
                return true;
            }
            return false;
        });

        /**
         * Validate password
         */
        Validator::extend('password', function ($attribute, $value, $parameters, $validator) {
            $pattern = '/^\S{8,32}$/';
            if (preg_match($pattern, $value)) {
                return true;
            }
            return false;
        });

        /**
         * Validate mật khẩu mạnh
         */
        Validator::extend('strong_password', function ($attribute, $value, $parameters, $validator) {
            $pattern = '/^\S{8,31}$/';
            if (preg_match($pattern, $value)) {
                // Check blacklist
                $array_blacklist = config('validator.blacklist_password');
                if (count($array_blacklist) > 0 && in_array($value, $array_blacklist)) {
                    return false;
                }
                return true;
            }
            return false;
        });

        /**
         * Validate mã số thuế
         */
        Validator::extend('vn_taxcode', function ($attribute, $value, $parameters, $validator) {
            $vn_taxcode = '/^[0-9]{10}$|^[0-9]{10}-[0-9]{2}[1-9]{1}$/';
            if (preg_match($vn_taxcode, $value)) {
                $prefix_input = substr($value, 0, 3);
                $array_prefix = config('validator.tax_code_by_province');
                if (count($array_prefix) > 0 && in_array($prefix_input, $array_prefix)) {
                    $tax_code = substr($value, 0, 10);
                    if (strlen($tax_code) == 10) {
                        $total = 31*$tax_code[0];
                        $total += 29*$tax_code[1];
                        $total += 23*$tax_code[2];
                        $total += 19*$tax_code[3];
                        $total += 17*$tax_code[4];
                        $total += 13*$tax_code[5];
                        $total += 7*$tax_code[6];
                        $total += 5*$tax_code[7];
                        $total += 3*$tax_code[8];
                        if ($tax_code[9] == (10 - ($total % 11))) {
                            return true;
                        }
                        return false;
                    }
                    return false;
                }
                return false;
            }
            return false;
        });

        /**
         * Validate đầu số mạng mobifone, không tính chuyển mạng giữ số
         */
        Validator::extend('mobifone_prefix', function ($attribute, $value, $parameters, $validator){
            $mobifone_pattern = '^8490\d{7}$|^0?90\d{7}$|^90\d{7}$|';
            $mobifone_pattern .= '^8493\d{7}$|^0?93\d{7}$|^93\d{7}$|';
            $mobifone_pattern .= '^8470\d{7}$|^0?70\d{7}$|^70\d{7}$|';
            $mobifone_pattern .= '^8476\d{7}$|^0?76\d{7}$|^76\d{7}$|';
            $mobifone_pattern .= '^8477\d{7}$|^0?77\d{7}$|^77\d{7}$|';
            $mobifone_pattern .= '^8478\d{7}$|^0?78\d{7}$|^78\d{7}$|';
            $mobifone_pattern .= '^8479\d{7}$|^0?79\d{7}$|^79\d{7}$|';
            $mobifone_pattern .= '^8489\d{7}$|^0?89\d{7}$|^89\d{7}$';
            $mobifone_pattern = '/' . $mobifone_pattern . '/';
            if (preg_match($mobifone_pattern, $value)) {
                return true;
            }
            return false;
        });

        /**
         * Validate đầu số mạng việt nam mobile, không tính chuyển mạng giữ số
         */
        Validator::extend('vietnamobile_prefix', function ($attribute, $value, $parameters, $validator){
            $vietnamobile_pattern = '^8492\d{7}$|^0?92\d{7}$|^92\d{7}$|';
            $vietnamobile_pattern .= '^8456\d{7}$|^0?56\d{7}$|^56\d{7}$|';
            $vietnamobile_pattern .= '^8458\d{7}$|^0?58\d{7}$|^58\d{7}$';
            $vietnamobile_pattern = '/' . $vietnamobile_pattern . '/';
            if (preg_match($vietnamobile_pattern, $value)) {
                return true;
            }
            return false;
        });

        /**
         * Validate đầu số mạng viettel, không tính chuyển mạng giữ số
         */
        Validator::extend('viettel_prefix', function ($attribute, $value, $parameters, $validator){
            $viettel_pattern = '^8498\d{7}$|^0?98\d{7}$|^98\d{7}$|';
            $viettel_pattern .= '^8497\d{7}$|^0?97\d{7}$|^97\d{7}$|';
            $viettel_pattern .= '^8496\d{7}$|^0?96\d{7}$|^96\d{7}$|';
            $viettel_pattern .= '^8432\d{7}$|^0?32\d{7}$|^32\d{7}$|';
            $viettel_pattern .= '^8433\d{7}$|^0?33\d{7}$|^33\d{7}$|';
            $viettel_pattern .= '^8434\d{7}$|^0?34\d{7}$|^34\d{7}$|';
            $viettel_pattern .= '^8435\d{7}$|^0?35\d{7}$|^35\d{7}$|';
            $viettel_pattern .= '^8436\d{7}$|^0?36\d{7}$|^36\d{7}$|';
            $viettel_pattern .= '^8437\d{7}$|^0?37\d{7}$|^37\d{7}$|';
            $viettel_pattern .= '^8438\d{7}$|^0?38\d{7}$|^38\d{7}$|';
            $viettel_pattern .= '^8439\d{7}$|^0?39\d{7}$|^39\d{7}$|';
            $viettel_pattern .= '^8486\d{7}$|^0?86\d{7}$|^86\d{7}$';
            $viettel_pattern = '/' . $viettel_pattern . '/';
            if (preg_match($viettel_pattern, $value)) {
                return true;
            }
            return false;
        });

        /**
         * Validate đầu số mạng vinaphone, không tính chuyển mạng giữ số
         */
        Validator::extend('vinaphone_prefix', function ($attribute, $value, $parameters, $validator){
            $vinaphone_pattern = '^8491\d{7}$|^0?91\d{7}$|^91\d{7}$|';
            $vinaphone_pattern .= '^8494\d{7}$|^0?94\d{7}$|^94\d{7}$|';
            $vinaphone_pattern .= '^8481\d{7}$|^0?81\d{7}$|^81\d{7}$|';
            $vinaphone_pattern .= '^8482\d{7}$|^0?82\d{7}$|^82\d{7}$|';
            $vinaphone_pattern .= '^8483\d{7}$|^0?83\d{7}$|^83\d{7}$|';
            $vinaphone_pattern .= '^8484\d{7}$|^0?84\d{7}$|^84\d{7}$|';
            $vinaphone_pattern .= '^8485\d{7}$|^0?85\d{7}$|^85\d{7}$|';
            $vinaphone_pattern .= '^8488\d{7}$|^0?88\d{7}$|^88\d{7}$';
            $vinaphone_pattern = '/' . $vinaphone_pattern . '/';
            if (preg_match($vinaphone_pattern, $value)) {
                return true;
            }
            return false;
        });

        /**
         * Validate số điện thoại cố định ở Việt Nam
         */
        Validator::extend('vn_landline_prefix', function ($attribute, $value, $parameters, $validator){
            $landline_pattern = '^84203\d{7}$|^0?203\d{7}$|^203\d{7}$|';
            $landline_pattern .= '^84204\d{7}$|^0?204\d{7}$|^204\d{7}$|';
            $landline_pattern .= '^84205\d{7}$|^0?205\d{7}$|^205\d{7}$|';
            $landline_pattern .= '^84206\d{7}$|^0?206\d{7}$|^206\d{7}$|';
            $landline_pattern .= '^84207\d{7}$|^0?207\d{7}$|^207\d{7}$|';
            $landline_pattern .= '^84208\d{7}$|^0?208\d{7}$|^208\d{7}$|';
            $landline_pattern .= '^84209\d{7}$|^0?209\d{7}$|^209\d{7}$|';
            $landline_pattern .= '^84210\d{7}$|^0?210\d{7}$|^210\d{7}$|';
            $landline_pattern .= '^84211\d{7}$|^0?211\d{7}$|^211\d{7}$|';
            $landline_pattern .= '^84212\d{7}$|^0?212\d{7}$|^212\d{7}$|';

            $landline_pattern .= '^84213\d{7}$|^0?213\d{7}$|^213\d{7}$|';
            $landline_pattern .= '^84214\d{7}$|^0?214\d{7}$|^214\d{7}$|';
            $landline_pattern .= '^84215\d{7}$|^0?215\d{7}$|^215\d{7}$|';
            $landline_pattern .= '^84216\d{7}$|^0?216\d{7}$|^216\d{7}$|';
            $landline_pattern .= '^84218\d{7}$|^0?218\d{7}$|^218\d{7}$|';
            $landline_pattern .= '^84219\d{7}$|^0?219\d{7}$|^219\d{7}$|';
            $landline_pattern .= '^84220\d{7}$|^0?220\d{7}$|^220\d{7}$|';
            $landline_pattern .= '^84221\d{7}$|^0?221\d{7}$|^221\d{7}$|';
            $landline_pattern .= '^84222\d{7}$|^0?222\d{7}$|^222\d{7}$|';
            $landline_pattern .= '^84225\d{7}$|^0?225\d{7}$|^225\d{7}$|';

            $landline_pattern .= '^84226\d{7}$|^0?226\d{7}$|^226\d{7}$|';
            $landline_pattern .= '^84227\d{7}$|^0?227\d{7}$|^227\d{7}$|';
            $landline_pattern .= '^84228\d{7}$|^0?228\d{7}$|^228\d{7}$|';
            $landline_pattern .= '^84229\d{7}$|^0?229\d{7}$|^229\d{7}$|';
            $landline_pattern .= '^84232\d{7}$|^0?232\d{7}$|^232\d{7}$|';
            $landline_pattern .= '^84233\d{7}$|^0?233\d{7}$|^233\d{7}$|';
            $landline_pattern .= '^84234\d{7}$|^0?234\d{7}$|^234\d{7}$|';
            $landline_pattern .= '^84235\d{7}$|^0?235\d{7}$|^235\d{7}$|';
            $landline_pattern .= '^84236\d{7}$|^0?236\d{7}$|^236\d{7}$|';
            $landline_pattern .= '^84237\d{7}$|^0?237\d{7}$|^237\d{7}$|';

            $landline_pattern .= '^84238\d{7}$|^0?238\d{7}$|^238\d{7}$|';
            $landline_pattern .= '^84239\d{7}$|^0?239\d{7}$|^239\d{7}$|';
            $landline_pattern .= '^8424\d{7}$|^0?24\d{7}$|^24\d{7}$|';
            $landline_pattern .= '^84251\d{7}$|^0?251\d{7}$|^251\d{7}$|';
            $landline_pattern .= '^84252\d{7}$|^0?252\d{7}$|^252\d{7}$|';
            $landline_pattern .= '^84254\d{7}$|^0?254\d{7}$|^254\d{7}$|';
            $landline_pattern .= '^84255\d{7}$|^0?255\d{7}$|^255\d{7}$|';
            $landline_pattern .= '^84256\d{7}$|^0?256\d{7}$|^256\d{7}$|';
            $landline_pattern .= '^84257\d{7}$|^0?257\d{7}$|^257\d{7}$|';
            $landline_pattern .= '^84258\d{7}$|^0?258\d{7}$|^258\d{7}$|';

            $landline_pattern .= '^84259\d{7}$|^0?259\d{7}$|^259\d{7}$|';
            $landline_pattern .= '^84260\d{7}$|^0?260\d{7}$|^260\d{7}$|';
            $landline_pattern .= '^84261\d{7}$|^0?261\d{7}$|^261\d{7}$|';
            $landline_pattern .= '^84262\d{7}$|^0?262\d{7}$|^262\d{7}$|';
            $landline_pattern .= '^84263\d{7}$|^0?263\d{7}$|^263\d{7}$|';
            $landline_pattern .= '^84269\d{7}$|^0?269\d{7}$|^269\d{7}$|';
            $landline_pattern .= '^84270\d{7}$|^0?270\d{7}$|^270\d{7}$|';
            $landline_pattern .= '^84271\d{7}$|^0?271\d{7}$|^271\d{7}$|';
            $landline_pattern .= '^84272\d{7}$|^0?272\d{7}$|^272\d{7}$|';
            $landline_pattern .= '^84273\d{7}$|^0?273\d{7}$|^273\d{7}$|';

            $landline_pattern .= '^84274\d{7}$|^0?274\d{7}$|^274\d{7}$|';
            $landline_pattern .= '^84275\d{7}$|^0?275\d{7}$|^275\d{7}$|';
            $landline_pattern .= '^84276\d{7}$|^0?276\d{7}$|^276\d{7}$|';
            $landline_pattern .= '^84277\d{7}$|^0?277\d{7}$|^277\d{7}$|';
            $landline_pattern .= '^8428\d{7}$|^0?28\d{7}$|^28\d{7}$|';
            $landline_pattern .= '^84290\d{7}$|^0?290\d{7}$|^290\d{7}$|';
            $landline_pattern .= '^84291\d{7}$|^0?291\d{7}$|^291\d{7}$|';
            $landline_pattern .= '^84292\d{7}$|^0?292\d{7}$|^292\d{7}$|';
            $landline_pattern .= '^84293\d{7}$|^0?293\d{7}$|^293\d{7}$|';
            $landline_pattern .= '^84294\d{7}$|^0?294\d{7}$|^294\d{7}$|';

            $landline_pattern .= '^84296\d{7}$|^0?296\d{7}$|^296\d{7}$|';
            $landline_pattern .= '^84297\d{7}$|^0?297\d{7}$|^297\d{7}$|';
            $landline_pattern .= '^84299\d{7}$|^0?299\d{7}$|^299\d{7}$';

            $landline_pattern = '/' . $landline_pattern . '/';
            if (preg_match($landline_pattern, $value)) {
                return true;
            }
            return false;
        });

        /**
         * Validate đầu số mạng di động ở Việt Nam
         */
        Validator::extend('vn_mobile_prefix', function ($attribute, $value, $parameters, $validator){
            // Mạng mobifone
            $pattern = '^8490\d{7}$|^0?90\d{7}$|^90\d{7}$|';
            $pattern .= '^8493\d{7}$|^0?93\d{7}$|^93\d{7}$|';
            $pattern .= '^8470\d{7}$|^0?70\d{7}$|^70\d{7}$|';
            $pattern .= '^8476\d{7}$|^0?76\d{7}$|^76\d{7}$|';
            $pattern .= '^8477\d{7}$|^0?77\d{7}$|^77\d{7}$|';
            $pattern .= '^8478\d{7}$|^0?78\d{7}$|^78\d{7}$|';
            $pattern .= '^8479\d{7}$|^0?79\d{7}$|^79\d{7}$|';
            $pattern .= '^8489\d{7}$|^0?89\d{7}$|^89\d{7}$|';
            // Mạng vietnammobile
            $pattern .= '^8492\d{7}$|^0?92\d{7}$|^92\d{7}$|';
            $pattern .= '^8456\d{7}$|^0?56\d{7}$|^56\d{7}$|';
            $pattern .= '^8458\d{7}$|^0?58\d{7}$|^58\d{7}$|';
            // Mạng viettel
            $pattern .= '^8498\d{7}$|^0?98\d{7}$|^98\d{7}$|';
            $pattern .= '^8497\d{7}$|^0?97\d{7}$|^97\d{7}$|';
            $pattern .= '^8496\d{7}$|^0?96\d{7}$|^96\d{7}$|';
            $pattern .= '^8432\d{7}$|^0?32\d{7}$|^32\d{7}$|';
            $pattern .= '^8433\d{7}$|^0?33\d{7}$|^33\d{7}$|';
            $pattern .= '^8434\d{7}$|^0?34\d{7}$|^34\d{7}$|';
            $pattern .= '^8435\d{7}$|^0?35\d{7}$|^35\d{7}$|';
            $pattern .= '^8436\d{7}$|^0?36\d{7}$|^36\d{7}$|';
            $pattern .= '^8437\d{7}$|^0?37\d{7}$|^37\d{7}$|';
            $pattern .= '^8438\d{7}$|^0?38\d{7}$|^38\d{7}$|';
            $pattern .= '^8439\d{7}$|^0?39\d{7}$|^39\d{7}$|';
            $pattern .= '^8486\d{7}$|^0?86\d{7}$|^86\d{7}$|';
            // Mạng vinaphone
            $pattern .= '^8491\d{7}$|^0?91\d{7}$|^91\d{7}$|';
            $pattern .= '^8494\d{7}$|^0?94\d{7}$|^94\d{7}$|';
            $pattern .= '^8481\d{7}$|^0?81\d{7}$|^81\d{7}$|';
            $pattern .= '^8482\d{7}$|^0?82\d{7}$|^82\d{7}$|';
            $pattern .= '^8483\d{7}$|^0?83\d{7}$|^83\d{7}$|';
            $pattern .= '^8484\d{7}$|^0?84\d{7}$|^84\d{7}$|';
            $pattern .= '^8485\d{7}$|^0?85\d{7}$|^85\d{7}$|';
            $pattern .= '^8488\d{7}$|^0?88\d{7}$|^88\d{7}$';
            //
            $pattern = '/' . $pattern . '/';
            if (preg_match($pattern, $value)) {
                return true;
            }
            return false;
        });

        //Validator::extend('name_prefix', function ($attribute, $value, $parameters, $validator){});
    }
}
