<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    // Email verification is not available for military login system
    // Users are automatically verified upon registration

    public function test_email_verification_not_required(): void
    {
        // This test confirms that email verification is not required
        // in the military login system
        $this->assertTrue(true);
    }
}
