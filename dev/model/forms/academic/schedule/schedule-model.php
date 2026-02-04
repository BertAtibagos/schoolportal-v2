<style>
    #table-container {
        overflow-x: auto;
    }

    .subject p {
        font-size: 10px;
    }
</style>

<?php include '../partials/loader.php' ?>
<section id="section-schedule">
    <div class="alert alert-info alert-dismissible fade show" role="alert" id="login-alert">
        <h5 class="alert-heading">Important Note! 🗓️</h5>
        <p class="mb-2">
            For official verification or schedule adjustments, please visit the
            <strong>FCPC Registrar's Office</strong>.
        </p>
        <p class="mb-2">
            Only subjects with <strong>assigned schedules</strong> and <strong>verified instructors</strong>
            will appear in this calendar.
        </p>
        <p class="mb-0">
            If a subject <strong>does not appear</strong> in your schedule, it means it currently has
            <strong>no assigned time</strong>. Please <strong>contact the Registrar's Office</strong> for clarification.
        </p>
        <button type="button" class="btn btn-sm btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php
        include '../partials/dropdown.php';

        // Example usage
        $required_dropdowns = ['type', 'level', 'year', 'period', 'course'];
        echo dropdownModule($required_dropdowns, 'small');
    ?>
    <div id="table-container"></div>
</section>

<script type="module" src="<?= assetLoader('../../js/custom/schedule-script.js') ?>"></script>