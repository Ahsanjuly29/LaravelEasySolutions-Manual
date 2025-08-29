<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;

class FrontendController extends Controller
{

    function dashboard()
    {
        return view('admin.dashboard', []);
    }

    function company()
    {
        return view('admin.company.index', [
            'allData' => Company::orderBy('id', 'DESC')->paginate(10)
        ]);
    }
}
