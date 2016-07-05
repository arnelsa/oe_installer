<?php


class BaseLabResultElement extends BaseEventTypeElement
{
    protected $htmlOptions = array();

    /**
     * @param $input
     * @return array|mixed
     */
    public function getHtmlOptionsForInput($input)
    {
        if(array_key_exists($input, $this->htmlOptions)){
            return $this->htmlOptions[$input];
        }

        return array();
    }

    /**
     * Overrides a set of rules with those of a child class
     *
     * When we override a model we should be able to override it's rules without redefining the entire rule array.
     * Because of the terrible format in which Yii declares it's rules this requires looping both sets of rules to look
     * for matches.
     *
     * @param $parentRules
     * @param $rules
     * @return array
     */
    protected function overrideRules($parentRules, $rules)
    {
        foreach ($parentRules as $parentKey => $parentRule) {
            foreach ($rules as $key => $rule) {
                if (array_slice($parentRule, 0, 2) === array_slice($rule, 0, 2)) {
                    $parentRules[$parentKey] = $rule;
                    unset($rules[$key]);
                }
            }
        }

        foreach ($rules as $rule) {
            $parentRules[] = $rule;
        }

        return $parentRules;
    }
}