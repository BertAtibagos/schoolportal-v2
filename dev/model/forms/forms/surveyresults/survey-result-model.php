<?php include '../partials/loader.php';

// echo "<pre>";
// echo var_dump($_SESSION);
// echo "</pre>";
?>

<section id="section-schedule">
    <?php
    include '../partials/dropdown.php';

    // Example usage
    $required_dropdowns = ['level', 'year', 'period'];
    echo dropdownModule($required_dropdowns, 'small');
    ?>
    <div id="results-container">
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h5 class="card-title">Survey Results Summary</h5>
                <p class="card-text">
                    <strong>Instructor:</strong> Prof. John Doe<br>
                    <strong>Respondents:</strong> 35 students<br>
                </p>
                <ul>
                    <li>Teaching Effectiveness: 3.5 / 4</li>
                    <li>Class Engagement: 3.3 / 4</li>
                    <li>Preparedness: 3.7 / 4</li>
                    <li>Overall Satisfaction: 3.6 / 4</li>
                </ul>
                <button class="btn btn-success">Download PDF</button>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="card-title">PDF Preview</h5>
                <embed src="survey/survey-result/sample-survey.pdf" type="application/pdf" width="100%" height="600px" />
            </div>
        </div>
    </div>

</section>

<script type="module" src="<?= assetLoader('../../js/custom/survey-result-script.js') ?>"></script>