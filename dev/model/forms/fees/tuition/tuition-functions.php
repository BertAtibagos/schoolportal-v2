<?php
function array_to_table(array $data): string {
    $footers = ["TOTAL TUITION FEE", "TOTAL REMAINING BALANCE"];
    $html = "";

    foreach ($data as $key => $value) {
        if(in_array($key, $footers)){
            $html .= "<tfoot>";
            $html .= "<tr>";
            $html .= "<td class='totals text-end text-danger'><u><strong>" . htmlspecialchars($key) . ":</strong></u></td>";
            $html .= "<td class='totals text-end text-danger'><u><strong>₱" . number_format((float)$value, 2) . "</strong></u></td>";
            $html .= "</tr>";
            $html .= "</tfoot>";
            continue;
        }

        $html .= "<tr>";
        $html .= "<td>" . htmlspecialchars($key) . "</td>";
        $html .= "<td class='text-end'>₱ " . number_format((float)$value, 2) . "</td>";
        $html .= "</tr>";
    }

    return $html;
}

function to_transaction_table(array $array): string {
    $html = "";
    $counter = 1;
    foreach ($array as $row) {
        // Case 1: transaction row
        if (isset($row['TRANSACTION_DATE'])) {
            $html .= "<tr>";
            $html .= "<td class='text-center'>" . htmlspecialchars($counter++) . "</td>";
            $html .= "<td>" . htmlspecialchars($row['TRANSACTION_DATE']) . "</td>";
            $html .= "<td>" . htmlspecialchars($row['PARTICULARS']) . "</td>";
            $html .= "<td class='text-center'>" . htmlspecialchars($row['PAYMENT_MODE']) . "</td>";
            $html .= "<td class='text-center'>" . htmlspecialchars($row['OR_NUMBER']) . "</td>";
            $html .= "<td class='text-end'>₱ " . number_format((float)$row['AMOUNT_TENDERED'], 2) . "</td>";
            $html .= "</tr>";
        }
        // Case 2: total row
        elseif (isset($row['TOTAL AMOUNT PAID'])) {
            $html .= "<tfoot>";
            $html .= "<tr>";
            $html .= "<td colspan='5' class='totals text-end text-danger'><u><strong>TOTAL AMOUNT PAID:</strong></u></td>";
            $html .= "<td class='totals text-end text-danger'><u><strong>₱" . number_format((float)$row['TOTAL AMOUNT PAID'], 2) . "</strong></u></td>";
            $html .= "</tr>";
            $html .= "</tfoot>";
        }
    }

    return $html;
}


function college_tutorial(string $type, array $subject, int $scheme_amount_per_unit = 0){

}

function college_regular(string $type, array $subject, int $scheme_amount_per_unit = 0): array {
    $unit     = $type . '_UNIT';
    $fee      = $type . '_FEE';
    $subFee   = $type . '_SUB_FEE';
    $useAlias = $type . '_USE_ALIAS';
    $alias    = $type . '_ALIAS';

    // ----- 1. Tutorial vs regular -----
    if ((int)$subject['ASTUTORIAL'] === 1) {
        // Use tutorial function for this component
        // $amount = college_tutorial($type, $subject);
        // echo json_encode($subject);
    } else {
        // Regular calculation
        if ($type === 'LEC') {
            $lecture_units = $subject['LEC_UNIT']
                + ($subject['LAB_INCLUDE'] === 1 ? $subject['LAB_UNIT'] : 0)
                + ($subject['SL_INCLUDE'] === 1 ? $subject['SL_UNIT'] : 0)
                + ($subject['C_INCLUDE'] === 1 ? $subject['C_UNIT'] : 0)
                + ($subject['RLE_INCLUDE'] === 1 ? $subject['RLE_UNIT'] : 0)
                + ($subject['AFF_INCLUDE'] === 1 ? $subject['AFF_UNIT'] : 0)
                + ($subject['OTHER_INCLUDE'] === 1 ? $subject['OTHER_UNIT'] : 0);

            $amount_per_unit = ($subject['USE_SUBJ_UNIT_AMT'] == 1 || $subject['IS_NSTP'] == 1 || $scheme_amount_per_unit == 0 || $subject['ASTUTORIAL'] == 1)
                ? $subject['LEC_FEE']
                : $scheme_amount_per_unit;

            $amount = (float)$lecture_units * (float)$amount_per_unit;
        } else {
            $amount = $subject[$unit] * $subject[$fee];
        }
    }

    // ----- 2. Add SUB_FEE -----
    $amount += floatval($subject[$subFee] ?? 0);

    // ----- 3. Determine Key -----
    if ($subject[$useAlias] == 1 && !empty($subject[$alias])) {
        $key = $subject[$alias];
    } elseif ($type === 'LEC' && (int)$subject['IS_NSTP'] === 1) {
        $key = 'NSTP';
    } elseif ((int)$subject['ASTUTORIAL'] === 1 && $type === 'LEC') {
        $key = 'TUTORIAL';
    } elseif ($type === 'LEC') {
        $key = 'TUITION FEE';
    } else {
        $key = $type . ' FEE';
    }

    return [$key, $amount];
}


