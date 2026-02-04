

<link rel="stylesheet" href="forms/tadi/dean/css_tadi.css">
<style>
    .select-shadow {
        box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.3);
    }

    .button-bg-change {
        background-color: #EEEEF6;
        transition: background-color 0.1s ease;
        border: 1px solid transparent;
        border-color: #181a46;
    }

    .button-bg-change:hover {
        background-color: #181a46;
        color: white;
    }

    .button-search-bg-change {
        background-color: #181a46;
        transition: background-color 0.1s ease;
        border: 1px solid transparent;
        color: white;
    }

    .button-search-bg-change:hover {
        background-color: black;
        color: white;
    }

    .viewAttch {
        background-color: #181a46;
        color: white;
    }

    .noAttch {
        background-color: #6a6a6aff;
        color: white;
    }

    .modalHead {
        display: flex;
        flex-direction: row-reverse;
        padding: 5px;
    }

    .table_tadi_responsive {
        max-height: 60vh;
        overflow-y: auto;
    }

    /* .inst_list_tbl_wrapper,
    .by_subj_table_wrapper {
        max-height: 70vh;
        overflow-y: auto;
    } */

    .tadi_tbl,
    .by_subj_table thead {
        position: sticky;
        top: 0;
        z-index: 2;
        background-color: #181a46;
        /* same as your header color */
        color: white;
    }

    .activity-text {
        white-space: pre-wrap;
        word-wrap: break-word;
        max-width: 600px;
        display: inline-block;
        text-align: justify;
    }

    
    #prof_tadi_list_table tr, #prof_tadi_list_table td,
    #prof_section_list_table tr, #prof_section_list_table td{
        background-color: white;
    }
	.fixed-modal{
		width: 600px;
    	height: 500px;
    	max-width: 90vw;
    	max-height: 90vh;
	}
	.img-container{
		width: 100%;
		height: 400px;
		display: flex;
		justify-content: center;
		align-items: center;
		overflow: hidden;
	}
	.img-container img{
		max-width: 100%;
		max-height: 100%;
		object-fit: contain;
	}
	.inst_list_tbl_wrapper{
		max-height: 65vh;
    	overflow-y: auto;
	}
    .table-scroll-width-limit,
    .report-container{
        max-height: 58vh;
    	overflow-y: auto;
    }
    .legend{
        font-size: 14px;
        margin-top: 10px;
    }
    .subject-card-header{
        background-color: #181a46;
    }
    .report-btn{
        background-color: #EEEEF6;
        transition: background-color 0.1s ease;
        border: 1px solid transparent;
        border-color: #0d6efd;
    }
    .report-btn:hover{
        background-color: #0d6efd;
        color: white;
    }
    .gotadi-btn{
        background-color: #EEEEF6;
        transition: background-color 0.1s ease;
        border: 1px solid transparent;
        border-color: #d59432ff;
    }
    .gotadi-btn:hover{
        background-color: #d59432ff;
        color: white;
    }
    .button-gen-rep-bg-change {
        background-color: #28A745;
        transition: background-color 0.1s ease;
        border: 1px solid transparent;
        color: white;
    }
    .button-gen-rep-bg-change:hover {
        background-color: #26893dff;
        color: white;
    }
</style>

