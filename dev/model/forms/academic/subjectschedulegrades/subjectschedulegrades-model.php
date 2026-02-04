<?php

if(!isset($_SESSION['STUDENT']['ID'])){
    die("Unauthorized access!");
}
?>

<style>
    .watermark-wrapper {
        position: relative;
        overflow-y: hidden;
    }

    /* Watermark text centered */
    .watermark-wrapper::before {
        /* content: '"This is not an official document." "This is not an official document." \A ~~ "This is not an official document." ~~'; */
        content: '~~ "This is not an official document." ~~';
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        font-size: 2.5rem;
        color: rgba(255, 0, 0, 0.15);
        white-space: pre-wrap;
        pointer-events: none;
        z-index: 0;
        font-style: italic;
        width: 100%;
        text-align: center;
    }

    table.watermarked-table {
        background: transparent;

    }

    @media screen and (orientation: portrait) {
        .watermark-wrapper::before {
            font-size: 1.5rem;
        }
    }
</style>
<section id='sec-subject-schedule-grades' class="section-container">
	<?php include '../partials/loader.php' ?>
    <div class="alert alert-info alert-dismissible fade show" role="alert">
        <h5 class="alert-heading">Important Note! 📣</h5>
        <p class="mb-2">
            This page serves only as a <strong>reference for viewing grades</strong> and is
            <strong class="text-danger">not an official document</strong>.
            To request a <strong>Certified Copy of Grades</strong>, please visit the
            <strong>FCPC Registrar's Office</strong>.
        </p>
        <p class="mb-2">
            Grades will be visible on the Student Portal
            <strong>only after they have been submitted by the Program Head/Dean and verified by the Registrar's Office</strong>.
        </p>
        <ol class="mb-0">
            <li>
                For courses marked <strong>“No Designated Instructor”</strong>, kindly
                <strong>contact the Registrar's Office</strong> for assistance.
            </li>
            <li>
                For courses showing <strong>“No Encoded Grade Yet”</strong>, please
                <strong>coordinate directly with your Instructor</strong>.
            </li>
        </ol>
        
        <button type="button" class="btn btn-sm btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>

    <?php
    include '../partials/dropdown.php';

    // Example usage
    $required_dropdowns = ['level', 'year', 'period', 'course'];
    echo dropdownModule($required_dropdowns, 'small');
    ?>

    <div id='div-grades'>
        <div id='div-grades-list'>
            <div id="errormessage" style="display: none; border: solid 3px red; background-color: #84202933; border-radius: .5rem; padding: .5rem 1rem; margin: .5rem 0;">
                <!-- Error message -->
            </div>

            <div class="watermark-wrapper">
            <!-- <div class="watermark-wrapper border rounded shadow-sm p-3"> -->
                <table id='table-grades' class='table table-hover table-responsive table-bordered watermarked-table'>
                    <thead class='table-primary' id='thead-grades'>
                        <tr>
                            <th>CODE</th>
                            <th>DESCRIPTION</th>
                            <th>UNITS</th>
                            <th>INSTRUCTOR</th>
                            <th>FINAL GRADE</th>
                            <th>EQUIVALENT</th>
                            <th>REMARKS</th>
                            <th>ACTION</th>
                        </tr>
                    </thead>
                    <tbody id='tbody-grades'>
                        <tr>
                            <td colspan='9' style='font-size: 18px;
                                                        font-family: Roboto, sans-serif;
                                                        font-weight: normal;
                                                        text-decoration: none;
                                                        color: red;'>
                                LOADING...
                            </td>
                        </tr>
                    </tbody>
                    <tfoot id="tfoot-grades"></tfoot>
                </table>
            </div>
        </div>
    </div>
</section>
<script type="module" src="<?= assetLoader('../../js/custom/subjectschedulegrades-script.js') ?>"></script>