<?php

namespace Tests\Unit\Services\Url;

use App\Services\Url\Link;
use Tests\TestCase;

/**
 * @see Link
 */
class LinkTest extends TestCase
{
    /** @test */
    public function it_generates_simple_link_based_on_base_url(): void
    {
        $link = new Link('https://application.com');

        $this->assertEquals('https://application.com/testing', $link->to('testing'));
    }

    /** @test */
    public function it_trims_slashes_correctly(): void
    {
        $link = new Link('https://application.com/');

        $this->assertEquals('https://application.com/testing', $link->to('/testing/'));
    }

    /** @test */
    public function it_generates_link_with_query_params(): void
    {
        $link = new Link('https://app.testing.com');

        $this->assertEquals('https://app.testing.com/users?utm=TESTING', $link->to('users/', ['utm' => 'TESTING']));
    }
}