<section>
    <!-- Main Card -->
    <div class="card ms-3 me-3 mt-5">
        <!-- Filter Dropdown Section -->
        <div class="container-fluid mt-4">
            <div class="m-2 d-flex justify-content-between align-items-center">
                <h3 id="tadiTitle">TADI - Dean</h3>
                <button class="btn report-btn" id="exportBtn">Generate Report</button>
                <button class="btn gotadi-btn" id="tadiBtn" style="display: none">Go back to TADI</button>
            </div>
            <div class="row justify-content-center align-items-center g-3 mt-4">
                <!-- Academic Level Dropdown -->
                <div class="col-md">
                    <select class="form-select border border-dark select-shadow" style="background-color:#EEEEF6;" id="academiclevel" name="academiclevel">
                        <!-- <option value="" disabled selected>Academic Level</option> -->
                    </select>
                </div>
                <!-- Year Level Dropdown -->
                <div class="col-md">
                    <select class="form-select border border-dark select-shadow" style="background-color:#EEEEF6;" id="academicyearlevel" name="academicyearlevel">
                        <option value="" disabled selected>Year Level</option>
                    </select>
                </div>
                <!-- Period Dropdown -->
                <div class="col-md">
                    <select class="form-select border border-dark select-shadow" style="background-color:#EEEEF6;" id="academicperiod" name="academicperiod">
                        <option value="" disabled selected>Period</option>
                    </select>
                </div>
                <!-- Academic Year Dropdown -->
                <div class="col-md">
                    <select class="form-select border border-dark select-shadow" style="background-color:#EEEEF6;" id="acadyear" name="acadyear">
                        <option value="" disabled selected>Academic Year</option>
                    </select>
                </div>
                <!-- Start date and end time -->
                <div class="col-md date-range-xport" style="display:none">
                    <input type="text" class="form-control border border-dark select-shadow" style="background-color:#EEEEF6;" id="startDate" name="startDate" placeholder="Start Date">
                </div>
                <div class="col-md date-range-xport" style="display:none">
                    <input type="text" class="form-control border border-dark select-shadow" style="background-color:#EEEEF6;" id="endDate" name="endDate" placeholder="End Date">
                </div>
                <!-- Type Dropdown -->
                <!-- <div class="col-md">
                    <select class="form-select border border-dark select-shadow" style="background-color:#EEEEF6;" id="type" name="type">
                        <option value="" disabled selected>Select Type</option>
                        <option value="instructor">Instructor</option>
                        <option value="subject">Subject</option>
                    </select>
                </div> -->
                <!-- Search Input for Subject -->
                <div class="col-md box box-one" style="display:none;">
                    <input type="text" class="form-control border border-dark" id="searchInput" placeholder="Search Subject">
                </div>
                <!-- Search Input for Instructor -->
                <div class="col-md box box-two" style="display:none;">
                    <input type="text" class="form-control border border-dark" id="searchValInstr" placeholder="Search Instructor">
                </div>
                <!-- Search Button -->
                <div class="col-md">
                    <button type="button" id="search_button" class="btn w-100 button-search-bg-change tadi-search">
                        Search
                    </button>
                    <button type="button" id="reportSearch" class="btn w-100 button-gen-rep-bg-change tadi-search" style="display:none">
                        Generate Report
                    </button>
                </div>
            </div>

            <!-- End Filter Dropdown Section -->

            <!-- Instructor -->
            <!-- Instructor Dashboard -->
            <div class="mt-4">
                <div class="table-body box box-two instr-table" style="display:none;">
                    <div class="table mt-4 shadow-sm table-responsive shadow border" id="div">
                        <!-- <h5 class="mt-3 mb-3 ms-2" style="font-size: 1.2rem;">INSTRUCTOR</h5> -->
                        <div class="inst_list_tbl_wrapper">
                            <table class="table table-hover table-bordered inst_list_tbl" style="line-height: 2.5; border-color: rgb(157, 157, 157);">
                                <thead style="position:sticky; top:0; z-index:2">
                                    <tr>
                                        <th class="col" style="background-color: #181a46; color: white; text-align: center;">Name of Instructor</th>
                                        <th class="col-2" style="background-color: #181a46; color: white; text-align: center;">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="instructor">
                                    <tr>
                                        <td colspan="5" class="text-center">No data available</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
            <div class="export-header">
                <div class="export-content d-flex justify-content-between">
                </div>
            </div>
            
            <div id="reportContainer" class="container-fluid mt-4 report-container" style="display:none">
                
            </div>
            <!-- End Instructor Dashboard -->

            <!-- Modals -->

            <!-- Instructor Subject List-->
            <div class="modal fade" id="Instructor_Subject_List" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
                <div class="modal-dialog modal-dialog-centered modal-xl">
                    <div class="modal-content">
                        <!-- Modal Header -->
                        <div class="modal-header d-flex justify-content-between align-items-start" style="background-color: #181a46; color: white;">
                            <div class="subject-info">
                                <h5 class="modal-title tadi_inst_name" id="tadi_inst_name"></h5>
                            </div>
                        </div>

                        <!-- Modal Body -->
                        <div class="modal-body">
                            <div class="row justify-content-center align-items-center g-3">

                            </div>

                            <div class="mt-4">
                                <div class="shadow-sm">
                                    <div class="body">
                                        
                                            <div class="row" style="margin-bottom: 10px;">
                                                <div class="col-4" style="width: auto;">
                                                    <label for="BySubjDescCode">By Subject Description</label>
                                                    <input class="form-control" type="text" id="BySubjDesc" placeholder="Subject Description">
                                                </div>
                                                <div class="col-4" style="width: auto;">
                                                    <label for="BySubjDescCode">By Subject Code</label>
                                                    <input class="form-control" type="text" id="ByCode" placeholder="Subject Code">
                                                </div>
                                                <div class="col-3" style="width: auto;">
                                                    <label for="ByCode">By Section</label>
                                                    <input class="form-control" type="text" id="BySection" placeholder="Search Section">
                                                </div>
                                                <div class="col-1" style="width: auto; padding-top: 24px;">
                                                    <button id="searchSubjBtn" class="btn btn-primary searchSubjBtn">Search</button>
                                                </div>
                                                <div class="col-3 err-message-box border border-warning rounded-3" style="width: auto; margin-top: 1.5rem; padding:5px; background-color: #fff3e4; display:none;" id="subjErrorMessage">
                                                    <!--Warning Message -->
                                                </div>
                                            </div>
                                            <div class="table-scroll-width-limit">
                                                <table class="table table-hover table-bordered" style="line-height: 2.5; border-color: rgb(157, 157, 157);">
                                                    <thead style="background-color: #181a46; color: white; position:sticky; top:0; z-index:2">
                                                        <tr>
                                                            <th class="col" style="background-color: #181a46; color: white;">Section</th>
                                                            <th class="col-3" style="background-color: #181a46; color: white;">Subject Code</th>
                                                            <th class="col-3" style="background-color: #181a46; color: white;">Subject Description</th>
                                                            <th class="col-3" style="background-color: #181a46; color: white;"></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="subj_list">
                                                        

                                                    </tbody>
                                                </table>
                                            </div>
                                       
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Modal Footer -->
                        <div class="modal-footer d-flex justify-content-between">
                            <div class="legend"> 
                                <div>Number in <span class="badge bg-secondary"> </span> : Total number of records</div>
                                <div>Number in <span class="badge bg-danger"> </span> : Total number of unverified records</div>
                            </div>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>

                    </div>
                </div>
            </div>

            <!-- Instructor TADI List  -->
            <div class="modal fade tadirecord_list" id="Instructor_Tadi_List" tabindex="-1" aria-labelledby="tadiModalLabel" aria-hidden="true" data-bs-backdrop="static">
                <div class="modal-dialog modal-dialog-centered modal-xl" style="max-width: 90%; max-height:90%; width: auto;">
                    <div class="modal-content">
                        <!-- HEADER MODAL -->
                        <div class="modal-header d-flex justify-content-between align-items-start" style="background-color: #181a46; color: white;">
                            <div class="subject-info">
                                <h5 class="modal-title" id="tadi_subj_name">Subject Name</h5>
                                <p class="subject-details mb-0" id="section_name">section Name</p>
                            </div>
                            <!-- Close Button on Top Right -->
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" id="closeTadiModal1"></button>
                            <!--  END Close Button on Top Right -->
                        </div>
                        <!--END HEADER MODAL -->
                        <div class="modal-body" style="background-color: rgb(238, 238, 246); max-height: 80vh;">
                            <!-- Section List -->
                            <div>
                                <div class="card shadow-sm">
                                    <div class="card-body">
                                        <!-- <div class="row" style="margin-bottom: 10px; width:60%">
                                            <div class="col-4">
                                                <label for="strtDateSearch">BETWEEN</label>
                                                <input type="date" class="form-control dte_srch" id="strtDateSearch">
                                            </div>
                                            <div class="col-4">
                                                <label for="endDateSearch">AND</label>
                                                <input type="date" class="form-control dte_srch" id="endDateSearch" value="<?php echo date('Y-m-d'); ?>">
                                            </div>
                                            <div class="col-3">
                                                <label for="endDateSearch">STATUS</label>
                                                <select name="verification" id="verification" class="verification form-select">
                                                    <option value="" selected disabled>All</option>
                                                    <option value="1">Verified</option>
                                                    <option value="0">Unverified</option>
                                                </select>
                                            </div>
                                            <div class="col-1" style="padding-top: 24px">
                                                <button class="btn btn-primary srchdte" id="deanDate_srch">Search</button>
                                            </div>
                                        </div> -->
                                        <div class="table-responsive table_tadi_responsive">
                                            <table class="table table-hover table-bordered tadi_tbl" id="tadi_tbl" style="border-color: rgb(157, 157, 157);">
                                                <thead style="background-color: #181a46; color: white; position: sticky; top:0; z-index:2">
                                                    <tr>
                                                        <th class="col-2" style="background-color: #181a46; color: white;">Student Name</th>
                                                        <th class="col-2" style="background-color: #181a46; color: white;">Date and Time</th>
                                                        <th class="col-1" style="background-color: #181a46; color: white;">Class type</th>
                                                        <th style="background-color: #181a46; color: white;">Activity</th>
                                                        <th class="col-1" style="background-color: #181a46; color: white;">Attachment</th>
                                                        <th class="col-1" style="background-color: #181a46; color: white;">Status</th>
                                                    </tr>
                                                </thead>
                                                <style>
                                                    #prof_tadi_list_table tr, #prof_tadi_list_table td{
                                                        background-color: white;
                                                    }
                                                </style>
                                                <tbody id="prof_tadi_list_table">
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- End Section List -->
                        </div>
                        <!-- Modal Footer -->
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="Instructor_Section_List" tabindex="-1" aria-labelledby="tadiModalLabel" aria-hidden="true" data-bs-backdrop="static">
                <div class="modal-dialog modal-dialog-centered modal-xl" style="width: auto;">
                    <div class="modal-content">
                        <!-- HEADER MODAL -->
                        <div class="modal-header d-flex justify-content-between align-items-start" style="background-color: #181a46; color: white;">
                            <div class="subject-info">
                                <h5 class="modal-title" id="inst_subj_name">Section List</h5>
                                <!-- <p class="subject-details mb-0" id="subj_code">Subj_code</p> -->
                            </div>
                            <!-- Close Button on Top Right -->
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" id="closeTadiModal1"></button>
                            <!--  END Close Button on Top Right -->
                        </div>
                        <!--END HEADER MODAL -->
                        <div class="modal-body" style="background-color: rgb(238, 238, 246); max-height: 80vh; overflow-y: auto;">
                            <!-- Section List -->
                            <div class="mt-4">
                                <div class="card shadow-sm">
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-hover table-bordered tadi_tbl" id="tadi_tbl" style="line-height: 2.5; border-color: rgb(157, 157, 157);">
                                                <thead style="background-color: #181a46; color: white;">
                                                    <tr>
                                                        <th class="col-5" style="background-color: #181a46; color: white;">Section</th>
                                                        <th class="col-1" style="background-color: #181a46; color: white;"></th>
                                                    </tr>
                                                </thead>
                                                <tbody id="prof_section_list_table">
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- End Section List -->
                        </div>
                        <!-- Modal Footer -->
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Image Modal -->
            <div id="imageModal" class="image modal fade" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content fixed-modal">
                        <div class="modalHead">
                            <button type="button" class="btn-close" id="closeModalBtn"></button>
                        </div>
                        <div class="modal-body">
                            <div class="img-container">
                                <img id="attchPrev" src="" alt="Image Preview" class="img-fluid" />
                            </div>
                            <div class="img_details">
                                <div class="imgDetails img-taken">
                                    <div id="dateTimeTaken"></div>
                                </div>
                                <div class="imgDetails img-uploaded">
                                    <div id="dateTimeUpld"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Subject -->
            <!-- Subject Dashboard -->
            <div class=" mb-4">
                <div class="body box box-one subj-table">
                    <div class="table mt-4 shadow-sm table-responsive shadow border">
                        <!-- <h5 class="mt-3 mb-3 ms-2" style="font-size: 1.2rem;">SUBJECT</h5> -->
                        <div class="by_subj_table_wrapper">
                            <table class="table table-hover table-bordered by_subj_table" style="line-height: 2.5; border-color: rgb(157, 157, 157);">
                                <thead style="background-color: #007BFF; color: white;">
                                    <tr>
                                        <th class="col-1" style="background-color: #181a46; color: white;">Subject Code</th>
                                        <th class="col" style="background-color: #181a46; color: white; text-align: center;">Description</th>
                                        <th class="col-1" style="background-color: #181a46; color: white;">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="subject">
                                    <tr>
                                        <td colspan="5" class="text-center">No data available</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Subject Dashboard -->

            <!-- Subject Instructor List -->
            <div class="modal fade" id="Subject_Instructor_List" tabindex="-1" aria-labelledby="tadiModalLabel" aria-hidden="true" data-bs-backdrop="static">
                <div class="modal-dialog modal-dialog-centered modal-xl">
                    <div class="modal-content">
                        <!-- Modal Header -->
                        <div class="modal-header d-flex justify-content-between align-items-start" style="background-color: #181a46; color: white;">
                            <div class="subject-info">
                                <h5 class="modal-title" id="inst_subj_name">Instructor List</h5>
                                <!-- <p class="subject-details mb-0" id="subj_code">Subj_code</p> -->
                            </div>
                        </div>

                        <!-- Modal Body -->
                        <div class="modal-body">
                            <div class="row justify-content-center align-items-center g-3">
                                <!-- Potential content or filters can go here -->
                            </div>

                            <div class="mt-4">
                                <div class="shadow-sm">
                                    <div class="body">
                                        <div class="table-responsive">
                                            <table class="table table-hover table-bordered" style="line-height: 2.5; border-color: rgb(157, 157, 157);">
                                                <thead style="background-color: #181a46; color: white;">
                                                    <tr>
                                                        <th class="col" style="background-color: #181a46; color: white;">Instructor Name</th>
                                                        <th class="col-3" style="background-color: #181a46; color: white;">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="subj_instr_list">
                                                    <!-- Dynamic content will be loaded here -->
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Modal Footer -->
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Subject -->
            <!-- end container -->
        </div>
    </div>

    <div class="modal fade" id="alertModal" tabindex="-1" aria-labelledby="alertModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="alertModalLabel">Notice</h5>
            <!-- <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> -->
        </div>
        <div class="modal-body" id="alertModalBody">
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
        </div>
    </div>
    </div>

</section>
<script src="https://unpkg.com/xlsx/dist/xlsx.full.min.js"></script>
<script src="forms/tadi/dean/view/index-function.js?t=<?php echo time(); ?>"></script>
<script src="forms/tadi/dean/view/index-script.js?t=<?php echo time(); ?>"></script>