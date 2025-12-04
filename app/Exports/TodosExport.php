<?php

namespace App\Exports;

use App\Models\Todo;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class TodosExport implements FromArray, WithHeadings, ShouldAutoSize
{
    protected Collection $todos;

    public function __construct(Collection $todos)
    {
        $this->todos = $todos;
    }

    public function headings(): array
    {
        return [
            'Title',
            'Assignee',
            'Due Date',
            'Time Tracked',
            'Status',
            'Priority',
        ];
    }

    public function array(): array
    {
        $rows = $this->todos->map(function (Todo $todo) {
            return [
                $todo->title,
                $todo->assignee,
                optional($todo->due_date)->format('Y-m-d'),
                (string) ($todo->time_tracked ?? 0),
                $todo->status,
                $todo->priority,
            ];
        })->toArray();

        $rows[] = ['', '', '', '', '', ''];

        $totalTodos = $this->todos->count();
        $totalTimeTracked = $this->todos->sum('time_tracked');

        $rows[] = ['TOTAL', '', '', '', '', ''];
        $rows[] = ["todos: {$totalTodos}", '', '', '', '', ''];
        $rows[] = ["time tracked: {$totalTimeTracked}", '', '', '', '', ''];

        return $rows;
    }
}