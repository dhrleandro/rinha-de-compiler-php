<?php

namespace LeandroDaher\RinhaDeCompilerPhp;

use LeandroDaher\RinhaDeCompilerPhp\GenericStack;

class Register
{
    const AX = 'AX';
    const BX = 'BX';

    public static function isRegister(string $register)
    {
        $reflectionClass = new \ReflectionClass(__CLASS__);
        $registers = array_keys($reflectionClass->getConstants());

        return in_array($register, $registers);
    }
}

// OpCode mnemonics
class OpCode
{
    const DEF = 'DEF';
    const MOV = 'MOV';

    const PUSH_SR = 'PUSH_SR'; // push stack register
    const POP_SR = 'POP_SR'; // pop stack register
    const PUSH_SP = 'PUSH_SP'; // push stack param
    const POP_SP = 'POP_SP'; // pop stack param
    const PUSH_SRET = 'PUSH_SRET'; // push stack return
    const POP_SRET = 'POP_SRET'; // pop stack return

    const JMP = 'JMP'; // jump to address
    const JT = 'JT'; // jump to addres if true
    const JF = 'JF'; // jump to addres if false

    const CALL = 'CALL';
    const RET = 'RET';
    const PRINT = 'PRINT';

    const CMP = 'CMP'; // compare

    const ADD = 'ADD';
    const SUB = 'SUB';
    const MUL = 'MUL';
    const DIV = 'DIV';
    // const NEG = 'NEG';

    const EQ = 'EQ';
    const NEQ = 'NEQ';
    const LT = 'LT';
    const GT = 'GT';
    const LTE = 'LTE';
    const GTE = 'GTE';
    const AND = 'AND';
    const OR = 'OR';

    const SET_EQ = 'SET_EQ';
    const SET_NEQ = 'SET_NEQ';
    const SET_LT = 'SET_LT';
    const SET_GT = 'SET_GT';
    const SET_LTE = 'SET_LTE';
    const SET_GTE = 'SET_GTE';
    const SET_AND = 'SET_AND';
    const SET_OR = 'SET_OR';

    public static function isOpCode(string $opcode)
    {
        $reflectionClass = new \ReflectionClass(__CLASS__);
        $opcodes = array_keys($reflectionClass->getConstants());

        return in_array($opcode, $opcodes);
    }
}

// LDHRVM
class LDHRVirtualMachine
{
    private array $bytecode;

    /**
     * Key - Value Index of label
     * @var int[]
     */
    private array $labels;

    /**
     * Can be a numeric value or an $label key
     * @var array<int>[] key-value array of array\<int\>
     */
    private array $scopes;
    private int $scopeIndex;

    /**
     * @var GenericStack<int>
     */
    private GenericStack $registerStack;

    /**
     * @var GenericStack<int>
     */
    private GenericStack $paramStack;

    /**
     * @var GenericStack<int>
     */
    private GenericStack $returnStack;

    private $registerState = [
        Register::AX => 0,
        Register::BX => 0,
    ];

    private $compareState = [
        OpCode::EQ => false,
        OpCode::NEQ => false,
        OpCode::LT => false,
        OpCode::GT => false,
        OpCode::LTE => false,
        OpCode::GTE => false,
        OpCode::AND => false,
        OpCode::OR => false,
    ];

    public function __construct(string $bytecode)
    {

        $this->labels = [];

        $lines = explode("\n", $bytecode);
        $commands = array();

        // clean bytecode
        foreach ($lines as $line) {
            $line = trim(strtoupper($line));

            $explode = [];
            $params = [];

            if (!empty($line))
                $explode = explode(' ', $line);

            foreach ($explode as $key => $param) {
                // ignore comments
                if ($param == ';' || $param == ':')
                    break;

                if ($key == 0 && !OpCode::isOpCode($param) && !$this->isLabel($param))
                    throw new \Exception("$param is not a OpCode or Label", 1);

                $params[] = $param;
            }

            if (count($params) > 0)
                $commands[] = $params;

            $lastInsertedCommand = end($commands)[0];
            if ($this->isLabel($lastInsertedCommand)) {
                $commandIndex = count($commands) - 1;
                $lastInsertedCommand = str_replace(':', '', $lastInsertedCommand);
                $this->labels[$lastInsertedCommand] = $commandIndex;
            }
        }

        $this->bytecode = $commands;

        $this->scopeIndex = 0;
        $this->scopes = [];
        $this->scopes[$this->scopeIndex] = [];

        $this->registerStack = new GenericStack('integer');
        $this->paramStack = new GenericStack('integer');
        $this->returnStack = new GenericStack('integer');
    }

    public function isLabel(string $str)
    {
        return strpos($str, ':') !== false;
    }

