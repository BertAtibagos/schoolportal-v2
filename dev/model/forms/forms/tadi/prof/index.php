<link rel="stylesheet" href="forms/tadi/prof/css_tadi.css">
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

    .dte_srch {
        width: 12rem;
    }

    .srchdte,
    .acknw,
    .viewAttch,
    .profUploadBtn {
        background-color: #181a46;
        color: white;
    }

    .log_tbl {
        text-align: center;
    }

    .upldprof {
        background-color: rgba(51, 212, 137, 1);
        color: black
    }

    .modal-backdrop.show {
        z-index: 1050;
    }

    .modal-backdrop.modal-stack {
        z-index: 1055 !important;
    }

    #imageModal {
        z-index: 1060;
    }

    #uploadModal {
        z-index: 1060;
    }

    .img_details {
        padding: 5px;
        display: flex;
        flex-direction: column;
    }

    .imgDetails {
        font-size: 13px;
    }

    .modalHead {
        display: flex;
        flex-direction: row-reverse;
        padding: 5px;
    }

    .activity-text {
        white-space: pre-wrap;
        word-wrap: break-word;
        max-width: 600px;
        display: inline-block;
        text-align: left;
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
		max-height: 56vh;
    	overflow-y: auto;
	}
    .inst_list_tbl_wrapper.dashboard{
        max-height: 33vh;
    }
    .legend{
        font-size: 14px;
        margin-top: 10px;
    }
    ::-webkit-scrollbar {
        width: 5px;
    }
    ::-webkit-scrollbar-track {
        border-radius: 10px;
    }
    
    ::-webkit-scrollbar-thumb {
        background: rgb(75, 75, 75); 
        border-radius: 10px;
    }
    .summary-hide{
        display: none;
    }

</style>

