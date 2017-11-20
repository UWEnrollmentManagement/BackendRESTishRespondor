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
                "success_message" => "paragraph",
            ],
            "optional" => [
                "retired" => "boolean",
                'root_element_id' => ['reference', 'elements'],
            ],
        ],
        'elements' => [
            "required" => [
                'type' => ["randomElement", ["information", "affirmation", "date", "text-field", "big-text-field", "choice-field", "secure-upload", "secure-upload-multiple", "choices-from-file"]],
                'label' => 'catchPhrase',
            ],
            "optional" => [
                'retired' => "boolean",
                'help_text' => 'catchPhrase',
                'placeholder_text' => 'catchPhrase',
                'required' => "boolean",
                'initial value' => 'catchPhrase',
                'parent_id' => ['reference', 'elements']
            ],
        ],
        'responses' => [
            "required" => [
                'content' => 'sentence',
                'submission_id' => ['reference', 'submissions'],
                'element_id' => ['reference', 'elements'],
            ],
            "optional" => [
            ],
        ],
        'visitors' => [
            'required' => [
                'uw_net_id' => 'userName',
            ],
            'optional' => [
                'uw_student_number' => 'creditCardNumber',
                'first_name' => 'firstName',
                'middle_name' => 'firstName',
                'last_name' => 'lastName',
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
                'value' => 'randomNumber',
            ],
            'optional' => [
            ],
        ],
        'dependencies' => [
            'required' => [
                'element_id' => ['reference', 'elements'],
                'slave_id' => ['reference', 'elements'],
                'condition_id' => ['reference', 'conditions'],
            ],
            'optional' => [
            ],
        ],
        'requirements' => [
            'required' => [
                'element_id' => ['reference', 'elements'],
                'condition_id' => ['reference', 'conditions'],
                'failure_message' => 'sentence',
            ],
            'optional' => [
            ],
        ],
        'submissions' => [
            'required' => [
                'visitor_id' => ['reference', 'visitors'],
                'form_id' => ['reference', 'forms'],
                'status_id' => ['reference', 'statuses'],
                'assignee_id' => ['reference', 'visitors'],
                'submitted' => ['numberBetween', 1410165081, 1510165081],
            ],
            'optional' => [
                'parent_id' => ['reference', 'submissions'],
            ],
        ],
        'statuses' => [
            'required' => [
                'name' => 'word',
                'default_message' => 'sentence'
            ],
            'optional' => [
            ],
        ],
        'tags' => [
            'required' => [
                'name' => 'word',
                'default_message' => 'sentence'
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
                'note_id' => ['reference', 'notes']
            ],
            'optional' => [
            ],
        ],
        'stakeholders' => [
            'required' => [
                'label' => 'catchPhrase',
                'address' => 'email',
                'form_id' => ['reference', 'forms'],
            ],
            'optional' => [
            ],
        ],
        'reactions' => [
            'required' => [
                'subject' => 'title',
                'recipient' => 'email',
                'sender' => 'email',
                'reply_to' => 'email',
                'cc' => 'email',
                'bcc' => 'email',
                'template' => 'paragraph',
                'content' => 'paragraph',
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
                'parent_id' => ['reference', 'forms'],
                'child_id' => ['reference', 'forms'],
                'tag_id' => ['reference', 'tags'],
                'reaction_id' => ['reference', 'reactions'],
            ],
            'optional' => [
            ],
        ],
        'elementchoices' => [
            'required' => [
                'element_id' => ['reference', 'elements'],
                'choice_id' => ['reference', 'choices'],
            ],
            'optional' => [
            ],
        ],
        'submissiontags' => [
            'required' => [
                'submission_id' => ['reference', 'submissions'],
                'tag_id' => ['reference', 'tags'],
            ],
            'optional' => [
            ],
        ],
        'formtags' => [
            'required' => [
                'form_id' => ['reference', 'forms'],
                'tag_id' => ['reference', 'tags'],
            ],
            'optional' => [
            ],
        ],
        'formstatuses' => [
            'required' => [
                'form_id' => ['reference', 'forms'],
                'status_id' => ['reference', 'statuses'],
            ],
            'optional' => [
            ],
        ],
        'formreactions' => [
            'required' => [
                'form_id' => ['reference', 'forms'],
                'reaction_id' => ['reference', 'reactions'],
            ],
            'optional' => [
            ],
        ],
        'dashboardelements' => [
            'required' => [
                'dashboard_id' => ['reference', 'dashboards'],
                'element_id' => ['reference', 'elements'],
            ],
            'optional' => [
            ],
        ],
        'dashboardforms' => [
            'required' => [
                'dashboard_id' => ['reference', 'dashboards'],
                'form_id' => ['reference', 'forms'],
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