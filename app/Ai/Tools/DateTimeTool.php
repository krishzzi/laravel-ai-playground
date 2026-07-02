<?php

namespace App\Ai\Tools;

/**
 * ─────────────────────────────────────────────────────────────
 * Tool: DateTimeTool
 * ─────────────────────────────────────────────────────────────
 * Provides date/time capabilities to the AI agent:
 *   now        – current datetime in any timezone
 *   convert    – convert a datetime between timezones
 *   difference – human-readable gap between two dates
 *   add        – add a duration to a date
 *   subtract   – subtract a duration from a date
 *   format     – reformat a date string
 *
 * Uses Carbon (already a Laravel core dependency).
 * ─────────────────────────────────────────────────────────────
 */

use Carbon\Carbon;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

class DateTimeTool implements Tool
{
    public function description(): Stringable|string
    {
        return
            'Provides date and time operations with timezone support. ' .
            'Use "now" to get the current time, "convert" to translate between ' .
            'timezones, "difference" to measure gaps between two dates, ' .
            '"add"/"subtract" to perform date arithmetic, and "format" to ' .
            'reformat a date into any PHP date-format string.';
    }

    public function handle(Request $request): Stringable|string
    {
        $op  = (string) $request['operation'];
        $tz  = (string) ($request['timezone'] ?? 'UTC');
        $d1  = (string) ($request['date'] ?? '');
        $d2  = (string) ($request['date2'] ?? '');

        try {
            return match ($op) {
                'now'        => $this->now($tz),
                'convert'    => $this->convert($d1, $tz),
                'difference' => $this->difference($d1, $d2),
                'add'        => $this->addOrSub('add', $d1, (int)($request['amount'] ?? 0), (string)($request['unit'] ?? 'days')),
                'subtract'   => $this->addOrSub('sub', $d1, (int)($request['amount'] ?? 0), (string)($request['unit'] ?? 'days')),
                'format'     => $this->format($d1, (string)($request['format'] ?? 'Y-m-d H:i:s')),
                default      => "Unknown operation '{$op}'.",
            };
        } catch (\Throwable $e) {
            return 'Error: ' . $e->getMessage();
        }
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'operation' => $schema->string()
                ->enum(['now', 'convert', 'difference', 'add', 'subtract', 'format'])
                ->description('Which date/time operation to perform.')
                ->required(),

            'timezone' => $schema->string()
                ->description(
                    'IANA timezone name e.g. "America/New_York", "Europe/London", ' .
                    '"Asia/Kolkata". Defaults to UTC.'
                ),

            'date' => $schema->string()
                ->description(
                    'ISO-8601 date string or natural language date. ' .
                    'Required for all operations except "now".'
                ),

            'date2' => $schema->string()
                ->description('Second date for the "difference" operation.'),

            'amount' => $schema->integer()
                ->description('Amount to add or subtract. Required for "add"/"subtract".'),

            'unit' => $schema->string()
                ->enum(['seconds', 'minutes', 'hours', 'days', 'weeks', 'months', 'years'])
                ->description('Time unit for add/subtract operations.'),

            'format' => $schema->string()
                ->description(
                    'PHP date() format string for the "format" operation. ' .
                    'E.g. "D, d M Y", "g:i A", "Y-m-d\\TH:i:sP"'
                ),
        ];
    }

    // ──────────────────────────────────────────────────────────
    // Private operation methods
    // ──────────────────────────────────────────────────────────

    private function now(string $tz): string
    {
        $dt = Carbon::now($this->safeTz($tz));
        return $dt->format('l, F j, Y \a\t g:i:s A') . " ({$tz})";
    }

    private function convert(string $date, string $tz): string
    {
        $dt = Carbon::parse($date)->setTimezone($this->safeTz($tz));
        return $dt->format('l, F j, Y \a\t g:i:s A') . " ({$tz})";
    }

    private function difference(string $d1, string $d2): string
    {
        $a    = Carbon::parse($d1);
        $b    = Carbon::parse($d2);
        $diff = $a->diff($b);

        $parts = array_filter([
            $diff->y ? "{$diff->y} year(s)"   : null,
            $diff->m ? "{$diff->m} month(s)"  : null,
            $diff->d ? "{$diff->d} day(s)"    : null,
            $diff->h ? "{$diff->h} hour(s)"   : null,
            $diff->i ? "{$diff->i} minute(s)" : null,
            $diff->s ? "{$diff->s} second(s)" : null,
        ]);

        if (empty($parts)) {
            return 'The two dates are identical.';
        }

        $direction = $a->lessThan($b) ? 'until' : 'since';
        return implode(', ', $parts) . " {$direction} the second date.";
    }

    private function addOrSub(string $op, string $date, int $amount, string $unit): string
    {
        $dt = Carbon::parse($date);

        $op === 'add'
            ? $dt->add($unit, $amount)
            : $dt->sub($unit, $amount);

        return $dt->toIso8601String();
    }

    private function format(string $date, string $format): string
    {
        return Carbon::parse($date)->format($format);
    }

    /** Validate timezone string; fall back to UTC on failure. */
    private function safeTz(string $tz): string
    {
        try {
            new \DateTimeZone($tz);
            return $tz;
        } catch (\Exception) {
            return 'UTC';
        }
    }
}
