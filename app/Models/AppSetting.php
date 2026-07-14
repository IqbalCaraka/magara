<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class AppSetting extends Model
{
    protected $primaryKey = 'key';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = ['key', 'value', 'is_encrypted'];

    protected function casts(): array
    {
        return [
            'is_encrypted' => 'boolean',
        ];
    }

    /**
     * Get setting value, auto-decrypt if encrypted.
     */
    public static function getValue(string $key, mixed $default = null): mixed
    {
        $setting = static::find($key);

        if (!$setting || $setting->value === null) {
            return $default;
        }

        if ($setting->is_encrypted) {
            try {
                return Crypt::decryptString($setting->value);
            } catch (\Exception) {
                return $default;
            }
        }

        return $setting->value;
    }

    /**
     * Set setting value, auto-encrypt if flagged.
     */
    public static function setValue(string $key, ?string $value, bool $encrypt = false): void
    {
        static::updateOrCreate(
            ['key' => $key],
            [
                'value' => $encrypt && $value ? Crypt::encryptString($value) : $value,
                'is_encrypted' => $encrypt,
            ]
        );
    }

    /**
     * Get all DMS connection config from database.
     */
    public static function getDmsConfig(): array
    {
        return [
            'host' => static::getValue('dms_db_host', ''),
            'port' => static::getValue('dms_db_port', '3306'),
            'database' => static::getValue('dms_db_database', ''),
            'username' => static::getValue('dms_db_username', ''),
            'password' => static::getValue('dms_db_password', ''),
        ];
    }
}