    public function pushStack(GenericStack $stack, string $param1)
    {
        $value = null;
        if (Register::isRegister($param1)) {
            $value = $this->registerState[$param1];
        } elseif (isset($this->scopes[$this->scopeIndex][$param1])) {
            $value = $this->scopes[$this->scopeIndex][$param1];
        } elseif (is_numeric($param1)) {
            $value = intval($param1);
        } else {
            throw new \Exception("Unexpected Error", 1);
        }
        $stack->push($value);
    }

    public function popStack(GenericStack $stack, string $param1)
    {
        if (!Register::isRegister($param1) && !isset($this->scopes[$this->scopeIndex][$param1])) {
            throw new \Exception("$param1 must be a Register or Var", 1);
        }
        if (Register::isRegister($param1)) {
            $this->registerState[$param1] = $stack->pop();
        } else {
            $this->scopes[$this->scopeIndex][$param1] = $stack->pop();
        }
    }

    public function isRegisterOrVariable(string $param): bool
    {
        return Register::isRegister($param) || isset($this->scopes[$this->scopeIndex][$param]);
    }

    public function checkIsRegisterOrVariable(string $param): void
    {
        if (!$this->isRegisterOrVariable($param))
            throw new \Exception("$param is not a register or a variable");
    }

    public function setValue(string $origin, string $destiny): void
    {
        if (Register::isRegister($origin)) {

            if (Register::isRegister($destiny)) {
                $this->registerState[$destiny] = $this->registerState[$origin];
            } elseif (isset($this->scopes[$this->scopeIndex][$destiny])) {
                $this->scopes[$this->scopeIndex][$destiny] = $this->registerState[$origin];
            }
        } elseif (isset($this->scopes[$this->scopeIndex][$origin])) {

            if (Register::isRegister($destiny)) {
                $this->registerState[$destiny] = $this->scopes[$this->scopeIndex][$origin];
            } elseif (isset($this->scopes[$this->scopeIndex][$destiny])) {
                $this->scopes[$this->scopeIndex][$destiny] = $this->scopes[$this->scopeIndex][$origin];
            }
        } elseif (!Register::isRegister($destiny) && isset($this->labels[$origin])) {
            $this->scopes[$this->scopeIndex][$destiny] = $origin;
        } elseif (is_numeric($origin)) {

            if (Register::isRegister($destiny)) {
                $this->registerState[$destiny] = intval($origin);
            } elseif (isset($this->scopes[$this->scopeIndex][$destiny])) {
                $this->scopes[$this->scopeIndex][$destiny] = intval($origin);
            }
        } else {
            throw new \Exception("Unexpected Error", 1);
        }
    }

    public function getValue(string $identifier): mixed
    {
        if (Register::isRegister($identifier)) {
            $value = $this->registerState[$identifier];
        } elseif (isset($this->scopes[$this->scopeIndex][$identifier])) {
           $value = $this->scopes[$this->scopeIndex][$identifier];
           /*if (isset($this->labels[$value])) {
            $value = $this->labels[$value];
           }*/
        } else {
            throw new \Exception("Unexpected Error", 1);
        }

        return intval($value);
    }

    public function getLableIndex(string $identifier): int
    {
        $value = null;
        if (isset($this->labels[$identifier])) {
            $value = $this->labels[$identifier];
        } elseif (isset($this->scopes[$this->scopeIndex][$identifier])) {
            $label = $this->scopes[$this->scopeIndex][$identifier];
            if (isset($this->labels[$label])) {
                $value = $this->labels[$label];
            }
        } elseif (isset($this->scopes[0][$identifier])) {
            $label = $this->scopes[0][$identifier];
            if (isset($this->labels[$label])) {
                $value = $this->labels[$label];
            }
        } else {
            throw new \Exception("Unexpected Error", 1);
        }

        return $value;
    }

    public function compare($left, $right): void
    {
        $left = $this->getValue($left);
        $right = $this->getValue($right);

        $this->compareState[OpCode::EQ] = $left == $right;
        $this->compareState[OpCode::NEQ] = $left != $right;
        $this->compareState[OpCode::LT] = $left < $right;
        $this->compareState[OpCode::GT] = $left > $right;
        $this->compareState[OpCode::LTE] = $left <= $right;
        $this->compareState[OpCode::GTE] = $left >= $right;
        $this->compareState[OpCode::AND] = $left && $right;
        $this->compareState[OpCode::OR] = $left || $right;
    }

    public function getCompareState(string $operator): int
    {
        if (!isset($this->compareState[$operator]))
            throw new Exception("Error Request", 1);

        return $this->compareState[$operator] ? 1 : 0;
    }

