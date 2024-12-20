<?php

namespace App\Repositories;

use App\Models\Task;

class TaskRepository extends BaseRepository
{
    public function getModel()
    {
        return Task::class;
    }

    public function filter($data)
    {
        $query = $this->model->query();
        if (isset($data['difficulty_level'])) {
            $query->where('difficulty_level', $data['difficulty_level']);
        }
        if (isset($data['code'])) {
            $query->where('code', $data['code']);
        }
        if (isset($data['employee'])) {
            $query->whereHas('employees', function ($q) use ($data) {
                $q->where('employees.id', $data['employee']);
            });
        }
        if (isset($data['name'])) {
            $query->where('name', 'like', '%' . $data['name'] . '%');
        }

        if (isset($data['project'])) {
            $query->where('project_id', $data['project']);
        }

        return $query->orderBy('id', 'desc')->paginate($data['size'] ?? 10);
    }

    public function syncEmployee(array $data, $id)
    {
        $task = $this->model->findOrFail($id);
        return $task->employees()->sync($data['employee_id']);
    }

    public function countTask()
    {
        return [
            'name' => 'Công việc',
            'total_task' => $this->model->count(),
            'total_easy_task' => $this->model->where('difficulty_level', 'easy')->count(),
            'total_medium_task' => $this->model->where('difficulty_level', 'medium')->count(),
            'total_hard_task' => $this->model->where('difficulty_level', 'hard')->count(),
            'total_veryhard_task' => $this->model->where('difficulty_level', 'veryhard')->count(),
        ];
    }

    public function compareRatioDificultyByProject($project_ids)
    {
        $projects = $this->model
            ->select('projects.name as project_name', 'tasks.difficulty_level', \DB::raw('COUNT(*) as count'))
            ->rightJoin('projects', 'tasks.project_id', '=', 'projects.id')
            ->whereIn('projects.id', $project_ids)
            ->groupBy('projects.name', 'tasks.difficulty_level')
            ->get()
            ->groupBy('project_name');

        $data = [
            ['project', 'easy', 'medium', 'hard', 'very_hard']
        ];

        foreach ($project_ids as $projectId) {
            $projectName = \DB::table('projects')
                ->where('id', $projectId)
                ->value('name');

            $taskCounts = $projects->get($projectName, collect())->pluck('count', 'difficulty_level')->toArray();
            $data[] = [
                'project' => $projectName,
                'easy' => $taskCounts['easy'] ?? 0,
                'medium' => $taskCounts['medium'] ?? 0,
                'hard' => $taskCounts['hard'] ?? 0,
                'very_hard' => $taskCounts['very_hard'] ?? 0,
            ];
        }

        return $data;
    }

}
