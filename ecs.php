<?php

declare(strict_types=1);

use Symplify\EasyCodingStandard\Config\ECSConfig;
use Symplify\EasyCodingStandard\ValueObject\Set\SetList;

return static function (ECSConfig $ecsConfig): void {
    $ecsConfig->parallel();

    $ecsConfig->paths([
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ]);

    $header = <<<'EOF'
This file is part of DbUnit.

(c) Sebastian Bergmann <sebastian@phpunit.de>

For the full copyright and license information, please view the LICENSE
file that was distributed with this source code.
EOF;

    $ecsConfig->ruleWithConfiguration(PhpCsFixer\Fixer\Comment\HeaderCommentFixer::class, [
        'header' => $header,
    ]);

    $ecsConfig->skip([
        PhpCsFixer\Fixer\Operator\NotOperatorWithSuccessorSpaceFixer::class,
        PhpCsFixer\Fixer\StringNotation\ExplicitStringVariableFixer::class,
    ]);

    $ecsConfig->sets([
        SetList::PSR_12,
        SetList::SPACES,
        SetList::CLEAN_CODE,
        SetList::ARRAY,
        SetList::PHPUNIT,
        SetList::CONTROL_STRUCTURES,
        SetList::NAMESPACES,
    ]);
};