    public function interpret()
    {
        $head = 0; // index of line
        $retIndex = -1; // index of return for line after CALL, if retIndex < 0 => exit
        $returning = false; // if retindex is used this value is equal true
        $jumping = false;
        $exit = false;

        while ($head < count($this->bytecode) && !$exit) {
            $jumping = false;

            $line = $this->bytecode[$head];
            $opcode = $line[0];

            // debug
            echo "[$head] " . implode(' ', $line) . "\n";

            if ($this->isLabel($opcode)) {
                $opcode = 'LABEL';
            }

            switch ($opcode) {

                case OpCode::RET:
                    $this->scopeIndex--;
                    if ($retIndex < 0) {
                        $exit = true;
                    } else {
                        $returning = true;
                    }
                    break;

                case OpCode::DEF:
                    $param = $line[1];
                    if (isset($this->scopes[$this->scopeIndex][$param]))
                        throw new \Exception("$param has already been defined");
                    $this->scopes[$this->scopeIndex][$param] = 0;
                    break;

                case OpCode::MOV:
                    $param1 = $line[1];
                    $param2 = $line[2];

                    $this->checkIsRegisterOrVariable($param1);

                    $this->setValue($param2, $param1);
                    break;

                case OpCode::PUSH_SR:
                    $param1 = $line[1];
                    $this->pushStack($this->registerStack, $param1);
                    break;

                case OpCode::POP_SR:
                    $param1 = $line[1];
                    $this->popStack($this->registerStack, $param1);
                    break;

                case OpCode::PUSH_SP:
                    $param1 = $line[1];
                    $this->pushStack($this->paramStack, $param1);
                    break;

                case OpCode::POP_SP:
                    $param1 = $line[1];
                    $this->popStack($this->paramStack, $param1);
                    break;

                case OpCode::PUSH_SRET:
                    $param1 = $line[1];
                    $this->pushStack($this->returnStack, $param1);
                    break;

                case OpCode::POP_SRET:
                    $param1 = $line[1];
                    $this->popStack($this->returnStack, $param1);
                    break;

                case OpCode::CALL:
                    $this->scopeIndex++;
                    $param1 = $line[1];
                    $retIndex = $head;
                    $head = $this->getLableIndex($param1);
                    break;

                case OpCode::CMP:
                    $left = $line[1];
                    $right = $line[2];
                    $this->compare($left, $right);
                    break;

                case OpCode::SET_EQ:
                case OpCode::SET_NEQ:
                case OpCode::SET_LT:
                case OpCode::SET_GT:
                case OpCode::SET_LTE:
                case OpCode::SET_GTE:
                case OpCode::SET_AND:
                case OpCode::SET_OR:
                    $operator = str_replace('SET_', '', $opcode);
                    $param = $line[1];
                    $this->checkIsRegisterOrVariable($param);
                    $this->setValue($this->getCompareState($operator), $param);
                    break;

                case OpCode::SUB:
                    $param1 = $line[1];
                    $param2 = $line[2];

                    $this->checkIsRegisterOrVariable($param1);
                    $result = $this->getValue($param2) - $this->getValue($param1);
                    $this->setValue($result, $param1);
                    break;

                case OpCode::JMP:
                    $label = $line[1];
                    $label = $this->getLableIndex($label);
                    $head = $label;
                    $jumping = true;
                    break;

                case OpCode::JT:
                    $param1 = $line[1];
                    $label = $line[2];
                    $this->checkIsRegisterOrVariable($param1);
                    $label = $this->getLableIndex($label);
                    if ($this->getValue($param1) == 1) {
                        $head = $label;
                        $jumping = true;
                    }
                    break;

                case OpCode::JF:
                    $param1 = $line[1];
                    $label = $line[2];
                    $this->checkIsRegisterOrVariable($param1);
                    $label = $this->getLableIndex($label);
                    if ($this->getValue($param1) == 0) {
                        $head = $label;
                        $jumping = true;
                    }
                    break;

                case OpCode::PRINT:
                    $param1 = $line[1];
                    $this->checkIsRegisterOrVariable($param1);
                    echo "PRINT: ".$this->getValue($param1)."\n";
                    break;

                case 'LABEL':
                    echo "__label__\n";
                    break;

                default:
                    var_dump($line);

                    var_dump($this->registerState);
                    var_dump($this->registerStack->getList());
                    var_dump($this->paramStack->getList());
                    var_dump($this->returnStack->getList());
                    var_dump("Scope index: ".$this->scopeIndex);
                    var_dump("Scopes: ");
                    var_dump($this->scopes);
                    var_dump($this->labels);

                    throw new \Exception("$opcode Unexpected Error", 1);
                    break;
            }

            if ($returning) {
                $head = $retIndex; // head return to call
                $retIndex = -1;
                $returning = false;
            }

            if (!$jumping) {
                $head++; // next
            }
        }

        echo "\nEnd Of File\n";
    }

    function printBytecode()
    {
        foreach ($this->bytecode as $key => $line) {
            echo "[$key] ".implode(' ', $line)."\n";
        }
        echo "\n\n";
    }
}
