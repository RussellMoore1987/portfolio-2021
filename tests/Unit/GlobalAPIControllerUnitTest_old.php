<?php

namespace Tests\Unit;

use App\Http\Controllers\GlobalAPIController;
use Illuminate\Http\Request;
use PHPUnit\Framework\TestCase;

class GlobalAPIControllerUnitTest extends TestCase
{   
    public function test_initial_process_of_all_parameters()
    {
        $GlobalAPIController = new GlobalAPIController;

        $parameters = [
            'id' => 33,
            'title' => 'Gogo!!',
            'roles' => 'Admin',
            'foo_bar' => 33,
            'perPage' => 1,
            'page' => 3,
            'orderBy' => 'title'
        ];

        $paramsAccepted = [
            'id' => 33,
            'title' => 'Gogo!!',
            'roles' => 'Admin',
            'perPage' => 1,
            'page' => 3,
            'orderBy' => 'title'
        ];

        $acceptableParameters = [
            "id" => [],
            "title" => [],
            "roles" => []
        ];

        $GlobalAPIController->initialProcessOfAllParameters($parameters, $acceptableParameters);

        $this->assertTrue($GlobalAPIController->paramsAccepted === $paramsAccepted);
        $this->assertTrue($GlobalAPIController->paramsRejected === ['foo_bar' => 33]);
    }

    public function test_getting_and_setting_class_db_data()
    {
        $GlobalAPIController = new GlobalAPIController;

        $classDBData = [
            [
                "Field" => "id",
                "Type" => "bigint(20) unsigned",
                "Null" => "NO",
                "Key" => "PRI",
                "Default" => null,
                "Extra" => "auto_increment",
            ],
            [
                "Field" => "title",
                "Type" => "varchar(75)",
                "Null" => "NO",
                "Key" => "",
                "Default" => null,
                "Extra" => "",
            ],
            [
                "Field" => "roles",
                "Type" => "varchar(50)",
                "Null" => "YES",
                "Key" => "",
                "Default" => null,
                "Extra" => "",
            ]
            
        ];

        $assertArray = [
            "id" => [
                "Field" => "id",
                "Type" => "bigint(20) unsigned",
                "Null" => "NO",
                "Key" => "PRI",
                "Default" => null,
                "Extra" => "auto_increment"
            ],
            "title" => [
                "Field" => "title",
                "Type" => "varchar(75)",
                "Null" => "NO",
                "Key" => "",
                "Default" => null,
                "Extra" => ""
            ],
            "roles" => [
                "Field" => "roles",
                "Type" => "varchar(50)",
                "Null" => "YES",
                "Key" => "",
                "Default" => null,
                "Extra" => ""
            ]
        ];

        $GlobalAPIController->setAcceptableParameters($classDBData);

        
        $this->assertTrue($GlobalAPIController->acceptableParameters === $assertArray);
    }


    public function test_array_of_objects_to_array_of_arrays()
    {
        $GlobalAPIController = new GlobalAPIController;

        $arrayOfObjects = [
            (object) ['1' => 'foo'],
            (object) ['1' => 'foo'],
            (object) ['1' => 'foo']
        ];

        $arrayOfArrays = [
            ['1' => 'foo'],
            ['1' => 'foo'],
            ['1' => 'foo']
        ];

        $result = $GlobalAPIController->arrayOfObjectsToArrayOfArrays($arrayOfObjects);

        
        $this->assertTrue($result === $arrayOfArrays);
    }

    // Test 
}