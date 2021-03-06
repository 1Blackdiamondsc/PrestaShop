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
namespace MolliePrefix\PhpCsFixer\Fixer\LanguageConstruct;

use MolliePrefix\PhpCsFixer\AbstractFixer;
use MolliePrefix\PhpCsFixer\Fixer\ConfigurationDefinitionFixerInterface;
use MolliePrefix\PhpCsFixer\FixerConfiguration\AllowedValueSubset;
use MolliePrefix\PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use MolliePrefix\PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use MolliePrefix\PhpCsFixer\FixerDefinition\CodeSample;
use MolliePrefix\PhpCsFixer\FixerDefinition\FixerDefinition;
use MolliePrefix\PhpCsFixer\FixerDefinition\VersionSpecification;
use MolliePrefix\PhpCsFixer\FixerDefinition\VersionSpecificCodeSample;
use MolliePrefix\PhpCsFixer\Preg;
use MolliePrefix\PhpCsFixer\Tokenizer\CT;
use MolliePrefix\PhpCsFixer\Tokenizer\Token;
use MolliePrefix\PhpCsFixer\Tokenizer\Tokens;
/**
 * @author Andreas Möller <am@localheinz.com>
 */
final class SingleSpaceAfterConstructFixer extends \MolliePrefix\PhpCsFixer\AbstractFixer implements \MolliePrefix\PhpCsFixer\Fixer\ConfigurationDefinitionFixerInterface
{
    /**
     * @var array<string, null|int>
     */
    private static $tokenMap = ['abstract' => \T_ABSTRACT, 'as' => \T_AS, 'attribute' => \MolliePrefix\PhpCsFixer\Tokenizer\CT::T_ATTRIBUTE_CLOSE, 'break' => \T_BREAK, 'case' => \T_CASE, 'catch' => \T_CATCH, 'class' => \T_CLASS, 'clone' => \T_CLONE, 'const' => \T_CONST, 'const_import' => \MolliePrefix\PhpCsFixer\Tokenizer\CT::T_CONST_IMPORT, 'continue' => \T_CONTINUE, 'do' => \T_DO, 'echo' => \T_ECHO, 'else' => \T_ELSE, 'elseif' => \T_ELSEIF, 'extends' => \T_EXTENDS, 'final' => \T_FINAL, 'finally' => \T_FINALLY, 'for' => \T_FOR, 'foreach' => \T_FOREACH, 'function' => \T_FUNCTION, 'function_import' => \MolliePrefix\PhpCsFixer\Tokenizer\CT::T_FUNCTION_IMPORT, 'global' => \T_GLOBAL, 'goto' => \T_GOTO, 'if' => \T_IF, 'implements' => \T_IMPLEMENTS, 'include' => \T_INCLUDE, 'include_once' => \T_INCLUDE_ONCE, 'instanceof' => \T_INSTANCEOF, 'insteadof' => \T_INSTEADOF, 'interface' => \T_INTERFACE, 'match' => null, 'named_argument' => \MolliePrefix\PhpCsFixer\Tokenizer\CT::T_NAMED_ARGUMENT_COLON, 'new' => \T_NEW, 'open_tag_with_echo' => \T_OPEN_TAG_WITH_ECHO, 'php_open' => \T_OPEN_TAG, 'print' => \T_PRINT, 'private' => \T_PRIVATE, 'protected' => \T_PROTECTED, 'public' => \T_PUBLIC, 'require' => \T_REQUIRE, 'require_once' => \T_REQUIRE_ONCE, 'return' => \T_RETURN, 'static' => \T_STATIC, 'throw' => \T_THROW, 'trait' => \T_TRAIT, 'try' => \T_TRY, 'use' => \T_USE, 'use_lambda' => \MolliePrefix\PhpCsFixer\Tokenizer\CT::T_USE_LAMBDA, 'use_trait' => \MolliePrefix\PhpCsFixer\Tokenizer\CT::T_USE_TRAIT, 'var' => \T_VAR, 'while' => \T_WHILE, 'yield' => \T_YIELD, 'yield_from' => null];
    /**
     * @var array<string, int>
     */
    private $fixTokenMap = [];
    /**
     * {@inheritdoc}
     */
    public function configure(array $configuration = null)
    {
        parent::configure($configuration);
        if (\defined('T_YIELD_FROM')) {
            self::$tokenMap['yield_from'] = \T_YIELD_FROM;
        }
        if (\defined('T_MATCH')) {
            self::$tokenMap['match'] = T_MATCH;
        }
        $this->fixTokenMap = [];
        foreach ($this->configuration['constructs'] as $key) {
            $this->fixTokenMap[$key] = self::$tokenMap[$key];
        }
        if (isset($this->fixTokenMap['public'])) {
            $this->fixTokenMap['constructor_public'] = \MolliePrefix\PhpCsFixer\Tokenizer\CT::T_CONSTRUCTOR_PROPERTY_PROMOTION_PUBLIC;
        }
        if (isset($this->fixTokenMap['protected'])) {
            $this->fixTokenMap['constructor_protected'] = \MolliePrefix\PhpCsFixer\Tokenizer\CT::T_CONSTRUCTOR_PROPERTY_PROMOTION_PROTECTED;
        }
        if (isset($this->fixTokenMap['private'])) {
            $this->fixTokenMap['constructor_private'] = \MolliePrefix\PhpCsFixer\Tokenizer\CT::T_CONSTRUCTOR_PROPERTY_PROMOTION_PRIVATE;
        }
    }
    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new \MolliePrefix\PhpCsFixer\FixerDefinition\FixerDefinition('Ensures a single space after language constructs.', [new \MolliePrefix\PhpCsFixer\FixerDefinition\CodeSample('<?php

throw  new  \\Exception();
'), new \MolliePrefix\PhpCsFixer\FixerDefinition\CodeSample('<?php

echo  "Hello!";
', ['constructs' => ['echo']]), new \MolliePrefix\PhpCsFixer\FixerDefinition\VersionSpecificCodeSample('<?php

yield  from  baz();
', new \MolliePrefix\PhpCsFixer\FixerDefinition\VersionSpecification(70000), ['constructs' => ['yield_from']])]);
    }
    /**
     * {@inheritdoc}
     */
    public function isCandidate(\MolliePrefix\PhpCsFixer\Tokenizer\Tokens $tokens)
    {
        return $tokens->isAnyTokenKindsFound(\array_values($this->fixTokenMap)) && !$tokens->hasAlternativeSyntax();
    }
    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, \MolliePrefix\PhpCsFixer\Tokenizer\Tokens $tokens)
    {
        $tokenKinds = \array_values($this->fixTokenMap);
        for ($index = $tokens->count() - 2; $index >= 0; --$index) {
            $token = $tokens[$index];
            if (!$token->isGivenKind($tokenKinds)) {
                continue;
            }
            $whitespaceTokenIndex = $index + 1;
            if (';' === $tokens[$whitespaceTokenIndex]->getContent()) {
                continue;
            }
            if ($token->isGivenKind(\T_STATIC) && !$tokens[$tokens->getNextMeaningfulToken($index)]->isGivenKind([\T_FUNCTION, \T_VARIABLE])) {
                continue;
            }
            if ($token->isGivenKind(\T_OPEN_TAG)) {
                if ($tokens[$whitespaceTokenIndex]->equals([\T_WHITESPACE]) && \false === \strpos($token->getContent(), "\n")) {
                    $tokens->clearAt($whitespaceTokenIndex);
                }
                continue;
            }
            if ($token->isGivenKind(\T_CLASS) && $tokens[$tokens->getNextMeaningfulToken($index)]->equals('(')) {
                continue;
            }
            if ($token->isGivenKind([\T_EXTENDS, \T_IMPLEMENTS]) && $this->isMultilineExtendsOrImplementsWithMoreThanOneAncestor($tokens, $index)) {
                continue;
            }
            if ($token->isGivenKind(\T_RETURN) && $this->isMultiLineReturn($tokens, $index)) {
                continue;
            }
            if (!$tokens[$whitespaceTokenIndex]->equals([\T_WHITESPACE])) {
                $tokens->insertAt($whitespaceTokenIndex, new \MolliePrefix\PhpCsFixer\Tokenizer\Token([\T_WHITESPACE, ' ']));
            } elseif (' ' !== $tokens[$whitespaceTokenIndex]->getContent()) {
                $tokens[$whitespaceTokenIndex] = new \MolliePrefix\PhpCsFixer\Tokenizer\Token([\T_WHITESPACE, ' ']);
            }
            if (70000 <= \PHP_VERSION_ID && $token->isGivenKind(\T_YIELD_FROM) && 'yield from' !== \strtolower($token->getContent())) {
                $tokens[$index] = new \MolliePrefix\PhpCsFixer\Tokenizer\Token([\T_YIELD_FROM, \MolliePrefix\PhpCsFixer\Preg::replace('/\\s+/', ' ', $token->getContent())]);
            }
        }
    }
    protected function createConfigurationDefinition()
    {
        return new \MolliePrefix\PhpCsFixer\FixerConfiguration\FixerConfigurationResolver([(new \MolliePrefix\PhpCsFixer\FixerConfiguration\FixerOptionBuilder('constructs', 'List of constructs which must be followed by a single space.'))->setAllowedTypes(['array'])->setAllowedValues([new \MolliePrefix\PhpCsFixer\FixerConfiguration\AllowedValueSubset(\array_keys(self::$tokenMap))])->setDefault(\array_keys(self::$tokenMap))->getOption()]);
    }
    /**
     * @param int $index
     *
     * @return bool
     */
    private function isMultiLineReturn(\MolliePrefix\PhpCsFixer\Tokenizer\Tokens $tokens, $index)
    {
        ++$index;
        $tokenFollowingReturn = $tokens[$index];
        if (!$tokenFollowingReturn->isGivenKind(\T_WHITESPACE) || \false === \strpos($tokenFollowingReturn->getContent(), "\n")) {
            return \false;
        }
        $nestedCount = 0;
        for ($indexEnd = \count($tokens) - 1, ++$index; $index < $indexEnd; ++$index) {
            if (\false !== \strpos($tokens[$index]->getContent(), "\n")) {
                return \true;
            }
            if ($tokens[$index]->equals('{')) {
                ++$nestedCount;
            } elseif ($tokens[$index]->equals('}')) {
                --$nestedCount;
            } elseif (0 === $nestedCount && $tokens[$index]->equalsAny([';', [\T_CLOSE_TAG]])) {
                break;
            }
        }
        return \false;
    }
    /**
     * @param int $index
     *
     * @return bool
     */
    private function isMultilineExtendsOrImplementsWithMoreThanOneAncestor(\MolliePrefix\PhpCsFixer\Tokenizer\Tokens $tokens, $index)
    {
        $hasMoreThanOneAncestor = \false;
        while (++$index) {
            $token = $tokens[$index];
            if ($token->equals(',')) {
                $hasMoreThanOneAncestor = \true;
                continue;
            }
            if ($token->equals('{')) {
                return \false;
            }
            if ($hasMoreThanOneAncestor && \false !== \strpos($token->getContent(), "\n")) {
                return \true;
            }
        }
        return \false;
    }
}
