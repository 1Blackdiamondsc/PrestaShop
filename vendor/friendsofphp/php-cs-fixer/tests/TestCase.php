<?php

/*
 * This file is part of PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace MolliePrefix\PhpCsFixer\Tests;

use MolliePrefix\LegacyPHPUnit\TestCase as BaseTestCase;
use MolliePrefix\PHPUnitGoodPractices\Polyfill\PolyfillTrait;
use MolliePrefix\PHPUnitGoodPractices\Traits\ExpectationViaCodeOverAnnotationTrait;
use MolliePrefix\PHPUnitGoodPractices\Traits\ExpectOverSetExceptionTrait;
use MolliePrefix\PHPUnitGoodPractices\Traits\IdentityOverEqualityTrait;
use MolliePrefix\PHPUnitGoodPractices\Traits\ProphecyOverMockObjectTrait;
use MolliePrefix\PHPUnitGoodPractices\Traits\ProphesizeOnlyInterfaceTrait;
use MolliePrefix\Prophecy\PhpUnit\ProphecyTrait;
// we check single, example DEV dependency - if it's there, we have the dev dependencies, if not, we are using PHP-CS-Fixer as library and trying to use internal TestCase...
if (\trait_exists(\MolliePrefix\PHPUnitGoodPractices\Traits\ProphesizeOnlyInterfaceTrait::class)) {
    if (\trait_exists(\MolliePrefix\Prophecy\PhpUnit\ProphecyTrait::class)) {
        /**
         * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
         *
         * @internal
         */
        abstract class InterimTestCase extends \MolliePrefix\LegacyPHPUnit\TestCase
        {
            use ProphecyTrait;
        }
    } else {
        /**
         * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
         *
         * @internal
         */
        abstract class InterimTestCase extends \MolliePrefix\LegacyPHPUnit\TestCase
        {
        }
    }
    /**
     * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
     *
     * @internal
     */
    abstract class TestCase extends \MolliePrefix\PhpCsFixer\Tests\InterimTestCase
    {
        use ExpectationViaCodeOverAnnotationTrait;
        use ExpectOverSetExceptionTrait;
        use IdentityOverEqualityTrait;
        use PolyfillTrait;
        use ProphecyOverMockObjectTrait;
        use ProphesizeOnlyInterfaceTrait;
    }
} else {
    /**
     * Version without traits for cases when this class is used as a lib.
     *
     * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
     *
     * @internal
     *
     * @todo 3.0 To be removed when we clean up composer prod-autoloader from dev-packages.
     */
    abstract class TestCase extends \MolliePrefix\LegacyPHPUnit\TestCase
    {
    }
}
