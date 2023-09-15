<?php

namespace Tests\Feature;

use App\Models\Voucher;
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
}
