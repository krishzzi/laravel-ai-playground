<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Tool;
use Illuminate\Http\Request;

class ToolController extends Controller
{
    public function index(Request $request)
    {
        return Tool::availableTo($request->user())->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'slug' => 'required|alpha_dash|unique:tools,slug',
            'name' => 'required|string|max:80',
            'description' => 'required|string|max:500',
            'prompt_template' => 'nullable|string|max:5000',
            'kind' => 'required|in:prompt,mcp,provider_tool,webhook',
            'mcp_server_id' => 'nullable|exists:mcp_servers,id',
            'is_public' => 'boolean',
        ]);

        return $request->user()->tools()->create($data);
    }

    public function show(Tool $tool)
    {
        return $tool;
    }

    public function update(Request $request, Tool $tool)
    {
        abort_unless($tool->user_id === $request->user()->id, 403);
        $tool->update($request->validate([
            'name' => 'sometimes|string|max:80',
            'description' => 'sometimes|string|max:500',
            'prompt_template' => 'nullable|string|max:5000',
            'is_active' => 'sometimes|boolean',
            'is_public' => 'sometimes|boolean',
        ]));

        return $tool;
    }

    public function destroy(Request $request, Tool $tool)
    {
        abort_unless($tool->user_id === $request->user()->id, 403);
        $tool->delete();

        return response()->noContent();
    }
}
