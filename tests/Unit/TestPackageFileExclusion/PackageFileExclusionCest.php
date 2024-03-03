<?php

declare(strict_types=1);

namespace Rocky\SymfonyMessengerReader\Tests\Unit\TestPackageFileExclusion;

use Rocky\PackageFiles\PackageParser;
use Rocky\SymfonyMessengerReader\Tests\Support\UnitTester;

final class PackageFileExclusionCest
{
    public function packageWillOnlyIncludeSrcAndInfo(UnitTester $tester): void
    {
        $tester->assertSame(
            [
                'LICENSE',
                'README.md',
                'composer.json',
                'src'
            ],
            PackageParser::simplePackageSearch(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..')
        );
    }
}