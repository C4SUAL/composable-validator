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

            if (is_array($value) && isset($this->rules[$key]) && $this->rules[$key] instanceof Validator) {
                /**
                 * @var Validator $validator
                 */
                $validator = $this->rules[$key];
                $valid = $validator->isValid($value);
                if (!$valid) {
                    $this->collectMessages($validator->getMessages(), $key);
                }
                continue;
            }

            if (is_array($value)) {
                $validator = clone $this;
                $valid = $validator->isValid($value);
                if (!$valid) {
                    $this->collectMessages($validator->getMessages(), $key);
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
        if (array_key_exists($key, $this->messages)) {
            // a collection
            $this->messages[$key][] = $arr;
        } else {
            $this->messages[$key] = $arr;
        }
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
