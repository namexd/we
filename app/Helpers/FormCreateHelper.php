<?php

namespace App\Helpers;
use LaravelFormBuilder\Form;

class FormCreateHelper extends Form
{

    const TEXT = 'text';
    const PASSWORD = 'password';
    const NUMBER = 'number';

    public function __construct($action = '', array $components = [], $token = false)
    {
        parent::__construct($action, $components,$token);
    }
    public function buildFilter()
    {
        $api['rule'] = $this->getRules();
        return $api;
    }
}
