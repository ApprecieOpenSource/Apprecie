<?php
namespace Apprecie\Library\SourceParsing;

/**
 * Provides a function call (and parameter parser) for a php file.
 *
 * Parses function calls (name set by configuration) and arguments of primitive types.
 * Note that all other arguments will be set to null.
 *
 */
class PHPFunctionCallParser extends SourceFileParser
{
    private $_functionNames;

    /**
     * Specialised SourceFileParser to extract function calls matching the names ($functionNames) from
     * the php source file $filePath.
     *
     * Call ->Parse() before trying to iterate over the parse results!! else the collection will be empty.
     *
     * @param $filePath
     * @param $functionNames
     * @throws \InvalidArgumentException
     * @throws \Exception
     */
    public function __construct($filePath, $functionNames)
    {
        try {
            if ($functionNames == null) {
                throw new \InvalidArgumentException('functionNames must contain a string scalar or array fo string indicating the functions you will accept as results');
            }

            if (!is_array($functionNames)) {
                $this->_functionNames = array($functionNames);
            } else {
                $this->_functionNames = $functionNames;
            }

            parent::__construct($filePath, 'Apprecie\Library\SourceParsing\FunctionCallMeta');
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * Parses function calls a primitive arguments from the provided source file adding
     * the results to the internal collection.
     *
     * Based almost completely on Http://www.phpkode.com/source/s/postie/postie/util/wp-i18n/extract/extract.php
     */
    public function parse()
    {
        $latest_comment = false;
        $in_func = false;

        foreach ($this->_tokens as $token) {
            $id = $text = null;

            if (is_array($token)) {
                list($id, $text, $line) = $token;
            }
            if (T_WHITESPACE == $id) {
                continue;
            }

            if (T_STRING == $id && in_array($text, $this->_functionNames) && !$in_func) {
                $in_func = true;
                $paren_level = -1;
                $args = array();
                $func_name = $text;
                $func_line = $line;
                $func_comment = $latest_comment ? $latest_comment : '';

                $just_got_into_func = true;
                $latest_comment = false;
                continue;
            }

            if (!$in_func) {
                continue;
            }

            if ('(' == $token) {
                $paren_level++;

                if (0 == $paren_level) { // start of first argument
                    $just_got_into_func = false;
                    $current_argument = null;
                    $current_argument_is_just_literal = true;
                }
                continue;
            }

            if ($just_got_into_func) {
                // there wasn't a opening paren just after the function name -- this means it is not a function
                $in_func = false;
                $just_got_into_func = false;
            }

            if (')' == $token) {
                if (0 == $paren_level) {
                    $in_func = false;
                    $args[] = $current_argument;
                    $call = array('name' => $func_name, 'args' => $args, 'line' => $func_line);
                    if ($func_comment) {
                        $call['comment'] = $func_comment;
                    }

                    $fCall = new FunctionCallMeta($func_name, $args, $func_line, $this->getFilePath());
                    $this->add($fCall);
                }

                $paren_level--;
                continue;
            }

            if (',' == $token && 0 == $paren_level) {
                $args[] = $current_argument;
                $current_argument = null;
                $current_argument_is_just_literal = true;
                continue;
            }

            if ((T_CONSTANT_ENCAPSED_STRING == $id || T_LNUMBER == $id || $id == T_DNUMBER || strtolower(
                        $text
                    ) == 'true' || strtolower($text) == 'false') && $current_argument_is_just_literal
            ) {
                // we can use eval safely, because we are sure $text is just a string literal
                eval('$current_argument = ' . $text . ';');
                continue;
            }

            if (strtolower($text) == 'null') {
                $current_argument = 'null';
                $current_argument_is_just_literal = true;
                continue;
            }

            $current_argument_is_just_literal = false;
            $current_argument = '{{complex}}';
        }
    }

    /**
     * @param int $index
     * @return FunctionCallMeta
     */
    public function get($index)
    {
        return parent::get($index);
    }
}