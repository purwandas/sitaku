<?php

namespace App\Http\Controllers;

use App\Components\Helpers\FormBuilderHelper;
use Illuminate\Http\Request;
use \App\Role;

class HomeController extends Controller
{
    public $label = "Input";

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // return var_dump(get_loaded_extensions());
        $data = [
            'title' => $this->label,
            'icon'  => 'fa fa-user-md',
            'breadcrumb' => [
                ['label' => $this->label],
            ]
        ];

        $form_data = new FormBuilderHelper(Role::class,$data);
        $final     = $form_data
            ->setCustomFormBuilder([
                'password' => [
                    'type' => 'password'
                ],
                'email' => [
                    'type' => 'email'
                ],
                'number' => [
                    'type' => 'number'
                ],
                'file' => [
                    'type' => 'file'
                ],
                'textarea' => [
                    'type' => 'textarea',
                    'withPlugins' => true
                ],
                'date' => [
                    'type' => 'date'
                ],
                'daterange' => [
                    'type' => 'daterange'
                ],
                'switch' => [
                    'type' => 'switch'
                ],
                'multiple' => [
                    'type' => 'multiple'
                ],
                'select' => [
                    'type' => 'select',
                    'options' => [
                        1 => 'satu',
                        2 => 'dua',
                        3 => 'tiga',
                    ]
                ],
                'select2_1' => [
                    'type' => 'select2',
                    'options' => [
                        1 => 'satu',
                        2 => 'dua',
                        3 => 'tiga',
                    ]
                ],
                'select2_2' => [
                    'type' => 'select2',
                    'options' => 'user.select2',
                ],
                'location' => [
                    'type' => 'location',
                    'name' => 'location'
                ],
            ])
            ->useDatatable(false)
            ->useModal(false)
            ->get();
        
        return view('components.global_form', $final);
        return view('home');
    }
}
