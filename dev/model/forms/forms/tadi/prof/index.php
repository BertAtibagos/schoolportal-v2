<?php  
    echo '<script>console.log("Session Data:", ' . json_encode($_SESSION) . ');</script>';
?>
<link rel="stylesheet" href="forms/tadi/prof/css/css_tadi.css?t=<?php echo time(); ?>">

<section class="tadi-prof-section">
    <div class="container-fluid px-3 px-md-4 py-3">

        <div class="tadi-header">
            <div>
                <h3 class="tadi-title">
                    <i class="fas fa-clipboard-list me-2"></i>TADI &mdash; Faculty
                </h3>
            </div>
            <button class="btn tadi-back-btn" id="summaryTadiBtn" style="display:none">
                <i class="fas fa-arrow-left me-1"></i> Back to Dashboard
            </button>
        </div>

        <div class="filter-card">
            <div class="filter-card-header">
                <i class="fas fa-filter me-2"></i>Search Filters
            </div>
            <div class="filter-card-body">
                <div class="row g-3 align-items-end">
                    <div class="col-12 col-sm-6 col-lg">
                        <label class="filter-label" for="academiclevel">Academic Level</label>
                        <select class="form-select filter-select" id="academiclevel" name="academiclevel"></select>
                    </div>
                    <div class="col-12 col-sm-6 col-lg">
                        <label class="filter-label" for="academicYearLevel">Year Level</label>
                        <select class="form-select filter-select" id="academicYearLevel" name="academicYearLevel">
                            <option value="" disabled selected>Year Level</option>
                        </select>
                    </div>
                    <div class="col-12 col-sm-6 col-lg">
                        <label class="filter-label" for="period">Period</label>
                        <select class="form-select filter-select" id="period" name="period">
                            <option value="" disabled selected>Period</option>
                        </select>
                    </div>
                    <div class="col-12 col-sm-6 col-lg">
                        <label class="filter-label" for="acadyear">School Year</label>
                        <select class="form-select filter-select" id="acadyear" name="acadyear">
                            <option value="" disabled selected>School Year</option>
                        </select>
                    </div>
                    <div class="col-12 col-sm-6 col-lg">
                        <label class="filter-label" for="subjectSearch">Subject Code</label>
                        <input type="text" class="form-control filter-select" id="subjectSearch"
                               name="subjectCode" placeholder="e.g. CS101">
                    </div>
                    <div class="col-12 col-sm-6 col-lg-auto">
                        <button type="button" class="btn tadi-search-btn w-100" id="searchButton" disabled>
                            <i class="fas fa-search me-2"></i>Search
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="main-content-card">
            <div class="main-content-body">

                <div class="summary">
                    <div class="stats-row">
                        <div class="stat-card stat-total">
                            <div class="stat-icon"><i class="fas fa-list-alt"></i></div>
                            <div class="stat-info">
                                <div class="stat-value" id="totalCount">0</div>
                                <div class="stat-label">Total Records</div>
                            </div>
                        </div>
                        <div class="stat-card stat-verified">
                            <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
                            <div class="stat-info">
                                <div class="stat-value" id="totalVerified">0</div>
                                <div class="stat-label">Verified</div>
                            </div>
                        </div>
                        <div class="stat-card stat-unverified">
                            <div class="stat-icon"><i class="fas fa-exclamation-circle text-danger"></i></div>
                            <div class="stat-info">
                                <div class="stat-value" id="totalUnverified">0</div>
                                <div class="stat-label">Unverified</div>
                            </div>
                        </div>
                        <div class="stat-card stat-overdue">
                            <div class="stat-icon"><i class="fas fa-exclamation-triangle"></i></div>
                            <div class="stat-info">
                                <div class="stat-value" id="totalDue">0</div>
                                <div class="stat-label">Past Due</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="inst_list_tbl_wrapper dashboard">
                    <table class="table tadi-table">
                        <thead id="theadTable">
                            <tr id="defaultHeader">
                                <th scope="col">Section</th>
                                <th scope="col">Subject</th>
                                <th scope="col" class="text-center">Total Records</th>
                                <th scope="col" class="text-center">Unverified</th>
                                <th scope="col" class="text-center">Pending Approval</th>
                                <th scope="col" class="text-center">Over Due</th>
                                <th scope="col"></th>
                            </tr>
                        </thead>
                        <tbody class="prof_dashboard_table"></tbody>
                    </table>
                </div>

            </div>
        </div>

    </div>

    <div class="modal fade" id="sectionList" tabindex="-1" aria-labelledby="tadiModalLabel" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog modal-dialog-centered tadi-modal-xl">
            <div class="modal-content tadi-modal-content">

                <div class="tadi-modal-header">
                    <div class="modal-subject-info">
                        <div class="modal-subject-badge"><i class="fas fa-book me-1"></i>Subject</div>
                        <h5 class="modal-title" id="subj_name">Subject Description</h5>
                        <p class="modal-section-text mb-0" id="subj_code">Section</p>
                    </div>
                    <button type="button" class="tadi-close-btn" data-bs-dismiss="modal"
                            aria-label="Close" id="closeTadiModal1">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <div class="tadi-modal-body">
                    <div class="date-filter-bar">
                        <div class="date-filter-inner">
                            <div class="date-filter-group">
                                <label class="date-filter-label" for="strtDateSearch">From</label>
                                <input type="date" class="form-control date-input" id="strtDateSearch">
                            </div>
                            <span class="date-separator"><i class="fas fa-arrows-alt-h"></i></span>
                            <div class="date-filter-group">
                                <label class="date-filter-label" for="endDateSearch">To</label>
                                <input type="date" class="form-control date-input" id="endDateSearch"
                                       value="<?php echo date('Y-m-d'); ?>">
                            </div>
                            <button class="btn tadi-date-search-btn srchdte" id="date_srch" data-summary="false">
                                <i class="fas fa-search me-1"></i>Search
                            </button>
                        </div>
                    </div>

                    <div class="records-table-wrapper">
                        <table class="table tadi-records-table" id="rcrd_tbl">
                            <thead>
                                <tr>
                                    <th scope="col">Date</th>
                                    <th scope="col">Student Name</th>
                                    <th scope="col">Time</th>
                                    <th></th>
                                    <th scope="col" class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody id="rcrd_tbl_body" class="student_tadi_list_table"></tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
    
    <div class="modal fade" id="alertModal" tabindex="-1" aria-labelledby="alertModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content tadi-modal-content">
                <div class="tadi-alert-header">
                    <h5 class="modal-title" id="alertModalLabel">
                        <i class="fas fa-info-circle me-2"></i>Notice
                    </h5>
                </div>
                <div class="modal-body px-4 py-3" id="alertModalBody"></div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn tadi-cancel-btn" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content tadi-modal-content">
                <div class="tadi-modal-header">
                    <h5 class="modal-title" id="confirmModalLabel">
                        <i class="fas fa-check-double me-2"></i>Confirm Verification
                    </h5>
                    <button type="button" class="tadi-close-btn" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="modal-body px-4 py-3">
                    Are you sure you want to verify this record?
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn tadi-cancel-btn" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn tadi-confirm-btn" id="confirmVerifyBtn">
                        <i class="fas fa-check me-2"></i>Verify
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade tadirecord_list" id="Instructor_Tadi_List" tabindex="-1" aria-labelledby="tadiModalLabel" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered modal-lg rcrd-preview-mdl">
            <div class="modal-content">
                <div class="modal-header d-flex justify-content-between align-items-center">
                    <div>Tadi Details</div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" id="closeTadiModal1"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive table_tadi_responsive">
                        <div class="tab-content" id="tadiDetails"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="border-radius: 8px;">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="toast-container position-fixed top-0 end-0 p-3">
        <div id="successToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="true">
            <div id="toastHeader" class="toast-header bg-success text-white">
                <strong id="toastTitle" class="me-auto">Success</strong>
                <button id="toastCloseBtn" type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">
                <span id="toastMessage"></span>
            </div>
        </div>
    </div>

</section>
<script src="forms/tadi/prof/view/index-ui.js?t=<?php echo time(); ?>"></script>
<script src="forms/tadi/prof/view/index-api.js?t=<?php echo time(); ?>"></script>