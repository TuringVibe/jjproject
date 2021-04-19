<?php
namespace App\Http\Controllers;

use App\Services\ProjectService;
use App\Services\TaskService;

class ProjectDashboardController extends Controller {

    public function dashboard()
    {
        $this->config['title'] = "PROJECT DASHBOARD";
        $this->config['active'] = "project-dashboard";
        $this->config['navs'] = [
            [
                'label' => 'Project Dashboard'
            ]
        ];
        $this->config['project_statistic'] = (new ProjectService())->statistic();
        $this->config['task_statistic'] = (new TaskService())->statistic();
        return view('pages.project-dashboard', $this->config);
    }
}
