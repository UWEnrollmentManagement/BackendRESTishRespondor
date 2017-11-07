<?php

namespace FormsAPI\Test;


class FormsAPIFaker
{

    /** @var \Faker\Generator $faker */
    protected $faker;

    /** @var callable[] $extraFormatters */
    protected $extraFormatters;

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
                'rootElementId' => ['reference', 'elements'],
            ],
        ],
        'elements' => [
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
                'parentId' => ['reference', 'elements']
            ],
        ],
        'visitors' => [
            'required' => [
                'uwNetID' => 'userName',
            ],
            'optional' => [
                'uwStudentNumber' => 'creditCardNumber',
                'firstName' => 'firstName',
                'middleName' => 'firstName',
                'lastName' => 'lastName',
            ],
        ],
        'choices' => [
            'required' => [
                'value' => 'word',
            ],
            'optional' => [
            ],
        ],
        'conditions' => [
            'required' => [
                'operator' => ['randomElement', ['is', 'is not', 'less than', 'greater than', 'less than or equal to', 'greater than or equal to', 'maximum length', 'minimum length', 'exact length', 'regex']],
                'value' => ['reference', 'submissions'],
            ],
            'optional' => [
            ],
        ],
        'dependencies' => [
            'required' => [
                'elementId' => 'catchPhrase',
                'slaveId' => ['reference', 'elements'],
                'conditionId' => ['reference', 'conditions'],
            ],
            'optional' => [
            ],
        ],
        'requirements' => [
            'required' => [
                'elementId' => ['reference', 'elements'],
                'conditionId' => ['reference', 'submissions'],
                'failureMessage' => 'sentence',
            ],
            'optional' => [
            ],
        ],
        'submissions' => [
            'required' => [
                'visitorId' => ['reference', 'visitors'],
                'formId' => ['reference', 'forms'],
                'statusId' => ['reference', 'statuses'],
                'assigneeId' => ['reference', 'visitors'],
                'parentId' => ['reference', 'submissions'],
                'submitted' => 'datetime',
            ],
            'optional' => [
            ],
        ],
        'statuses' => [
            'required' => [
                'name' => 'word',
                'defaultMessage' => 'sentence'
            ],
            'optional' => [
            ],
        ],
        'tags' => [
            'required' => [
                'name' => 'word',
                'defaultMessage' => 'sentence'
            ],
            'optional' => [
            ],
        ],
        'notes' => [
            'required' => [
                'content' => 'paragraph',
                'subject' => 'title',
            ],
            'optional' => [
            ],
        ],
        'recipients' => [
            'required' => [
                'address' => 'email',
            ],
            'optional' => [
            ],
        ],
        'stakeholders' => [
            'required' => [
                'label' => 'catchPhrase',
                'address' => 'email',
                'formId' => ['reference', 'forms'],
            ],
            'optional' => [
            ],
        ],
        'reactions' => [
            'required' => [
                'subject' => 'title',
                'recipient' => 'email',
                'sender' => 'email',
                'replyTo' => 'email',
                'cc' => 'email',
                'bcc' => 'email',
                'template' => 'paragraphs',
                'content' => 'paragraphs',
            ],
            'optional' => [
            ],
        ],
        'settings' => [
            'required' => [
                'key' => 'word',
                'value' => 'word',
            ],
            'optional' => [
            ],
        ],
        'dashboards' => [
            'required' => [
                'name' => 'catchPhrase',
            ],
            'optional' => [
            ],
        ],
        'childformrelationships' => [
            'required' => [
                'parentId' => ['reference', 'forms'],
                'childId' => ['reference', 'forms'],
                'tagId' => ['reference', 'tags'],
                'reactionId' => ['reference', 'reactions'],
            ],
            'optional' => [
            ],
        ],
        'elementchoices' => [
            'required' => [
                'elementId' => ['reference', 'elements'],
                'choiceId' => ['reference', 'choices'],
            ],
            'optional' => [
            ],
        ],
        'submissiontags' => [
            'required' => [
                'submissionId' => ['reference', 'submissions'],
                'tagId' => ['reference', 'tags'],
            ],
            'optional' => [
            ],
        ],
        'formtags' => [
            'required' => [
                'formId' => ['reference', 'form'],
                'tagId' => ['reference', 'tags'],
            ],
            'optional' => [
            ],
        ],
        'formreactions' => [
            'required' => [
                'formId' => ['reference', 'forms'],
                'reactionId' => ['reference', 'reactions'],
            ],
            'optional' => [
            ],
        ],
        'dashboardelements' => [
            'required' => [
                'dashboardId' => ['reference', 'dashboard'],
                'elementId' => ['reference', 'elements'],
            ],
            'optional' => [
            ],
        ],
        'dashboardforms' => [
            'required' => [
                'dashboardId' => ['reference', 'dashboards'],
                'formId' => ['reference', 'forms'],
            ],
            'optional' => [
            ],
        ],

    ];

    /**
     * FormsAPIFaker constructor.
     */
    public function __construct(array $extraFormatters = [])
    {
        $this->faker = \Faker\Factory::create();
        $this->extraFormatters = $extraFormatters;
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
        foreach ($template as $key => $methodSpec) {

            if(gettype($methodSpec) === "string") {
                $methodName = $methodSpec;
                $methodArguments = null;
            } else {
                $methodName = $methodSpec[0];
                $methodArguments = $methodSpec[1];
            }

            if (array_key_exists($methodName, $this->extraFormatters)) {
                $method = $this->extraFormatters[$methodName];
                $result[$key] = $method($methodArguments);
            } else {
                $result[$key] = $this->faker->$methodName($methodArguments);
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