<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Device;
use App\Models\Command;
use App\Services\AIService;

class DashboardController extends Controller
{
    public function index()
    {
        $userCount = User::count();
        $deviceCount = Device::count();
        $activeDevices = Device::active()->count();
        $pendingCommands = Command::pending()->count();
        
        $recentCommands = Command::with(['device', 'user'])->latest()->take(5)->get();
        
        $aiService = new AIService();
        $aiFleetInsights = $aiService->getFleetHealthOverview();
        $adminStrategicAdvice = $aiService->getAdminStrategicAdvice();
        $adminStrategicChartData = $aiService->getAdminStrategicChartData();

        return view('admin.dashboard.index', compact(
            'userCount', 
            'deviceCount', 
            'activeDevices', 
            'pendingCommands',
            'recentCommands',
            'aiFleetInsights',
            'adminStrategicAdvice',
            'adminStrategicChartData'
        ));
    }
}
