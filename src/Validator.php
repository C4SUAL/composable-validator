<?php

class Validator
{
    const INVALID_KEY_ERROR = "Key '%s' is not permitted";

    private $rules;

    private $messages = [];

    /**
     * @param $data
     * @return bool
     */
    public function isValid($data)
    {
        if (empty($data)) {
            return false;
        }

        foreach($data as $key => $value) {

            if (is_array($value)) {

                /**
                 * @var Validator $validator
                 */
                $validator = (isset($this->rules[$key]) && $this->rules[$key] instanceof Validator)
                    ? $this->rules[$key]
                    : clone $this;

                $valid = $validator->isValid($value);

                if (!$valid) {
                    $this->messages[$key] = $validator->getInvalidKeys();
                }
                continue;
            }

            $valid = in_array($key, $this->rules, true);

            if (!$valid) {
                $this->addMessage($key);
            }
        }

        return count($this->messages) === 0;
    }

    public function add($input)
    {
        if (is_array($input)) {
            $this->rules = $input;
        }
    }

    public function getInvalidKeys()
    {
        return $this->messages;
    }

    public function getMessages()
    {
        return $this->_getMessages($this->messages, null);
    }

    private function _getMessages($messages, $parent)
    {
        $collect = [];
        foreach($messages as $key => $message)
        {
            $key = ($parent === null) ? $key : $parent . '[' . $key . ']';

            if (is_array($message)) {
                $collect = array_merge($collect, $this->_getMessages($message, $key));
                continue;
            }
            $collect[] = sprintf(self::INVALID_KEY_ERROR, $key);
        }
        return $collect;
    }

    private function addMessage($key)
    {
        $this->messages[$key] = sprintf(self::INVALID_KEY_ERROR, $key);
    }

    private function resetMessages()
    {
        $this->messages = [];
    }

    public function __clone()
    {
        $this->resetMessages();
    }
}
