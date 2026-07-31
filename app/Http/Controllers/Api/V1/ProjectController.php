<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        return $request->user()->projects()->withCount('chatSessions')->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:80',
            'color' => 'nullable|string|max:20',
            'instructions' => 'nullable|string|max:5000',
        ]);

        return $request->user()->projects()->create($data);
    }

    public function show(Request $request, Project $project)
    {
        abort_unless($project->user_id === $request->user()->id, 403);

        return $project->load('chatSessions');
    }

    public function update(Request $request, Project $project)
    {
        abort_unless($project->user_id === $request->user()->id, 403);

        $project->update($request->validate([
            'name' => 'sometimes|string|max:80',
            'color' => 'nullable|string|max:20',
            'instructions' => 'nullable|string|max:5000',
            'default_tool_ids' => 'nullable|array',
            'default_skill_ids' => 'nullable|array',
        ]));

        return $project;
    }

    public function destroy(Request $request, Project $project)
    {
        abort_unless($project->user_id === $request->user()->id, 403);
        $project->delete();

        return response()->noContent();
    }
}
