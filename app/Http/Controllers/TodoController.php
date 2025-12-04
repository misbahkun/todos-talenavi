<?php

namespace App\Http\Controllers;

use App\Exports\TodosExport;
use App\Http\Requests\StoreTodoRequest;
use App\Models\Todo;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class TodoController extends Controller
{
    public function store(StoreTodoRequest $request)
    {
        $data = $request->validated();

        $data['status'] = $data['status'] ?? 'pending';

        $data['time_tracked'] = $data['time_tracked'] ?? 0;

        $todo = Todo::create($data);

        return response()->json([
            'message' => 'Todo created successfully',
            'data'    => $todo,
        ], 201);
    }

    protected function applyFilters(Request $request, $query)
    {
        if ($request->filled('title')) {
            $query->where('title', 'like', '%' . $request->title . '%');
        }

        if ($request->filled('assignee')) {
            $assignees = array_filter(
                array_map('trim', explode(',', $request->assignee))
            );
            if (!empty($assignees)) {
                $query->whereIn('assignee', $assignees);
            }
        }

        if ($request->filled('start')) {
            $query->whereDate('due_date', '>=', $request->start);
        }
        if ($request->filled('end')) {
            $query->whereDate('due_date', '<=', $request->end);
        }

        if ($request->filled('min')) {
            $query->where('time_tracked', '>=', $request->min);
        }
        if ($request->filled('max')) {
            $query->where('time_tracked', '<=', $request->max);
        }

        if ($request->filled('status')) {
            $statuses = array_filter(
                array_map('trim', explode(',', $request->status))
            );
            if (!empty($statuses)) {
                $query->whereIn('status', $statuses);
            }
        }

        if ($request->filled('priority')) {
            $priorities = array_filter(
                array_map('trim', explode(',', $request->priority))
            );
            if (!empty($priorities)) {
                $query->whereIn('priority', $priorities);
            }
        }

        return $query;
    }

    public function exportAndSave(Request $request)
    {
        $query = Todo::query();

        $query = $this->applyFilters($request, $query);

        $todos = $query->get();

        $filename = 'todos_' . now()->format('Ymd_His') . '.xlsx';

        Excel::store(new TodosExport($todos), 'exports/' . $filename, 'public');

        $fileUrl = asset('storage/exports/' . $filename);

        return response()->json([
            'message'   => 'Excel generated and stored successfully',
            'file_name' => $filename,
            'file_url'  => $fileUrl,
        ]);
    }
}