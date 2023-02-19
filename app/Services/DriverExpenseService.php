<?php
 
declare(strict_types=1);

namespace App\Services;
  
class DriverExpenseService
{
    public function calculateDriverExpenses(array $drivers, array $expenses): array
    {
        $result = [];

        $overpayIndex = 0;
        $totalExpensesCents = 0;

        foreach ($expenses as $expenseType => $expenseAmount) {
            $expenseAmountCents = (int) round((float)$expenseAmount * 100, 0);
            $totalExpensesCents += $expenseAmountCents;
    
            $remainder = $expenseAmountCents % 2;
            $splitAmountCents = ($expenseAmountCents - $remainder) / 2;
           
            $splitAmount = number_format($splitAmountCents / 100, 2, '.', '');

            $temp = [
                'expense_type' => $expenseType,
                'amount' => $expenseAmount,
                $drivers[0] => $splitAmount,
                $drivers[1] => $splitAmount
            ];
           
            if ($remainder) {
                $temp[$drivers[$overpayIndex]] = number_format(($splitAmountCents + $remainder) / 100, 2, '.', '');
                $overpayIndex = (int)(!$overpayIndex);
            }

            $result['expenses'][] = $temp;
        }
    
        $totalRemainder = $totalExpensesCents % 2;
        $splitTotalCents = ($totalExpensesCents - $totalRemainder) / 2; 
        $splitOverpaidCents = $totalRemainder ? $splitTotalCents + 1 : $splitTotalCents;

        $result['total'] = [
            'amount' => number_format($totalExpensesCents / 100, 2, '.', ''),
            $drivers[0] => number_format($splitOverpaidCents / 100, 2, '.', ''),
            $drivers[1] => number_format($splitTotalCents / 100, 2, '.', ''),
        ];

        $result['drivers'] = $drivers;

        return $result;
    }
}