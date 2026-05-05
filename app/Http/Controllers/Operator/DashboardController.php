<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\Command;
use App\Services\AIService;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = auth()->id();
        
        $myDevicesCount = Device::assignedTo($userId)->count();
        $myCommandsCount = Command::byUser($userId)->count();
        
        $successCommands = Command::byUser($userId)->where('status', 'success')->count();
        $successRate = $myCommandsCount > 0 ? round(($successCommands / $myCommandsCount) * 100) : 0;
        
        $recentCommands = Command::byUser($userId)->with('device')->latest()->take(5)->get();
        
        $myDevices = Device::assignedTo($userId)->with(['type', 'data'])->get();
        
        $aiService = new AIService();
        $healthDistribution = $aiService->getOperatorHealthDistribution($userId);

        return view('operator.dashboard.index', compact(
            'myDevicesCount', 
            'myCommandsCount', 
            'successRate',
            'recentCommands',
            'myDevices',
            'healthDistribution'
        ));
    }
}
