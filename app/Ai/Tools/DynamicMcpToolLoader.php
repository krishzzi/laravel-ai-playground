<?php

namespace App\Ai\Tools;

use App\Models\McpServer;
use Illuminate\Support\Facades\Cache;

/**
 * Bridges a user's registered MCP server (third-party, or their own
 * project's MCP endpoint) into live AI SDK tools. Tool lists are cached
 * briefly per server to avoid a network round trip on every chat turn.
 */
class DynamicMcpToolLoader
{
    public function forServer(int $mcpServerId): array
    {
        $server = McpServer::find($mcpServerId);

        if (! $server || ! $server->is_active) {
            return [];
        }

        return Cache::remember("mcp_tools:{$server->id}", now()->addMinutes(5), function () use ($server) {
            try {
                return $server->client()->tools()->all();
            } catch (\Throwable $e) {
                report($e);

                return [];
            }
        });
    }
}
