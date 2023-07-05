<?php

namespace App\Models;

use App\Casts\SigningKeyCast;
use App\Casts\VerificationKeyCast;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\LaravelCipherSweet\Contracts\CipherSweetEncrypted;
use Spatie\LaravelCipherSweet\Concerns\UsesCipherSweet;
use ParagonIE\CipherSweet\EncryptedRow;
use ParagonIE\CipherSweet\BlindIndex;
use ParagonIE\CipherSweet\JsonFieldMap;

class Wallet extends Model implements CipherSweetEncrypted
{
    use HasTimestamps,
        UsesCipherSweet,
        SoftDeletes;

    protected $casts = [
        'signing_key' => SigningKeyCast::class,
        'verification_key' => VerificationKeyCast::class
    ];

    /**
     * Encrypted Fields
     *
     * Each column that should be encrypted should be added below. Each column
     * in the migration should be a `text` type to store the encrypted value.
     *
     * ```
     * ->addField('column_name')
     * ->addBooleanField('column_name')
     * ->addIntegerField('column_name')
     * ->addTextField('column_name')
     * ```
     *
     * A JSON array can be encrypted as long as the key structure is defined in
     * a field map. See the docs for details on defining field maps.
     *
     * ```
     * ->addJsonField('column_name', $fieldMap)
     * ```
     *
     * Each field that should be searchable using an exact match needs to be
     * added as a blind index. Partial search is not supported. See the docs
     * for details on bit sizes and how to use compound indexes.
     *
     * ```
     * ->addBlindIndex('column_name', new BlindIndex('column_name_index'))
     * ```
     *
     * @see https://github.com/spatie/laravel-ciphersweet
     * @see https://ciphersweet.paragonie.com/
     * @see https://ciphersweet.paragonie.com/php/blind-index-planning
     * @see https://github.com/paragonie/ciphersweet/blob/master/src/EncryptedRow.php
     *
     * @param EncryptedRow $encryptedRow
     *
     * @return void
     */
    public static function configureCipherSweet(EncryptedRow $encryptedRow): void
    {
        $encryptedRow
            ->addTextField('phasephrase')
            ->addBlindIndex('phasephrase', new BlindIndex('phasephrase_index'));

        $skMap = (new JsonFieldMap())
            ->addTextField(['type','description','cborHex']);

        $encryptedRow
            ->addJsonField('signing_key', $skMap);
    }

}
