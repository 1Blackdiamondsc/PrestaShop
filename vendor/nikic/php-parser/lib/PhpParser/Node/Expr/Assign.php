<?php

namespace MolliePrefix\PhpParser\Node\Expr;

use MolliePrefix\PhpParser\Node\Expr;
class Assign extends \MolliePrefix\PhpParser\Node\Expr
{
    /** @var Expr Variable */
    public $var;
    /** @var Expr Expression */
    public $expr;
    /**
     * Constructs an assignment node.
     *
     * @param Expr  $var        Variable
     * @param Expr  $expr       Expression
     * @param array $attributes Additional attributes
     */
    public function __construct(\MolliePrefix\PhpParser\Node\Expr $var, \MolliePrefix\PhpParser\Node\Expr $expr, array $attributes = array())
    {
        parent::__construct($attributes);
        $this->var = $var;
        $this->expr = $expr;
    }
    public function getSubNodeNames()
    {
        return array('var', 'expr');
    }
}
