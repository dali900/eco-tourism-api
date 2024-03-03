<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class App extends Model
{
    use HasFactory;

    const BZR_NAME = "BZR PORTAL";
    const BZR_KEY = "bzr";
    const EI_NAME = "EXPORT INFO";
    const EI_KEY = "exportinfo";
    const ZZS_NAME = "ZŽS PORTAL";
    const ZZS_KEY = "zzs";

    public static function getData($key): array
    {
        switch ($key) {
            case self::BZR_KEY:
                return [
                    'key' => self::BZR_KEY,
                    'name' => self::BZR_NAME,
                    'url' => config('app.client_bzr_app_url')
                ];
                break;
            case self::EI_KEY:
                return [
                    'key' => self::EI_KEY,
                    'name' => self::EI_NAME,
                    'url' => config('app.client_ei_app_url')
                ];
                break;
            case self::ZZS_KEY:
                return [
                    'key' => self::ZZS_KEY,
                    'name' => self::ZZS_NAME,
                    'url' => config('app.client_zzs_app_url')
                ];
                break;
            
            default:
                return [
                    'key' => 'actamedia',
                    'name' => 'ActaMedia',
                    'url' => 'www.actamedia.rs'
                ];
                break;
        }
    }
    
}
