<?php

class Validator
{
    const INVALID_KEY_ERROR = "Key '%s' is not permitted";

    private $rules;

    private $messages = [];

    public function isValid($data)
    {
        if (empty($data)) {
            return false;
        }

        $loopValid = true;

        foreach($data as $key => $value) {

            if (is_array($value) && isset($this->rules[$key]) && $this->rules[$key] instanceof Validator) {
                $validator = $this->rules[$key];
                $valid = $validator->isValid($value);
                if (!$valid) {
                    $this->collectMessages($validator->getMessages(), $key);
                    $loopValid = false;
                }
                continue;
            }

            if (is_array($value)) {
                $validator = clone $this;
                $valid = $validator->isValid($value);
                if (!$valid) {
                    $this->collectMessages($validator->getMessages(), $key);
                    $loopValid = false;
                }
                continue;
            }

            $valid = in_array($key, $this->rules, true);

            if (!$valid) {
                $this->addMessage($key);
                $loopValid = false;
            }
        }

        return $loopValid;
    }

    public function add($input)
    {
        if (is_array($input)) {
            $this->rules = $input;
        }
    }

    public function getMessages()
    {
        return $this->messages;
    }

    private function addMessage($key)
    {
        $this->messages[$key] = sprintf(self::INVALID_KEY_ERROR, $key);
    }

    private function collectMessages($arr, $key)
    {
        $this->messages[$key] = $arr;
    }
}
