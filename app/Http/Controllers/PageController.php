<?php

namespace App\Http\Controllers;


class PageController extends Controller
{
    //

    public function privacyPolicy()
    {
        return view('pages/privacy-policy');
    }

    public function termCondition()
    {
        return view('pages/term-condition');
    }

    public function faq()
    {
        return view('pages/faq');
    }
}
