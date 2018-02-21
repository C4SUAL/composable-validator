<?php

namespace spec;

use Validator;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ValidatorSpec extends ObjectBehavior
{
    public function it_returns_false_when_given_empty_input()
    {
        $this->isValid([])->shouldReturn(false);
        $this->isValid(null)->shouldReturn(false);
        $this->isValid('')->shouldReturn(false);
    }

    public function it_allows_you_to_define_the_structure_of_the_input()
    {
        $meta_fields = new Validator();
        $meta_fields->add([
            'name',
            'value'
        ])
        ;
        $this->add([
            'id',
            'start_date',
            'meta_fields' => $meta_fields
        ]);

        $input = [
            'id' => 1,
            'start_date' => '2018-06-29',
            'meta_fields' => [
                'name' => 'sku',
                'value' => 'ABC'
            ]
        ];

        $this->isValid($input)->shouldReturn(true);
    }

    public function it_validates_a_nested_collection_of_items()
    {
        $meta_fields = new Validator();
        $meta_fields->add([
            'name',
            'value'
        ])
        ;
        $this->add([
            'id',
            'start_date',
            'meta_fields' => $meta_fields,
            'cookie_period'
        ]);

        $input = [
            'id' => 1,
            'start_date' => '2018-06-29',
            'meta_fields' => [
                [ 'name' => 'sku',      'value' => 'ABC' ],
                [ 'name' => 'category', 'value' => 'books' ]
            ],
            'cookie_period' => 300
        ];

        $this->isValid($input)->shouldReturn(true);
    }

    public function it_validates_a_collection()
    {
        $this->add([
            'name',
            'value'
        ]);

        $input = [
            [ 'name' => 'sku',      'value' => 'ABC' ],
            [ 'name' => 'category', 'value' => 'books' ]
        ];

        $this->isValid($input)->shouldReturn(true);
    }

    public function it_returns_false_if_an_item_in_the_collection_is_not_valid()
    {
        $this->add([
            'name',
            'value'
        ]);

        $input = [
            [ 'name' => 'sku',      'value' => 'ABC' ],
            [ 'name' => 'category', 'val'   => 'books' ]
        ];

        $this->isValid($input)->shouldReturn(false);
    }

    public function it_collects_invalid_keys()
    {
        $this->add([
            'name',
            'value'
        ]);

        $this->isValid([
            'invalid' => 1,
            'value'   => 2,
            'mixup'   => 3
        ]);

        $this->getInvalidKeys()->shouldReturn([
            'invalid'=>"Key 'invalid' is not permitted",
            'mixup'  =>"Key 'mixup' is not permitted"
        ]);
    }

    public function it_collects_invalid_keys_in_nested_validators()
    {
        $meta_fields = new Validator();
        $meta_fields->add([
            'name',
            'value'
        ]);

        $this->add([
            'id',
            'meta_fields' => $meta_fields
        ]);

        $input = [
            'id' => 1,
            'start_date' => '2018-06-29',
            'meta_fields' => [
                [ 'foo'  => 1, 'value' => 1 ],
                [ 'name' => 1, 'value' => 1 ],
                [ 'bar'  => 1, 'baz'   => 1 ]
            ]
        ];

        $this->isValid($input);

        $this->getInvalidKeys()->shouldReturn([
            'start_date'=>"Key 'start_date' is not permitted",
            'meta_fields' => [
                0 => [ 'foo' => "Key 'foo' is not permitted" ],
                2 => [
                    'bar' => "Key 'bar' is not permitted",
                    'baz' => "Key 'baz' is not permitted",
                ]
            ]
        ]);
    }

    public function it_collects_invalid_keys_from_a_collection_of_objects()
    {
        $this->add([
            'name',
            'value'
        ]);

        $input = [
            [ 'name1' => 'sku', 'value' => 'ABC' ],
            [ 'name' => 'category', 'value2' => 'books' ]
        ];

        $this->isValid($input);

        $this->getInvalidKeys()->shouldReturn([
            [ 'name1' => "Key 'name1' is not permitted" ],
            [ 'value2' => "Key 'value2' is not permitted" ]
        ]);
    }

    public function it_returns_messages_in_a_flat_array()
    {
        $meta_fields = new Validator();
        $meta_fields->add([
            'name',
            'value'
        ]);

        $this->add([
            'id',
            'meta_fields' => $meta_fields
        ]);

        $input = [
            'id' => 1,
            'start_date' => '2018-06-29',
            'meta_fields' => [
                [ 'foo'  => 1, 'value' => 1 ],
                [ 'name' => 1, 'value' => 1 ],
                [ 'bar'  => 1, 'baz'   => 1 ]
            ]
        ];

        $this->isValid($input);

        $this->getMessages()->shouldReturn([
            "Key 'start_date' is not permitted",
            "Key 'meta_fields[0][foo]' is not permitted",
            "Key 'meta_fields[2][bar]' is not permitted",
            "Key 'meta_fields[2][baz]' is not permitted"
        ]);
    }
}
