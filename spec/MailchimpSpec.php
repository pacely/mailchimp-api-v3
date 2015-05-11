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

	function it_should_use_the_default_datacenter_if_omitted_in_apikey()
	{
		$this->beConstructedWith('apikeywithoutdatacenter');

		$this->getEndpoint()->shouldReturn('https://us1.api.mailchimp.com/3.0/');
	}

	function it_should_change_datacenter_if_api_key_has_one()
	{
		$this->beConstructedWith('apikeywith-us10');

		$this->getEndpoint()->shouldReturn('https://us10.api.mailchimp.com/3.0/');
	}

	function it_should_return_a_json_object()
	{
		$this->beConstructedWith('ea400f0d078e0ddddf638e95e69f9b0f-us10');

		$this->request('lists')->shouldReturnAnInstanceOf('Illuminate\Support\Collection');
		$this->request('lists/a2fbf5901c')->shouldReturnAnInstanceOf('Illuminate\Support\Collection');
	}
}
