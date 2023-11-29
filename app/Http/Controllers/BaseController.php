<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class BaseController extends Controller
{
    public $data = [];

    public function __construct()
    {
        $this->mainTitle = 'Main';
        $this->mainURL = '#';
        $this->pageTitle = 'Page';
        $this->pageURL = '#';
        $this->actionButtons = [];
        $this->recordAddURL = '#';
    }

    public function __set($name, $value)
    {
        $this->data[$name] = $value;
        View::share($name, $value);
    }

    public function __get($name)
    {
        return $this->data[$name];
    }

    public function __isset($name)
    {
        return isset($this->data[ $name ]);
    }
}
