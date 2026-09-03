<?php

namespace TraderTracker\Php\Tests\Utils;

use PHPUnit\Framework\TestCase;
use TraderTracker\Php\Utils\Sanitizer;

class SanitizerTest extends TestCase {

    public function testCleansAndNormalizesaValidDataset() : void {
    
        $result = Sanitizer::sanitizeUser([
            
            "name" => "   Jean  ", 
            "email" => "Jean@Test.com",
            "password" => "Abcdef1!",
            "role" => "analyst",
            "analyst_type_id" => 2,
            "company" => "ACME",
            "bio" => "hi",

        ]);

        $this->assertEquals("Jean", $result["name"]);
        $this->assertEquals("jean@test.com", $result["email"]);
        $this->assertEquals("Abcdef1!", $result["password"]);
        $this->assertEquals("analyst", $result["role"]);
        $this->assertEquals(2, $result["analyst_type_id"]);
        $this->assertEquals("ACME", $result["company"]);
        $this->assertEquals("hi", $result["bio"]);
    }

    public function testNormalizesTheEmailAndKeepsThePasswordAsIs(): void {

        $result = Sanitizer::sanitizeLogin([
            "email" => "Test@Test.com",
            "password" => "x",
        ]);

        $this->assertEquals("test@test.com", $result["email"]);
        $this->assertEquals("x", $result["password"]);

    }
}