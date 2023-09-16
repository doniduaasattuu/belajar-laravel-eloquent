<?php

namespace Tests\Feature;

use App\Models\Voucher;
use Database\Seeders\VoucherSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class VoucherTest extends TestCase
{
    public function testCreateVoucher()
    {
        $voucher = new Voucher();
        $voucher->name = "Sample Voucher";
        $voucher->voucher_code = "12321323543jrire345345";
        $voucher->save();

        self::assertNotNull($voucher->id);
        Log::info(json_encode($voucher->id));

        $vouchers = Voucher::query()->where("name", "=", "Sample Voucher")->get();
        foreach ($vouchers as $voucher) {
            Log::info(json_encode($voucher, JSON_PRETTY_PRINT));
        }
    }

    public function testCreateVoucherUniqueIds()
    {
        $voucher = new Voucher();
        $voucher->name = "Sample Voucher";
        $voucher->save();

        self::assertNotNull($voucher->id);
        Log::info(json_encode($voucher->id));

        $vouchers = Voucher::query()->where("name", "=", "Sample Voucher")->get();
        foreach ($vouchers as $voucher) {
            Log::info(json_encode($voucher, JSON_PRETTY_PRINT));
        }
    }

    public function testSoftDeletes()
    {
        $this->seed(VoucherSeeder::class);

        $voucher = Voucher::where("name", "=", "Sample Voucher")->first();
        self::assertNotNull($voucher);
        $voucher->delete();


        $voucher = Voucher::where("name", "=", "Sample Voucher")->first();
        self::assertNull($voucher);
    }

    public function testSoftDeletesWithTrashed()
    {
        $this->seed(VoucherSeeder::class);

        $voucher = Voucher::where("name", "=", "Sample Voucher")->first();
        self::assertNotNull($voucher);
        $voucher->delete();

        // tidak menggunakan with trashed maka akan return null
        $voucher = Voucher::where("name", "=", "Sample Voucher")->first();
        self::assertNull($voucher);

        // menggunakan withTrashed maka akan return record dengan deleted_at != null;
        $voucher = Voucher::withTrashed()->where("name", "=", "Sample Voucher")->first();
        self::assertNotNull($voucher);
        self::assertEquals("Sample Voucher", $voucher->name);
    }

    public function testLocalScopesActive()
    {
        $voucher = new Voucher();
        $voucher->name = "Sample Voucher";
        $voucher->voucher_code = "12345";
        $voucher->is_active = false;
        $voucher->save();

        // untuk menggunakan function scopeActive(), gunakan active() pada query 
        // select * from `vouchers` where `name` = ? and `is_active` = ? and `vouchers`.`deleted_at` is null limit 1  
        $voucher = Voucher::query()->where("name", "=", "Sample Voucher")->active()->first();
        self::assertNull($voucher);
    }

    public function testLocalScopesNonActive()
    {
        $voucher = new Voucher();
        $voucher->name = "Sample Voucher";
        $voucher->voucher_code = "12345";
        $voucher->is_active = false;
        $voucher->save();

        // untuk menggunakan function scopeNonActive(), gunakan nonActive() pada query 
        // select * from `vouchers` where `name` = ? and `is_active` = ? and `vouchers`.`deleted_at` is null limit 1  
        $voucher = Voucher::query()->where("name", "=", "Sample Voucher")->nonActive()->first();
        self::assertNotNull($voucher);
    }
}
