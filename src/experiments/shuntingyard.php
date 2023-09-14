<?php

// https://en.wikipedia.org/wiki/Shunting_yard_algorithm
// The functions referred to in this algorithm are simple single argument functions such as sine, inverse or factorial.
// This implementation does not implement composite functions, functions with a variable number of arguments, or unary operators.
/*
while there are tokens to be read:
    read a token
    if the token is:
    - a number:
        put it into the output queue
    - a function:
        push it onto the operator stack
    - an operator o1:
        while (
            there is an operator o2 at the top of the operator stack which is not a left parenthesis,
            and (o2 has greater precedence than o1 or (o1 and o2 have the same precedence and o1 is left-associative))
        ):
            pop o2 from the operator stack into the output queue
        push o1 onto the operator stack
    - a ",":
        while the operator at the top of the operator stack is not a left parenthesis:
             pop the operator from the operator stack into the output queue
    - a left parenthesis (i.e. "("):
        push it onto the operator stack
    - a right parenthesis (i.e. ")"):
        while the operator at the top of the operator stack is not a left parenthesis:
            {assert the operator stack is not empty}
            // If the stack runs out without finding a left parenthesis, then there are mismatched parentheses.
            pop the operator from the operator stack into the output queue
        {assert there is a left parenthesis at the top of the operator stack}
        pop the left parenthesis from the operator stack and discard it
        if there is a function token at the top of the operator stack, then:
            pop the function from the operator stack into the output queue
// After the while loop, pop the remaining items from the operator stack into the output queue.
while there are tokens on the operator stack:
    // If the operator token on the top of the stack is a parenthesis, then there are mismatched parentheses.
    {assert the operator on top of the stack is not a (left) parenthesis}
    pop the operator from the operator stack onto the output queue
*/

class Lexer
{
    private array $tokens;
    private int $index;
    private string $look;

    public function __construct(string $expression)
    {
        $this->tokens = [];

        foreach (explode(' ', $expression) as $value) {
            $value = trim($value);
            if (empty($value))
                continue;

            $match = preg_match('/[0-9\*\/\-\+\^\(\)\.]/', $value);
            if ($match) {
                $this->tokens[] = $value;
            } else {
                throw new \Exception("Unrecognized token '$value'", 1);
            }
        }

        $this->index = 0;
        $this->look = '';
    }

    public function next(): string
    {
        if (!isset($this->tokens[$this->index])) {
            return '';
        }

        $this->look = $this->tokens[$this->index];
        $this->index++;
        return $this->look;
    }

    public function isNumber(string $token): bool
    {
        return is_numeric($token);
    }

    public function isOperator(string $token): bool
    {
        return in_array($token, ['+', '-', '*', '/', '^']);
    }

    public function getTokens(): array
    {
        return $this->tokens;
    }
}

class Parser
{
    private Lexer $lexer;
    private array $stack;

    const ASSOC_RIGHT = 'right';
    const ASSOC_LEFT = 'left';

    const OPERATORS = array(
        '^' => [
            'precedence' => 4,
            'assoc' => self::ASSOC_RIGHT,
        ],
        '*' => [
            'precedence' => 3,
            'assoc' => self::ASSOC_LEFT,
        ],
        '/' => [
            'precedence' => 3,
            'assoc' => self::ASSOC_LEFT,
        ],
        '+' => [
            'precedence' => 2,
            'assoc' => self::ASSOC_LEFT,
        ],
        '-' => [
            'precedence' => 2,
            'assoc' => self::ASSOC_LEFT,
        ],
    );

    public function __construct(Lexer $lexer)
    {
        $this->lexer = $lexer;
        $this->stack = [];
    }

    /**
     * Return Reverse Polish Notation
     * @return string
     */
    public function rpn(): string
    {
        $output = '';
        $this->stack = [];

        $token = $this->lexer->next();
        while (!empty($token)) {
            $output .= $this->handleToken($token);
            $token = $this->lexer->next();
        }

        while (count($this->stack) > 0) {
            assert(end($this->stack) !== '(');
            $output .= ' ' . array_pop($this->stack);
        }

        return trim($output);
    }

