<?php

namespace spec\Mailchimp;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class MailchimpSpec extends ObjectBehavior
{

    function it_is_initializable()
    {
        $this->shouldHaveType('Mailchimp\Mailchimp');
    }

    function it_should_return_a_collection_object()
    {
        $this->beConstructedWith('ea400f0d078e0ddddf638e95e69f9b0f-us10');

        $this->request('lists', [
            'fields' => 'lists.id,lists.name,lists.stats.member_count',
            'count'  => 10
        ])->shouldReturnAnInstanceOf('Illuminate\Support\Collection');
    }
}
