<?php

namespace Hypo\LayoutBundle\DependencyInjection;

class VariableTokenParser extends \Twig_TokenParser
{
    public function parse(\Twig_Token $token)
    {
        $capture = true;
		  $lineno = $token->getLine();
        $stream = $this->parser->getStream();
		  
        
        if( ! $stream->test(\Twig_Token::BLOCK_END_TYPE) ){
            $variable = $this->parser->getExpressionParser()->parseExpression();
        }
        
        if( ! $stream->test(\Twig_Token::BLOCK_END_TYPE) ){
            $merge = $this->parser->getExpressionParser()->parseExpression();
        }

        if( ! isset($variable) ) throw new \Twig_Error_Syntax("Variable must be defined at line $lineno.", $lineno);
        if( ! isset($merge) ) $merge = new \Twig_Node_Expression_Constant(null, $lineno);

        $stream->expect(\Twig_Token::BLOCK_END_TYPE);
        $source = $this->parser->subparse(array($this, 'decideBlockEnd'), true);
        $stream->expect(\Twig_Token::BLOCK_END_TYPE);

        return new VariableNode($capture, $variable, $merge, $source, $lineno, $this->getTag());
    }
    public function decideBlockEnd(\Twig_Token $token)
    {
        return $token->test('end'.$this->getTag());
    }   

    public function getTag()
    {
        return 'var';
    }
}