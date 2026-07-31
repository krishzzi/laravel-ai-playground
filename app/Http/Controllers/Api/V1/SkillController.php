<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Skill;
use Illuminate\Http\Request;

class SkillController extends Controller
{
    public function index(Request $request)
    {
        return Skill::availableTo($request->user())->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'slug' => 'required|alpha_dash|unique:skills,slug',
            'name' => 'required|string|max:80',
            'description' => 'required|string|max:500',
            'instructions' => 'required|string|max:8000',
            'is_public' => 'boolean',
        ]);

        return $request->user()->skills()->create($data);
    }

    public function show(Skill $skill)
    {
        return $skill;
    }

    public function update(Request $request, Skill $skill)
    {
        abort_unless($skill->user_id === $request->user()->id, 403);
        $skill->update($request->validate([
            'name' => 'sometimes|string|max:80',
            'description' => 'sometimes|string|max:500',
            'instructions' => 'sometimes|string|max:8000',
            'is_active' => 'sometimes|boolean',
            'is_public' => 'sometimes|boolean',
        ]));

        return $skill;
    }

    public function destroy(Request $request, Skill $skill)
    {
        abort_unless($skill->user_id === $request->user()->id, 403);
        $skill->delete();

        return response()->noContent();
    }
}
