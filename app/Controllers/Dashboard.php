<?php

namespace App\Controllers;

class Dashboard extends BaseController
{
    public function index()
    {
        return $this->render('dashboard/index', [
            'title' => 'Dashboard'
        ]);
    }
}
