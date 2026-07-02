<?php

namespace App\Ai\Tools;

/**
 * ─────────────────────────────────────────────────────────────
 * Tool: CalculatorTool
 * ─────────────────────────────────────────────────────────────
 * Gives the agent the ability to evaluate mathematical
 * expressions precisely without relying on the model's own
 * arithmetic (which can be unreliable for large numbers).
 *
 * Security: input is strictly validated against an allowlist
 * of characters before any evaluation is attempted.
 * ─────────────────────────────────────────────────────────────
 */

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

class CalculatorTool implements Tool
{
    /**
     * Human-readable description the AI uses to decide when to call this tool.
     */
    public function description(): Stringable|string
    {
        return
            'Evaluates mathematical expressions and returns precise numeric results. ' .
            'Supports: arithmetic operators (+−×÷), parentheses, exponentiation (pow), ' .
            'square root (sqrt), absolute value (abs), rounding (round/ceil/floor), ' .
            'modulo (fmod), logarithms (log/log10), and the constant pi(). ' .
            'Always prefer this tool over mental arithmetic for accuracy.';
    }

    /**
     * Execute the calculation and return the result as a string.
     */
    public function handle(Request $request): Stringable|string
    {
        $expression = trim((string) $request['expression']);

        // ── Security: allowlist of safe characters / tokens ──
        // Only digits, basic operators, decimal points, spaces,
        // parentheses, and known PHP math function names.
        $safePattern = '/^[0-9+\-*\/\.\(\)\s\%,sqrtpowabsrounceiflomdg10pi]+$/i';

        if (!preg_match($safePattern, $expression)) {
            return 'Error: Expression contains disallowed characters. ' .
                'Only numeric expressions and basic math functions are allowed.';
        }

        // Block potential code-injection tokens even after allowlist
        $blocked = ['exec', 'system', 'passthru', 'shell', 'popen', 'proc'];
        foreach ($blocked as $token) {
            if (stripos($expression, $token) !== false) {
                return 'Error: Disallowed function detected.';
            }
        }

        try {
            // phpcs:ignore Squiz.PHP.Eval.Discouraged
            // eval() is intentional here after the strict validation above.
            $result = eval("return (float)({$expression});");

            if (!is_finite($result)) {
                return 'Error: Result is infinite or undefined (e.g. division by zero).';
            }

            if (is_nan($result)) {
                return 'Error: Result is not a number.';
            }

            // Return integer format when the result is a whole number
            return (floor($result) === $result && abs($result) < PHP_INT_MAX)
                ? (string)(int) $result
                : (string) $result;
        } catch (\Throwable $e) {
            return 'Error evaluating expression: ' . $e->getMessage();
        }
    }

    /**
     * JSON Schema that the AI uses to call this tool correctly.
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'expression' => $schema->string()
                ->description(
                    'A valid PHP math expression. Examples: "2 + 2", "sqrt(144)", ' .
                    '"pow(2, 10)", "round(3.14159, 2)", "fmod(17, 5)"'
                )
                ->required(),
        ];
    }
}
