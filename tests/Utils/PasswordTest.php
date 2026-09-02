<?php

namespace TraderTracker\Php\Tests\Utils;

use PHPUnit\Framework\TestCase;
use TraderTracker\Php\Utils\Password;

class PasswordTest extends TestCase {

    public function testHashProducesADifferentStringThanThePlainPassword(): void {
        $hash = Password::hash('aA123456!');
        $this->assertNotEquals('aA123456!', $hash);
    }

    public function testHashProducesADifferentHashOnEachCall(): void {
        $hash1 = Password::hash('aA123456!');
        $hash2 = Password::hash('aA123456!');
        $this->assertNotEquals($hash1, $hash2);
    }

    public function testVerifyReturnsTrueForTheCorrectPassword(): void {
        $hash = Password::hash('aA123456!');
        $this->assertTrue(Password::verify('aA123456!', $hash));
    }

    public function testVerifyreturnsFalseForAWrongPassword(): void {
        $hash = Password::hash('aA123456!');
        $this->assertFalse(Password::verify('wrongPassword', $hash));
    }
}