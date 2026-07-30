<?php

namespace App\Http\Controllers;

use App\Support\ServerStatusService;
use Illuminate\View\View;

class ServerStatusController extends Controller
{
    public function index(): View
    {
        $config = module_config('server_status');
        $servers = ServerStatusService::resolve($config);

        return view('theme::servers.index', [
            'servers' => $servers,
            'pageTitle' => $config['page_title'] ?? __('nav.servers'),
            'pageIntro' => $config['page_intro'] ?? __('servers.intro'),
        ]);
    }
}
