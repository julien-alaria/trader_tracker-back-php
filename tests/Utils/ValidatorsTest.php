<?php

namespace TraderTracker\Php\Tests\Utils;

use PHPUnit\Framework\TestCase;
use TraderTracker\Php\Utils\Validators;
use TraderTracker\Php\Utils\AppError;

class ValidatorsTest extends TestCase {

    public function testValidateEmailAcceptsValidEmail(): void {

        $result = Validators::validateEmail('Testemail@Example.com');
        $this->assertEquals('testemail@example.com', $result);

    }

    public function testValidateEmailRejectsinvalidEmail(): void {

        $this->expectException(AppError::class);
        Validators::validateEmail(('fake-email'));
    }

    public function testValidateNameRejectsForbiddenCharacters(): void {

        $this->expectException(AppError::class);
        Validators::validateName('John@Doe');

    }
}