<?php
declare(strict_types=1);

namespace Fc2blog\Tests\App\Model\BlogsModel;

use Fc2blog\Model\BlogsModel;
use PHPUnit\Framework\TestCase;

class IsValidBlogIdTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function testValid(): void
    {
        $this->assertTrue(BlogsModel::isValidBlogId("testblog1"));
        $this->assertFalse(BlogsModel::isValidBlogId("containSymbol!"));
        $this->assertFalse(BlogsModel::isValidBlogId("aa"), "require more than 3 chars");
        $this->assertTrue(BlogsModel::isValidBlogId("aaa"), "require more than 3 chars");
        $this->assertTrue(BlogsModel::isValidBlogId(str_repeat("a", 50)), "require less 50 chars or less");
        $this->assertFalse(BlogsModel::isValidBlogId(str_repeat("a", 51)), "require less 50 chars or less");
        $this->assertFalse(BlogsModel::isValidBlogId("CAPITALCASENOTALLOWED"));
        $this->assertFalse(BlogsModel::isValidBlogId("no space allowed"));
    }

}
