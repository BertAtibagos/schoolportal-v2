function safeArray(val) {
    return Array.isArray(val) ? val : [];
}

function safeObject(val) {
    return val && typeof val === "object" ? val : {};
}

function num(val) {
    const n = Number(val);
    return isNaN(n) ? 0 : n;
}

function escapeHtml(str) {
    return String(str ?? "")
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

export function arrayToTable(data) {
    data = safeObject(data);
    const footers = ["TOTAL TUITION FEE", "TOTAL REMAINING BALANCE"];
    let html = "";

    const keys = Object.keys(data).filter(k => !footers.includes(k));

    // No main rows
    if (keys.length === 0 ||keys.length === 1) {
        html += `<tr><td colspan='99' class='text-center text-danger fw-medium'>No record found.</td></tr>`;
    } else {
        keys.forEach(key => {
            html += `
                <tr>
                    <td>${escapeHtml(key)}</td>
                    <td class="text-end">₱ ${num(data[key]).toLocaleString(undefined, { minimumFractionDigits: 2 })}</td>
                </tr>`;
        });
    }

    // Footers
    footers.forEach(key => {
        if (data[key] !== undefined) {
            html += `
                <tfoot>
                    <tr>
                        <td class="totals text-end text-primary-emphasis">
                            <u><strong>${escapeHtml(key)}:</strong></u>
                        </td>
                        <td class="totals text-end text-primary-emphasis">
                            <u><strong>₱${num(data[key]).toLocaleString(undefined, { minimumFractionDigits: 2 })}</strong></u>
                        </td>
                    </tr>
                </tfoot>`;
        }
    });

    return html;
}

export function toTransactionTable(array) {
    array = safeArray(array);
    let html = "";
    let counter = 1;

    if (array.length === 0 || array.length === 1) {
        html += `<tr><td colspan='99' class='text-center text-danger fw-medium'>No record found.</td></tr>`;
        return html;
    }

    array.forEach(row => {
        row = safeObject(row);

        if (row.TRANSACTION_DATE) {
            html += `
                <tr>
                    <td class="text-center">${counter++}</td>
                    <td>${escapeHtml(row.TRANSACTION_DATE)}</td>
                    <td>${escapeHtml(row.PARTICULARS)}</td>
                    <td class="text-center">${escapeHtml(row.PAYMENT_MODE)}</td>
                    <td class="text-center">${escapeHtml(row.OR_NUMBER)}</td>
                    <td class="text-end">₱ ${num(row.AMOUNT_TENDERED).toLocaleString(undefined, { minimumFractionDigits: 2 })}</td>
                </tr>`;
        } else if (row["TOTAL AMOUNT PAID"] !== undefined) {
            html += `
                <tfoot>
                    <tr>
                        <td colspan="5" class="totals text-end text-primary-emphasis">
                            <u><strong>TOTAL AMOUNT PAID:</strong></u>
                        </td>
                        <td class="totals text-end text-primary-emphasis">
                            <u><strong>₱${num(row["TOTAL AMOUNT PAID"]).toLocaleString(undefined, { minimumFractionDigits: 2 })}</strong></u>
                        </td>
                    </tr>
                </tfoot>`;
        }
    });

    return html;
}

function collegeRegular(type, subject, schemeAmountPerUnit = 0) {
    subject = safeObject(subject);
    let amount = 0;

    if (num(subject.ASTUTORIAL) !== 1) {
        if (type === "LEC") {
            const lectureUnits =
                num(subject.LEC_UNIT) +
                (subject.LAB_INCLUDE ? num(subject.LAB_UNIT) : 0) +
                (subject.SL_INCLUDE ? num(subject.SL_UNIT) : 0) +
                (subject.C_INCLUDE ? num(subject.C_UNIT) : 0) +
                (subject.RLE_INCLUDE ? num(subject.RLE_UNIT) : 0) +
                (subject.AFF_INCLUDE ? num(subject.AFF_UNIT) : 0) +
                (subject.OTHER_INCLUDE ? num(subject.OTHER_UNIT) : 0);

            const amountPerUnit =
                subject.USE_SUBJ_UNIT_AMT == 1 ||
                subject.IS_NSTP == 1 ||
                schemeAmountPerUnit === 0
                    ? num(subject.LEC_FEE)
                    : num(schemeAmountPerUnit);

            amount = lectureUnits * amountPerUnit;
        } else {
            amount = num(subject[`${type}_UNIT`]) * num(subject[`${type}_FEE`]);
        }
    }

    amount += num(subject[`${type}_SUB_FEE`]);

    let key = `${type} FEE`;
    if (subject[`${type}_USE_ALIAS`] == 1 && subject[`${type}_ALIAS`])
        key = subject[`${type}_ALIAS`];
    else if (type === "LEC" && subject.IS_NSTP == 1)
        key = "NSTP";
    else if (type === "LEC" && subject.ASTUTORIAL == 1)
        key = "TUTORIAL";
    else if (type === "LEC")
        key = "TUITION FEE";

    return [key, amount];
}

export function collegeTuition(data) {
    data = safeObject(data);
    let tuition = {};
    let schemeAmount = 0;
    let discount = 1;

    safeArray(data["payment scheme"]).forEach(s => {
        if (s.IS_TUITION_FEE == 1) {
            schemeAmount = num(s.SCHEME_AMNT);
            if (num(s.DISCOUNT)) discount = 1 - num(s.DISCOUNT);
        } else {
            tuition["REG AND MISC"] = (tuition["REG AND MISC"] ?? 0) + num(s.SCHEME_AMNT);
        }
    });

    ["LEC","LAB","SL","C","RLE","AFF","OTHER"].forEach(type => {
        safeArray(data["subject offered"]).forEach(sub => {
            const [k, amt] = collegeRegular(type, sub, schemeAmount);
            if (amt > 0) tuition[k] = (tuition[k] ?? 0) + amt;
        });
    });

    if (tuition["TUITION FEE"] !== undefined)
        tuition["TUITION FEE"] *= discount;

    safeArray(data.deduction).forEach(d =>
        tuition[d.NAME] = (tuition[d.NAME] ?? 0) - num(d.AMOUNT)
    );

    safeArray(data.additional).forEach(a =>
        tuition[a.NAME] = (tuition[a.NAME] ?? 0) + num(a.AMOUNT)
    );

    tuition["TOTAL TUITION FEE"] =
        Object.values(tuition).reduce((a, b) => a + b, 0);

    return tuition;
}

export function collegeTransactionHistory(data) {
    const history = safeArray(data?.["transaction history"]);
    const total = history.reduce((s, t) => s + num(t?.AMOUNT_TENDERED), 0);
    return [...history, { "TOTAL AMOUNT PAID": total }];
}

export function collegePaymentPlan(data, transactionHistory) {
    data = safeObject(data);
    transactionHistory = safeArray(transactionHistory);

    const tuition = collegeTuition(data);
    const scheme = safeArray(data["payment scheme"])[0] || {};
    const down = num(scheme.DOWN_PAYMENT);
    const planDetail = (scheme.PLAN_DETAIL || "").split(",");
    const count = Math.max(planDetail.length - 1, 1);
    const perInstallment = (num(tuition["TOTAL TUITION FEE"]) - down) / count;

    const paid = num(transactionHistory.at(-1)?.["TOTAL AMOUNT PAID"]);
    let remaining = paid;
    let plan = {};

    planDetail.forEach(p => {
        const amt = p === "UPON ENROLLMENT" ? down : perInstallment;
        plan[p] = Math.max(amt - remaining, 0);
        remaining = Math.max(remaining - amt, 0);
    });

    plan["TOTAL REMAINING BALANCE"] = num(tuition["TOTAL TUITION FEE"]) - paid;
    return plan;
}
