<?php

namespace App\Services\Recruitment;

use App\Models\Opening;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

class ScreeningEvaluator
{
    /**
     * @param Opening $opening  (must have screening_policy + screening_rules)
     * @param array   $profile  normalized candidate profile (keys match rule fields)
     * @return array{pass:bool, fail_count:int, flag_count:int, fails:array, flags:array}
     */
    public function evaluate(Opening $opening, array $profile): array
    {
        $rules = (array) ($opening->screening_rules ?? []);
        $fails = [];
        $flags = [];

        foreach ($rules as $rule) {
            $field = $rule['field'] ?? null;
            $op    = $rule['op']    ?? null;
            $sev   = ($rule['severity'] ?? 'fail') === 'flag' ? 'flag' : 'fail';
            if (!$field || !$op) continue;

            $candidate = $profile[$field] ?? null;

            $match = $this->match($field, $op, $candidate, $rule);

            if (!$match) {
                $record = [
                    'id'      => $rule['id'] ?? null,
                    'field'   => $field,
                    'op'      => $op,
                    'expected'=> Arr::only($rule, ['value','min','max']),
                    'actual'  => $candidate,
                    'message' => $rule['message'] ?? null,
                ];
                if ($sev === 'fail') { $fails[] = $record; } else { $flags[] = $record; }
            }
        }

        return [
            'pass'       => count($fails) === 0,
            'fail_count' => count($fails),
            'flag_count' => count($flags),
            'fails'      => $fails,
            'flags'      => $flags,
        ];
    }

    protected function match(string $field, string $op, $candidate, array $rule): bool
    {
        // normalize types for comparison
        $isDate = in_array($field, ['start_date'], true);
        $isNum  = in_array($field, [
            'years_total','years_med_device','travel_percent_max','expected_base','expected_ote'
        ], true);

        if ($isDate) {
            try {
                $candidate = $candidate ? Carbon::parse($candidate)->startOfDay() : null;
            } catch (\Throwable) { $candidate = null; }
        } elseif ($isNum) {
            $candidate = is_numeric($candidate) ? (float)$candidate : null;
        }

        // Operator semantics
        switch ($op) {
            case '>=':  return $candidate !== null && (float)$candidate >= (float)($rule['value'] ?? $rule['min'] ?? 0);
            case '<=':  return $candidate !== null && (float)$candidate <= (float)($rule['value'] ?? $rule['max'] ?? 0);
            case 'eq':
                if ($isDate) {
                    $v = isset($rule['value']) ? Carbon::parse($rule['value'])->startOfDay() : null;
                    return $candidate && $v && $candidate->equalTo($v);
                }
                return (string)$candidate !== '' && (string)$candidate === (string)($rule['value'] ?? '');
            case 'between':
                if ($isDate) {
                    $min = isset($rule['min']) ? Carbon::parse($rule['min'])->startOfDay() : null;
                    $max = isset($rule['max']) ? Carbon::parse($rule['max'])->startOfDay() : null;
                    return $candidate && $min && $max && $candidate->betweenIncluded($min, $max);
                }
                $min = is_numeric($rule['min'] ?? null) ? (float)$rule['min'] : null;
                $max = is_numeric($rule['max'] ?? null) ? (float)$rule['max'] : null;
                return $candidate !== null && $min !== null && $max !== null
                    && $candidate >= $min && $candidate <= $max;

            case 'in':
                // candidate is scalar; expected is set/array (for state, work_auth, etc.)
                $set = (array)($rule['value'] ?? []);
                return $candidate !== null && in_array($candidate, $set, true);

            case 'contains_any':
                // candidate is array; intersects expected set?
                $cand = array_values((array) $candidate);
                $set  = array_values((array) ($rule['value'] ?? []));
                return count(array_intersect($cand, $set)) > 0;

            case 'contains_all':
                $cand = array_values((array) $candidate);
                $set  = array_values((array) ($rule['value'] ?? []));
                return !array_diff($set, $cand);

            default:
                return true; // unknown op? don't block
        }
    }
}
