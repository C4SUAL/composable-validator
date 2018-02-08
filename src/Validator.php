<?php

class Validator
{
    private $rules;

    public function isValid($data)
    {
        if (empty($data)) {
            return false;
        }

        $valid = true;

        foreach($data as $key => $value) {

            if (is_array($value) && isset($this->rules[$key]) && $this->rules[$key] instanceof Validator) {
                $validator = $this->rules[$key];
                $valid = $validator->isValid($value);
                continue;
            }

            if (is_array($value)) {
                //$validator = clone $this;
                $valid = $this->isValid($value);
                continue;
            }

            $valid = in_array($key, $this->rules, true);

            if (!$valid) {
                break;
            }
        }

        return $valid;
    }

    public function add($input)
    {
        if (is_array($input)) {
            $this->rules = $input;
        }
    }
}
