<?php

namespace spec;

use Validator;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ValidatorSpec extends ObjectBehavior
{
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
                [ 'name' => 'sku', 'value' => 'ABC' ],
                [ 'name' => 'catgeory', 'value' => 'childrens books' ]
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
            [ 'name' => 'sku', 'value' => 'ABC' ],
            [ 'name' => 'catgeory', 'value' => 'childrens books' ]
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
            [ 'name' => 'sku', 'value' => 'ABC' ],
            [ 'name' => 'catgeory', 'amount' => 'childrens books' ]
        ];

        $this->isValid($input)->shouldReturn(false);
    }
}
