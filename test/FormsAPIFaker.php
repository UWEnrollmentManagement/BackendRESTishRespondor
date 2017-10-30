<?php

namespace FormsAPI\Test;


class FormsAPIFaker
{

    /** @var  \Faker\Generator */
    protected $faker;

    /**
     * @var array $fieldsMap
     *
     * An array that maps element fields to \Faker\Generator\ methods.
     *
     * For example:
     *
     * $fieldsMap = [
     *   "forms" => [
     *     "required" => [
     *       "name" => "catchPhrase",
     *       "slug" => "slug",
     *       "successMessage" => "paragraph",
     *     ],
     *     "optional" => [
     *       "retired" => "boolean",
     *     ],
     *   ],
     *   ...
     *
     * Here the resource type "forms" has three required fields and one optional
     * field. The required field "name" can be produced by the \Faker\Generator
     * method `catchPhrase`, etc.
     */
    protected static $fieldsMap = [
        "forms" => [
            "required" => [
                "name" => "catchPhrase",
                "slug" => "slug",
                "successMessage" => "paragraph",
            ],
            "optional" => [
                "retired" => "boolean",
            ],
        ],
        "elements" => [
            "required" => [
                'type' => ["randomElement", ["information", "affirmation", "date", "text-field", "big-text-field", "choice-field", "secure-upload", "secure-upload-multiple", "choices-from-file"]],
                'label' => 'catchPhrase',
            ],
            "optional" => [
                'retired' => "boolean",
                'helpText' => 'catchPhrase',
                'placeholderText' => 'catchPhrase',
                'required' => "boolean",
                'initial value' => 'catchPhrase',
            ],
        ],
        "visitors" => [

        ]
    ];

    /**
     * FormsAPIFaker constructor.
     */
    public function __construct()
    {
        $this->faker = \Faker\Factory::create();
    }

    /**
     * Helper function which calls the \Faker\Generator methods to produce mock
     * data.
     *
     * For example, a $template of:
     *   ['name' => 'catchphrase', 'slug' => 'slug']
     *
     * might yield:
     *   ['name' => 'Reactive Octo-Interpretter', 'slug' => 'blue-dogs-fruit']
     *
     * @param string[] $template
     * @return array
     */
    protected function makeResult($template)
    {
        $result = [];
        foreach ($template as $key => $method) {
            if(gettype($method) === "string") {
                $result[$key] = $this->faker->$method;
            } else {
                $result[$key] = $this->faker->{$method[0]}($method[1]);
            }
        }

        return $result;
    }

    /**
     * Produce fake data for a given resource type, to include required and optional
     * attributes.
     *
     * For example, `fake(
     *
     * @param string $resourceType
     * @param string[] $data
     * @return string[]
     */
    public function fake($resourceType, $data = [])
    {
        $template = array_merge(
            static::$fieldsMap[$resourceType]['required'],
            static::$fieldsMap[$resourceType]['optional']
        );

        return array_merge($this->makeResult($template), $data);
    }

    public function fakeRequiredOnly($resourceType, $data = [])
    {
        $template = static::$fieldsMap[$resourceType]['required'];

        return array_merge($this->makeResult($template), $data);
    }

}
