<style>
    #section-tuition p,
    #section-tuition .totals {
        color: #071976;
        text-transform: uppercase;
    }

    .btn-close {
        color: var(--bs-info-text-emphasis);
    }
</style>

<?php include '../partials/loader.php' ?>
<div class="alert alert-info alert-dismissible fade show" role="alert">
    <h5 class="alert-heading">Important Reminder! 💡</h5>
    <p class="mb-2">
        This page serves as a <strong>reference for viewing tuition fees and payments</strong>. It is
        <strong class="text-danger">not an official receipt or billing statement</strong>.
        For official copies or payment verification, please visit the
        <strong>FCPC Accounting Office</strong>.
    </p>
    <p class="mb-2">
        
        Tuition fee records shown here are <strong>based on the latest encoded and verified transactions</strong>.
    </p>
    <p class="mb-0">
        For discrepancies in the total balance or recent payments,
        <strong>coordinate directly with the Cashier or Accounting staff</strong>.
    </p>
    <button type="button" class="btn btn-sm btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<section id="section-tuition">

    <?php
    include '../partials/dropdown.php';

    // Example usage
    $required_dropdowns = ['level', 'year', 'period', 'course'];
    echo dropdownModule($required_dropdowns, 'small');
    ?>

    <div class='row'>
        <div class='col-lg-4'>
            <div class="mb-4 d-none">
                <div class="text-center">
                    <p><u><strong>Summary of Fees</strong></u></p>
                </div>
                <table class="shadow-sm table table-bordered">
                    <thead class="table-primary">
                        <tr>
                            <th scope='col' class='col-8 text-center'>Category</th>
                            <th scope='col' class="text-center">Amount</th>
                        </tr>
                    </thead>
                    <tbody id="tbody_summary">
                        <tr>
                            <td colspan="99" class="d-none text-danger text-center">No records found yet</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="mb-4">
                <div class="text-center">
                    <p><u><strong>Payment Plan</strong></u></p>
                </div>
                <table class="shadow-sm table table-bordered">
                    <thead class="table-primary">
                        <tr>
                            <!-- <th scope='col' class='col-8 text-center'>Category (<span id="payment_plan_name">NON-FRESHMEN PLAN A</span>)</th> -->
                            <th scope='col' class='col-8 text-center'>Category</th>
                            <th scope='col' class="text-center">Amount</th>
                        </tr>
                    </thead>
                    <tbody id="tbody_plan">
                        <tr>
                            <td colspan="99" class="d-none text-danger text-center">No records found yet</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class='col' style="overflow-x: auto;">
            <div class="text-center">
                <p><u><strong>Transaction History</strong></u></p>
            </div>
            <div class="table-container">
                <table class="shadow-sm table table-bordered">
                    <thead class="table-primary">
                        <tr>
                            <th class="text-center" scope='col' style="width: 2rem;"></th>
                            <th class="text-center" scope='col'>Date</th>
                            <th class="text-center" scope='col'>Particulars</th>
                            <th class="text-center" scope='col'>Payment Mode</th>
                            <th class="text-center" scope='col'>OR Number</th>
                            <th class="text-center" scope='col'>Amount Tendered</th>
                        </tr>
                    </thead>
                    <div>
                        <tbody id="tbody_history">
                            <tr>
                                <td colspan="99" class="d-none text-danger text-center">No records found yet</td>
                            </tr>
                        </tbody>
                    </div>

                </table>
            </div>

        </div>
    </div>
</section>



<script type="module" src="<?= assetLoader('../../js/custom/tuition-script.js') ?>"></script>