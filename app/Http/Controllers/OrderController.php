<?php

namespace App\Http\Controllers;

class OrderController extends Controller
{
    public function create()
    {
        return view('orders.create');
    }
}
