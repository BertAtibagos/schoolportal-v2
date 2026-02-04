<section id="section-surv-main" class="d-flex flex-column">
    <style>
        #txtarea-comments, .txtarea-comments {
            width: 100%;
            height: 10rem;
            padding: .5rem;
        }

        label:hover, input[type="radio"]:checked + label, input[type="checkbox"]:checked + label {
            cursor: pointer;
            font-weight: 500;
            text-decoration: underline;
        }

        .action-col {
            width: 7.5rem;
        }
    </style>
	<!-- <div class="mx-auto p-2" style="width: 200px;"> -->
	
	<?php include '../partials/loader.php' ?>
	
    <div class="alert alert-info alert-dismissible fade show" role="alert" id="survey-alert">
		<h5 class="alert-heading">Student Survey Reminder 📝</h5>
		<p class="mb-2">
			Your participation in this <strong>official school survey</strong> is important and helps the institution
			improve academic services and student support.
		</p>
		<p class="mb-2">
			Please ensure that your responses are <strong>honest and accurate</strong>. All submitted data will be
			treated with <strong>confidentiality</strong>.
		</p>
		<p class="mb-2">
			<strong class="text-danger">Important Note: </strong> It is highly encouraged to complete all the surveys before accessing your grades.
		</p>
		<button type="button" class="btn btn-sm btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
	</div>
	<div class="col-md-7">
		<div id="div-list">
            <div id="list-survey" class="mb-4">
                <h5 class="mb-2">Survey</h5>
                <table class="table table-bordered table-hover">
                    <thead class="table-primary">
                        <th>Information</th>
                        <th class="text-center action-col">Action</th>
                    </thead>
                    <tbody id="list-survey-tbody"></tbody>
                </table>
            </div>
            <div id="list-evaluation">
                <h5 class="mb-2">Evaluation</h5>
                <table class="table table-bordered table-hover">
                    <thead class="table-primary">
                        <th>Information</th>
                        <th class="text-center action-col">Action</th>
                    </thead>
                    <tbody id="list-evaluation-tbody"></tbody>
                </table>
            </div>
		</div>

		<div id = "errormessage"></div>
		<div id="div-survey-header">
            <div id="main-survey">
                <button type="button" class="btn btn-sm btn-primary mb-4" id="btn-close-list"><i class="fa-solid fa-chevron-left"></i> Back</button>
                <!-- <h5 class="mb-2">Survey List</h5> -->
                
                <table class="table table-bordered table-hover">
                    <thead class="table-primary">
                        <th>Department Name</th>
                        <th class="text-center action-col">Action</th>
                    </thead>
                    <tbody id="main-survey-tbody"></tbody>
                </table>
            </div>
            <div id="main-evaluation">
                <button type="button" class="btn btn-sm btn-primary mb-4" id="btn-close-list"><i class="fa-solid fa-chevron-left"></i> Back</button>
                <!-- <h5 class="mb-2">Survey List</h5> -->
                
                <table class="table table-bordered table-hover">
                    <thead class="table-primary">
                        <th>Subject Code</th>
                        <th>Description</th>
                        <th>Instructor</th>
                        <th class="text-center action-col">Action</th>
                    </thead>
                    <tbody id="main-evaluation-tbody"></tbody>
                </table>
            </div>
		</div>
	</div>
    
	<div id="div-survey-content" class="col-md-6">
		<button type="button" class="btn btn-sm btn-primary mb-4" id="btn-close-survey-content"><i class="fa-solid fa-chevron-left"></i> Back</button> 
		<h3 id="hd-survey-content" class="text-center"></h3>
		<p id="hd-survey-description"></p>
		<div id="main-survey-content"></div>
	</div>

	<div id="div-evaluation-content" class="col-md-6">
		<button type="button" class="btn btn-sm btn-primary mb-4" id="btn-close-evaluation-content"><i class="fa-solid fa-chevron-left"></i> Back</button> 
		<h3 id="hd-evaluation-content" class="text-center"></h3>
		<p id="hd-evaluation-description"></p>
		<div id="main-evaluation-content"></div>
	</div>
</section>

<script type="module" src="../../js/custom/survey-script.js?d=<?= time() ?>"></script>
