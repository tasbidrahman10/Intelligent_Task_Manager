<?php
/**
 * Rule-based scheduler for ITM
 * - Uses pending tasks (status='pending') with optional deadline & priority.
 * - Produces a 7-day plan starting today.
 * - Heuristics:
 *   * Earlier deadlines ranked higher.
 *   * High > Medium > Low priority.
 *   * Tasks with no deadline get lower urgency.
 *   * Max N tasks per day (configurable).
 */

if (!function_exists('itm_build_schedule')) {
  function itm_build_schedule(array $tasks, int $days = 7, int $maxPerDay = 4): array {
    date_default_timezone_set(@date_default_timezone_get());
    $today = new DateTimeImmutable('today');

    $priorityWeight = ['High'=>3, 'Medium'=>2, 'Low'=>1];
    $scored = [];
    foreach ($tasks as $t) {
      $priority = $t['priority'] ?? 'Medium';
      $p = $priorityWeight[$priority] ?? 2;

      $deadlineDays = 999; 
      if (!empty($t['deadline'])) {
        $dl = DateTime::createFromFormat('Y-m-d', $t['deadline']);
        if ($dl) {
          $deadlineDays = (int)$today->diff($dl)->format('%r%a');
        }
      }

      $urgency = max(0, 14 - ($deadlineDays === 999 ? 14 : $deadlineDays));
      $overdueBonus = ($deadlineDays < 0) ? 10 : 0;

      $score = $p*10 + $urgency + $overdueBonus;
      $t['_score'] = $score;
      $t['_deadlineDays'] = $deadlineDays;
      $scored[] = $t;
    }

    usort($scored, function($a,$b){
      if ($a['_score'] === $b['_score']) {
        $ad = $a['_deadlineDays']; $bd = $b['_deadlineDays'];
        if ($ad == $bd) {
          $pri = ['High'=>0,'Medium'=>1,'Low'=>2];
          return ($pri[$a['priority']] ?? 1) <=> ($pri[$b['priority']] ?? 1);
        }
        return $ad <=> $bd;
      }
      return $b['_score'] <=> $a['_score'];
    });

    $plan = [];
    for ($i=0; $i<$days; $i++) {
      $d = $today->modify("+$i day")->format('Y-m-d');
      $plan[$d] = [];
    }

    foreach ($scored as $task) {
      $lastIndex = $days - 1;
      if (!empty($task['deadline'])) {
        $dl = DateTime::createFromFormat('Y-m-d', $task['deadline']);
        if ($dl) {
          $diff = (int)$today->diff($dl)->format('%r%a');
          if ($diff < 0) {
            $lastIndex = 0;
          } else {
            $lastIndex = min($lastIndex, $diff);
          }
        }
      }
      $placed = false;
      for ($i=0; $i<=$lastIndex; $i++) {
        $d = $today->modify("+$i day")->format('Y-m-d');
        if (count($plan[$d]) < $maxPerDay) {
          $plan[$d][] = $task;
          $placed = true;
          break;
        }
      }
      if (!$placed) {
        for ($i=0; $i<$days; $i++) {
          $d = $today->modify("+$i day")->format('Y-m-d');
          if (count($plan[$d]) < $maxPerDay) {
            $plan[$d][] = $task;
            $placed = true;
            break;
          }
        }
      }
    }

    return $plan;
  }
}
