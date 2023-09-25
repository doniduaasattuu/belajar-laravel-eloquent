<?php

namespace Tests\Feature;

use App\Models\Employee;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class EmployeeTest extends TestCase
{
    public function testEmployeeProgrammer()
    {
        $employee1 = Employee::factory()->programmer()->create([
            "id" => "1",
            "name" => "Doni"
        ]);

        self::assertNotNull($employee1);
        self::assertEquals("Programmer", $employee1->title);
        self::assertEquals(5000000, $employee1->salary);
    }

    public function testEmployeeSeniorProgrammer()
    {
        $employee2 = Employee::factory()->seniorProgrammer()->create([
            "id" => "2",
            "name" => "Eko Kurniawan Khannedy"
        ]);

        self::assertNotNull($employee2);
        self::assertEquals("Senior Programmer", $employee2->title);
        self::assertEquals(10000000, $employee2->salary);
    }

    public function testEmployeeMake()
    {
        $employee3 = Employee::factory()->seniorProgrammer()->make();
        $employee3->id = "3";
        $employee3->save();

        self::assertNotNull($employee3);
        self::assertEquals("3", $employee3->id);
        self::assertEquals("", $employee3->name);
        self::assertEquals("Senior Programmer", $employee3->title);
        self::assertEquals(10000000, $employee3->salary);
    }
}