<section>
    <div>
        <div class="container-fluid">
            <div class="m-2 d-flex justify-content-between align-items-center">
                <h3>TADI - Professor</h3>
                <button class="btn btn-outline-dark" id="summaryTadiBtn" style="display:none">Back to Dashboard</button>
            </div>
            <div class="row justify-content-center align-items-center g-3 mt-4">
                <div class="col-md">
                    <select class="form-select border border-dark select-shadow" id="academiclevel" style="background-color: #EEEEF6;" name="academiclevel">
                    </select>
                </div>
                <div class="col-md">
                    <select class="form-select border border-dark select-shadow" id="academicYearLevel" style="background-color: #EEEEF6;" name="academicYearLevel">
                        <option value="" disabled selected>Year Level</option>
                    </select>
                </div>
                <div class="col-md">
                    <select class="form-select border border-dark select-shadow" id="period" style="background-color: #EEEEF6;" name="period">
                        <option value="" disabled selected>Period</option>
                    </select>
                </div>
                <div class="col-md">
                    <select class="form-select border border-dark select-shadow" id="acadyear" style="background-color: #EEEEF6;" name="acadyear">
                        <option value="" disabled selected>School Year</option>
                    </select>
                </div>
                <div class="col-md">
                    <input type="text" class="form-control" id="subjectSearch" name="subjectCode" style="background-color: #EEEEF6;" placeholder="Subject Code">
                </div>
                <div class="col-md">
                    <button type="button" class="btn w-100" style="background-color: #181a46; color: white;" id="searchButton" disabled>Search</button>
                </div>
            </div>

            <div class="my-4">
                <div class="card shadow-sm">
                   <div class="card-body" style="max-height: 60vh; overflow-y: auto;">
                        <h4 id="summaryId">Summary</h4>
                        <div class="row">
                            <div class="summary row justify-content-center">
                                <div class="col-md-3 mx-2" style="margin-bottom: 5px">
                                    <div class="border rounded p-3 text-center h-100 border-secondary border-3" style="background-color: #c9c9c9;">
                                        <h6>Total Records</h6>
                                        <h3 id="totalCount">0</h3>
                                    </div>
                                </div>
                                <div class="col-md-3 mx-2" style="margin-bottom: 5px">
                                    <div class="border rounded p-3 text-center h-100 border-success border-3 text-success" style="background-color: #ddffef;">
                                        <h6>Total Verified</h6>
                                        <h3 id="totalVerified">0</h3>
                                    </div>
                                </div>
                                <div class="col-md-3 mx-2" style="margin-bottom: 5px">
                                    <div class="border rounded p-3 text-center h-100 border-danger border-3 text-danger" style="background-color: #fff1f2;">
                                        <h6>Total Unverified</h6>
                                        <h3 id="totalUnverified">0</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="inst_list_tbl_wrapper dashboard">
                            <table class="table table-bordered table-hover" style="line-height: 2.5; border-color: rgb(157, 157, 157);">
                                <thead style="position:sticky; top:0; z-index:2" id="theadTable">
                                    <tr id="defaultHeader">
                                        <th scope="col" style="background-color: #181a46; color: white;">Section</th>
                                        <th scope="col" style="background-color: #181a46; color: white;">Subject</th>
                                        <th scope="col" style="background-color: #181a46; color: white;">Total Records</th>
                                        <th scope="col" style="background-color: #181a46; color: white;">Unverified Records</th>
                                        <th scope="col" style="background-color: #181a46; color: white;"></th>
                                    </tr>
                                </thead>
                                
                                <tbody class="prof_dashboard_table">
                                    
                                </tbody>                            
                            </table>
                        </div>
                    </div>
                </div>
                <div class="legend" style="display: none"> 
                    <div>Number in <span class="badge bg-secondary"> </span> : Total number of records</div>
                    <div>Number in <span class="badge bg-danger"> </span> : Total number of unverified records</div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="sectionList" tabindex="-1" aria-labelledby="tadiModalLabel" aria-hidden="true" data-backdrop="static">
            <div class="modal-dialog modal-dialog-centered" style="max-width:95%;">
                <div class="modal-content">

                    <div class="modal-header d-flex justify-content-between align-items-start" style="background-color: #181a46; color: white;">
                        <div class="subject-info">
                            <h5 class="modal-title" id="subj_name">Subject_Desc Placeholder</h5>
                            <p class="subject-details mb-0" id="subj_code">Section Placeholder</p>
                        </div>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" id="closeTadiModal1"></button>
                    </div>
                    <div class="modal-body">
                        <label for="strtDateSearch">BETWEEN</label>
                        <input type="date" class="dte_srch" id="strtDateSearch">
                        <label for="endDateSearch">AND</label>
                        <input type="date" class="dte_srch" id="endDateSearch" value="<?php echo date('Y-m-d'); ?>">
                        <button class="btn srchdte" id="date_srch" data-summary="false">Search</button>
                        <div class="mt-4">
                            <div class="card shadow-sm">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-hover table-bordered log_tbl" style="line-height: 2.5; border-color: rgb(157, 157, 157); display: block; height: 400px; overflow-y: auto; width:100%;" id="rcrd_tbl">
                                            <thead style="color: white; width:100%;">
                                                <tr style="position: sticky; top: 0;">
                                                    <th scope="col" style="background-color: #181a46; color: white; width:20%;">Date</th>
                                                    <th scope="col" style="background-color: #181a46; color: white; width:20%;">Student Name</th>
                                                    <th scope="col" style="background-color: #181a46; color: white; width:20%;">Learning Modality</th>
                                                    <th scope="col" style="background-color: #181a46; color: white; width:20%;">Session Type</th>
                                                    <th scope="col" style="background-color: #181a46; color: white; width:15%;">Time</th>
                                                    <th scope="col" style="background-color: #181a46; color: white; width:10%;">Attachment</th>
                                                    <th scope="col" style="background-color: #181a46; color: white; width:10%;"></th>
                                                </tr>
                                            </thead>
                                            <tbody id="rcrd_tbl_body" class="student_tadi_list_table" style="width:100%">
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- VIEW IMAGE MODAL -->
        <div id="imageModal" class="modal fade" tabindex="-1">
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
        <!-- UPLOAD MODAL -->
        <div id="uploadModal" class="modal fade" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Upload Image</h5>
                        <button type="button" class="btn-close" id="uploadcloseModalBtn"></button>
                    </div>
                    <div class="modal-body text-center row align-items-center justify-content-center">
                        <div class="col-md">
                            <input type="file" class="form-control profUpload" name="attach" id="attach" accept=".jpg,.jpeg,.png">
                        </div>
                        <div class="col-md-3">
                            <button id="profUploadBtn" class="btn profUploadBtn" value="">Submit</button>
                        </div>
                    </div>
                </div>
            </div>
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

    <!-- Add this to your HTML file -->
    <div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="confirmModalLabel">Confirm Verification</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            Are you sure you want to verify this record?
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-primary" id="confirmVerifyBtn">Verify</button>
        </div>
        </div>
    </div>
    </div>
</section>
<script src="forms/tadi/prof/view/index-function.js?t=<?php echo time(); ?>"></script>
<script src="forms/tadi/prof/view/index-script.js?t=<?php echo time(); ?>"></script>