<?php

namespace Tests\Feature;

use App\Models\Person;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class PersonTest extends TestCase
{
    public function testPersonGetFullname()
    {
        $person = new Person();
        $person->first_name = "Eko";
        $person->last_name = "Khannedy";
        $person->save();

        self::assertEquals("EKO Khannedy", $person->fullName);
    }

    public function testPersonSetFullname()
    {
        $person = new Person();
        $person->first_name = "Eko";
        $person->last_name = "Khannedy";
        $person->save();

        self::assertEquals("EKO Khannedy", $person->full_name);
        self::assertEquals("EKO Khannedy", $person->fullName);

        // update `persons` set `first_name` = ?, `last_name` = ?, `persons`.`updated_at` = ? where `id` = ? 
        $person->full_name = "Doni Darmawan";
        $person->save();

        self::assertEquals("DONI", $person->first_name);
        self::assertEquals("Darmawan", $person->last_name);
    }

    public function testFirstName()
    {
        $person = new Person();
        $person->first_name = "Eko";
        $person->last_name = "Khannedy";
        $person->save();

        self::assertEquals("EKO", $person->first_name);

        $person->first_name = "doni";

        self::assertEquals("DONI", $person->first_name);

        $person = Person::query()->where("first_name", "DONI")->get();
        Log::info(json_encode($person));
    }

    public function testAttributeCasting()
    {
        $person = new Person();
        $person->first_name = "Eko";
        $person->last_name = "Khannedy";
        $person->save();

        Log::info(json_encode($person->created_at));
        Log::info(json_encode($person->updated_at));

        self::assertNotNull($person->created_at);
        self::assertNotNull($person->updated_at);
        self::assertInstanceOf(Carbon::class, $person->created_at);
        self::assertInstanceOf(Carbon::class, $person->updated_at);
    }
}
