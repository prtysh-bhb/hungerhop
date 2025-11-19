<?php

namespace App\Http\Controllers\Admin\Tenant;

use App\Http\Controllers\Controller;
use App\Models\MenuTemplate;

class MenuTemplateController extends Controller
{
    public function index()
    {
        $templates = MenuTemplate::with('categories.items.variations')->get();

        return view('admin.tenant.menu_templates.index', compact('templates'));
    }

    public function show(MenuTemplate $menuTemplate)
    {
        $menuTemplate->load('categories.items.variations');

        return view('admin.tenant.menu_templates.show', compact('menuTemplate'));
    }
}
