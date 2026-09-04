<?php

namespace TraderTracker\Php\Tests\E2E;

use Facebook\WebDriver\WebDriverBy;
use Symfony\Component\Panther\PantherTestCase;

class RegisterE2ETest extends PantherTestCase {

    public function testRegisterThenLoginThenDeleteAccount(): void {

        $client = static::createPantherClient(['external_base_uri' => 'http://127.0.0.1:5501']);
        $email = 'e2e-' . uniqid() . '@test.com';

        // 1. Inscription
        $client->request('GET', '/#/register');
        $client->waitFor('input[name="name"]');

        $client->submitForm('submit', [
            'name' => 'E2E Test User',
            'email' => $email,
            'password' => 'Abcdef1!',
        ]);

        $client->wait(3)->until(
            fn() => $client->getCurrentURL() === 'http://127.0.0.1:5501/#/'
        );

        // 2. Connexion
        $client->request('GET', '/#/login');
        $client->waitFor('input[name="email"]');

        $client->submitForm('SIGN IN', [
            'email' => $email,
            'password' => 'Abcdef1!',
        ]);

        $client->wait(3)->until(
            fn() => str_contains($client->getCurrentURL(), '/#/user')
        );
        $this->assertStringContainsString('/#/user', $client->getCurrentURL());

        // 3. Suppression du compte
        $client->waitFor('#delete-account-btn');

        $modalOpened = false;
        for ($i = 0; $i < 10 && !$modalOpened; $i++) {
            $client->findElement(WebDriverBy::id('delete-account-btn'))->click();
            try {
                $client->waitFor('#confirm-modal-confirm', 1);
                $modalOpened = true;
            } catch (\Exception $e) {
                // retry
            }
        }

        $this->assertTrue($modalOpened, 'La modal de confirmation ne s\'est jamais ouverte.');

        $client->findElement(WebDriverBy::id('confirm-modal-confirm'))->click();

        $client->wait(8)->until(
            fn() => $client->getCurrentURL() === 'http://127.0.0.1:5501/#/'
        );
        $this->assertStringNotContainsString('/#/user', $client->getCurrentURL());
    }
}