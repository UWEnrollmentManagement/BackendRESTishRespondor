<?php

namespace FormsAPI\Test;


class FormsAPIFakerTest extends \PHPUnit_Framework_TestCase
{

    public function testDefaultAttributes()
    {
        $faker = new FormsAPIFaker();

        $fakeForm = $faker->fake('forms');

        $this->assertArrayHasKey('name', $fakeForm);
        $this->assertArrayHasKey('slug', $fakeForm);
        $this->assertArrayHasKey('successMessage', $fakeForm);
        $this->assertArrayHasKey('retired', $fakeForm);

        $this->assertInternalType(gettype(''), $fakeForm['name']);
        $this->assertInternalType(gettype(''), $fakeForm['slug']);
        $this->assertInternalType(gettype(''), $fakeForm['successMessage']);
        $this->assertInternalType(gettype(False), $fakeForm['retired']);

        $this->assertEquals(4, sizeof($fakeForm));
    }

    public function testRequiredOnlyAttributes()
    {
        $faker = new FormsAPIFaker();

        $fakeForm = $faker->fakeRequiredOnly('forms');

        $this->assertArrayHasKey('name', $fakeForm);
        $this->assertArrayHasKey('slug', $fakeForm);
        $this->assertArrayHasKey('successMessage', $fakeForm);

        $this->assertInternalType(gettype(''), $fakeForm['name']);
        $this->assertInternalType(gettype(''), $fakeForm['slug']);
        $this->assertInternalType(gettype(''), $fakeForm['successMessage']);

        $this->assertEquals(3, sizeof($fakeForm));
    }

    public function testProvideSomeAttributes()
    {
        $faker = new FormsAPIFaker();

        $specificName = 'Specific Name for this Test';
        $extraAttributeKey = 'extra-attribute';
        $extraAttributeValue = 'extra-attribute-value';

        $fakeForm = $faker->fake('forms', ['name' => $specificName, $extraAttributeKey => $extraAttributeValue]);

        $this->assertArrayHasKey('name', $fakeForm);
        $this->assertArrayHasKey('slug', $fakeForm);
        $this->assertArrayHasKey('successMessage', $fakeForm);
        $this->assertArrayHasKey('retired', $fakeForm);
        $this->assertArrayHasKey($extraAttributeKey, $fakeForm);

        $this->assertInternalType(gettype(''), $fakeForm['name']);
        $this->assertInternalType(gettype(''), $fakeForm['slug']);
        $this->assertInternalType(gettype(''), $fakeForm['successMessage']);
        $this->assertInternalType(gettype(False), $fakeForm['retired']);
        $this->assertInternalType(gettype($extraAttributeValue), $fakeForm[$extraAttributeKey]);

        $this->assertEquals($specificName, $fakeForm['name']);
        $this->assertEquals($extraAttributeValue, $fakeForm[$extraAttributeKey]);

        $this->assertEquals(5, sizeof($fakeForm));
    }

    public function testProvideSomeAttributesToRequiredOnly()
    {
        $faker = new FormsAPIFaker();

        $specificName = 'Specific Name for this Test';
        $extraAttributeKey = 'extra-attribute';
        $extraAttributeValue = 'extra-attribute-value';

        $fakeForm = $faker->fakeRequiredOnly('forms', ['name' => $specificName, $extraAttributeKey => $extraAttributeValue]);

        $this->assertArrayHasKey('name', $fakeForm);
        $this->assertArrayHasKey('slug', $fakeForm);
        $this->assertArrayHasKey('successMessage', $fakeForm);
        $this->assertArrayHasKey($extraAttributeKey, $fakeForm);

        $this->assertInternalType(gettype(''), $fakeForm['name']);
        $this->assertInternalType(gettype(''), $fakeForm['slug']);
        $this->assertInternalType(gettype(''), $fakeForm['successMessage']);
        $this->assertInternalType(gettype($extraAttributeValue), $fakeForm[$extraAttributeKey]);

        $this->assertEquals($specificName, $fakeForm['name']);
        $this->assertEquals($extraAttributeValue, $fakeForm[$extraAttributeKey]);

        $this->assertEquals(4, sizeof($fakeForm));
    }

}