function college_tuition(array $array) {
    $tuition_fee_amt = [];
    $scheme_amount_per_unit = 0;
    $scheme_discount = 1;

    // get scheme info
    foreach ($array['payment scheme'] as $scheme) {
        if ($scheme['IS_TUITION_FEE'] == 1) {
            $scheme_amount_per_unit = $scheme['SCHEME_AMNT'];
            if (floatval($scheme['DISCOUNT']) != 0) {
                $scheme_discount = 1 - floatval($scheme['DISCOUNT']);
            }
        } else {
            // $tuition_fee_amt[$scheme['SCHEME_NAME']] = (float) $scheme['SCHEME_AMNT']; // if you want components to be displayed separately
            $tuition_fee_amt['REG AND MISC'] = ($tuition_fee_amt['REG AND MISC'] ?? 0) + (float) $scheme['SCHEME_AMNT'];
        }
    }

    $feeTypes = ['LEC', 'LAB', 'SL', 'C', 'RLE', 'AFF', 'OTHER'];

    foreach ($array['subject offered'] as $subject) {
        foreach ($feeTypes as $type) {
            [$key, $amount] = college_regular($type, $subject, $scheme_amount_per_unit);
            if ($amount > 0) {
                $tuition_fee_amt[$key] = ($tuition_fee_amt[$key] ?? 0) + $amount;
            }
        }
    }
    // echo json_encode($tuition_fee_amt);

    // apply scheme discount on TUITION FEE only
    if (isset($tuition_fee_amt['TUITION FEE'])) {
        $tuition_fee_amt['TUITION FEE'] *= $scheme_discount;
    }


    // desired primary order
    $order = ['REG AND MISC', 'TUITION FEE', 'LAB FEE'];

    // build sorted array
    $sorted = [];
    foreach ($order as $key) {
        if (isset($tuition_fee_amt[$key])) {
            $sorted[$key] = $tuition_fee_amt[$key];
        }
    }

    // append the rest (like RLE FEE, AFF FEE, OTHER FEE, etc.)
    foreach ($tuition_fee_amt as $key => $val) {
        if (!isset($sorted[$key])) {
            $sorted[$key] = $val;
        }
    }
    $tuition_fee_amt = $sorted;
    // append the deductions
    foreach($array['deduction'] as $deduction){
        if(!empty($deduction['NAME']) || !empty($deduction['AMOUNT'])){
            $tuition_fee_amt[$deduction['NAME']] = ($tuition_fee_amt[$deduction['NAME']] ?? 0) + ((float)$deduction['AMOUNT'] * -1);
        }
    }

    // append the additionals
    foreach($array['additional'] as $additional){
        if(!empty($additional['NAME']) || !empty($additional['AMOUNT'])){
            $tuition_fee_amt[$additional['NAME']] = ($tuition_fee_amt[$additional['NAME']] ?? 0) + ((float)$additional['AMOUNT']);
        }
    }


    $total = array_sum($tuition_fee_amt);
    $tuition_fee_amt['TOTAL TUITION FEE'] = $total;

    return $tuition_fee_amt;
}

function college_transaction_history(array $array) {
    $transaction_history = $array['transaction history'];
    $total = 0;

    foreach ($transaction_history as $transaction) {
        $total += (float) $transaction['AMOUNT_TENDERED'];
    }

    $transaction_history[] = ['TOTAL AMOUNT PAID' => $total];

    return $transaction_history;
}

function college_payment_plan(array $array, array $transaction_history) {
    $tuition_fee = college_tuition($array);
    $down_payment = (float) $array['payment scheme'][0]['DOWN_PAYMENT'];
    $payment_detail = explode(',', $array['payment scheme'][0]['PLAN_DETAIL']);
    $installment_count = max(count($payment_detail) - 1, 1); // avoid divide by zero
    $per_installment = ($tuition_fee['TOTAL TUITION FEE'] - $down_payment) / $installment_count;

    // Total already paid by student
    $amount_paid = (float) $transaction_history[count($transaction_history)-1]["TOTAL AMOUNT PAID"];

    $payment_plan = [];
    $remaining_balance = $amount_paid;

    foreach ($payment_detail as $installment) {
        if ($installment === 'UPON ENROLLMENT') {
            if ($remaining_balance >= $down_payment) {
                $payment_plan[$installment] = 0;
                $remaining_balance -= $down_payment;
            } else {
                $payment_plan[$installment] = $down_payment - $remaining_balance;
                $remaining_balance = 0;
            }
        } else {
            if ($remaining_balance >= $per_installment) {
                $payment_plan[$installment] = 0;
                $remaining_balance -= $per_installment;
            } else {
                $payment_plan[$installment] = $per_installment - $remaining_balance;
                $remaining_balance = 0;
            }
        }
    }

    $total_remaining_balance =  $tuition_fee['TOTAL TUITION FEE'] - $amount_paid;

    // Add total remaining balance (still unpaid)
    // $payment_plan['TOTAL REMAINING BALANCE'] = array_sum($payment_plan);
    $payment_plan['TOTAL REMAINING BALANCE'] = $total_remaining_balance;

    return $payment_plan;
}