    private function handleToken(string $token): string
    {
        $output = '';

        switch (true) {
            case $this->lexer->isNumber($token):
                $output .= ' ' . $token;
                break;

            case $this->lexer->isOperator($token):
                $op1 = $token;
                // look at the top of the stack (last element of the array)
                $op2 = end($this->stack);

                while (
                    $op2 !== false &&
                    $op2 !== '(' &&
                    (self::OPERATORS[$op2]['precedence'] > self::OPERATORS[$op1]['precedence'] ||
                        (self::OPERATORS[$op2]['precedence'] === self::OPERATORS[$op1]['precedence'] &&
                        self::OPERATORS[$op1]['assoc'] === self::ASSOC_LEFT))
                ) {
                    $output .= ' ' . array_pop($this->stack);
                    $op2 = end($this->stack);
                }
                $this->stack[] = $op1;
                break;

            case $token === '(':
                $this->stack[] = $token;
                break;

            case $token === ')':
                $topOfStack = end($this->stack);
                while ($topOfStack !== '(') {
                    assert(count($this->stack) > 0);
                    $output .= ' ' . array_pop($this->stack);
                    $topOfStack = end($this->stack);
                }
                assert(end($this->stack) === '(');
                array_pop($this->stack);
                break;

            default:
                throw new \Exception('Error');
                break;
        }

        return $output;
    }
}

class Calculator
{
    private Lexer $lexer;
    private array $stack;

    public function __construct(Lexer $lexer) {
        $this->lexer = $lexer;
        $this->stack = [];
    }

    private function handleToken(string $token)
    {
        if ($this->lexer->isNumber($token)) {
            $this->stack[] = $token;
            return;
        }

        $right = floatval(array_pop($this->stack));
        $left = floatval(array_pop($this->stack));

        switch ($token) {
            case '+':
                $this->stack[] = $left + $right;
                return;
            case '-':
                $this->stack[] = $left - $right;
                return;
            case '*':
                $this->stack[] = $left * $right;
                return;
            case '/':
                $this->stack[] = $left / $right;
                return;
            case '^':
                $this->stack[] = $left ** $right;
                return;
            default:
                throw new Error("Invalid token: $token");
                return;
        }
    }

     /**
     * Return Reverse Polish Notation
     * @return string
     */
    public function calc(): string
    {
        $this->stack = [];

        $token = $this->lexer->next();
        while (!empty($token)) {
            $this->handleToken($token);
            $token = $this->lexer->next();
        }

        return array_pop($this->stack);
    }
}

$expression = '1 + 2 * 3 - 4';
$lexer = new Lexer($expression);
$parser = new Parser($lexer);

echo "Infix: $expression\n";
echo "Expected: 1 2 3 * + 4 -\n";
$rpn = $parser->rpn();
echo "Postfix (result): ".$rpn."\n"; // 1 2 3 * + 4 -

// result
$lexer = new Lexer($rpn);
$calc = new Calculator($lexer);
echo "Expected Result: 3\n";
echo "Result: ".$calc->calc()."\n"; // 3

echo "\n\n";




$expression = '3 + 4 * 2 / ( 1 - 5 ) ^ 2 ^ 3';
$lexer = new Lexer($expression);
$parser = new Parser($lexer);

echo "Infix: $expression\n";
echo "Expected: 3 4 2 * 15 - 23 ^ ^ / +\n";
$rpn = $parser->rpn();
echo "Postfix (result): ".$rpn."\n"; // 3 4 2 * 15 - 23 ^ ^ / +

// result
$lexer = new Lexer($rpn);
$calc = new Calculator($lexer);
echo "Expected Result: 3.0001220703125\n";
echo "Result: ".$calc->calc()."\n"; // 3.0001220703125

echo "\n";
