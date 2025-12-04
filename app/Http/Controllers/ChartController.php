<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use Illuminate\Http\Request;

class ChartController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->query('type');

        return match ($type) {
            'status'   => $this->statusSummary(),
            'priority' => $this->prioritySummary(),
            'assignee' => $this->assigneeSummary(),
            default    => response()->json([
                'message' => 'Invalid type. Use: status, priority, or assignee'
            ], 400),
        };
    }

    protected function statusSummary()
    {
        $counts = Todo::selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return response()->json([
            'status_summary' => [
                'pending'     => (int) ($counts['pending'] ?? 0),
                'open'        => (int) ($counts['open'] ?? 0),
                'in_progress' => (int) ($counts['in_progress'] ?? 0),
                'completed'   => (int) ($counts['completed'] ?? 0),
            ]
        ]);
    }

    protected function prioritySummary()
    {
        $counts = Todo::selectRaw('priority, COUNT(*) as total')
            ->groupBy('priority')
            ->pluck('total', 'priority');

        return response()->json([
            'priority_summary' => [
                'low'    => (int) ($counts['low'] ?? 0),
                'medium' => (int) ($counts['medium'] ?? 0),
                'high'   => (int) ($counts['high'] ?? 0),
            ]
        ]);
    }

    protected function assigneeSummary()
    {
        $assignees = Todo::select('assignee')
            ->whereNotNull('assignee')
            ->distinct()
            ->pluck('assignee');

        $summary = [];

        foreach ($assignees as $name) {
            $todos = Todo::where('assignee', $name);

            $summary[$name] = [
                'total_todos'                        => (int) $todos->count(),
                'total_pending_todos'                => (int) $todos->clone()->where('status', 'pending')->count(),
                'total_timetracked_completed_todos'  => (float) $todos->clone()->where('status', 'completed')->sum('time_tracked'),
            ];
        }

        return response()->json([
            'assignee_summary' => $summary
        ]);
    }
}