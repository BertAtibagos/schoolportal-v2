<link rel="stylesheet" href="forms/tadi/prof/css/css_tadi.css?t=<?php echo time(); ?>">

<section class="tadi-prof-section">
    <div class="container-fluid px-3 px-md-4 py-3">

        <div class="tadi-header">
            <div>
                <h3 class="tadi-title">
                    <i class="fas fa-clipboard-list me-2"></i>TADI &mdash; Professor
                </h3>
                <p class="tadi-subtitle">Teaching Activity Documentation &amp; Implementation</p>
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
                    <h5 class="section-label" id="summaryId">
                        <i class="fas fa-chart-bar me-2"></i>Dashboard Overview
                    </h5>
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
                                    <th scope="col">Modality</th>
                                    <th scope="col">Session Type</th>
                                    <th scope="col">Make-up Date</th>
                                    <th scope="col">Time</th>
                                    <th scope="col" class="text-center">Attachment</th>
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

    <div id="imageModal" class="modal fade" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content tadi-img-modal">

                <div class="tadi-img-modal-header">
                    <span class="img-modal-title">
                        <i class="fas fa-image me-2"></i>Attachment Preview
                    </span>

                    <button type="button" class="tadi-close-btn" id="closeModalBtn">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="modal-body p-3">
                    <div id="imageCarousel" class="carousel slide" data-bs-interval="false">
                        <div class="carousel-inner">
                            <!-- Start Image -->
                            <div class="carousel-item active">
                                <div class="img-container">
                                    <span class="image-label start-label">
                                        Start of class
                                    </span>
                                    <img id="start_attchPrev" src="" alt="Start Image" class="img-fluid">
                                </div>
                            </div>
                            <!-- End Image -->
                            <div class="carousel-item">
                                <div class="img-container">
                                    <span class="image-label end-label">
                                        End of class
                                    </span>
                                    <img id="end_attchPrev" src="" alt="End Image" class="img-fluid">
                                </div>
                            </div>
                        </div>
                        <!-- Previous -->
                        <button class="carousel-control-prev" type="button" data-bs-target="#imageCarousel" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon"></span>
                        </button>
                        <!-- Next -->
                        <button class="carousel-control-next" type="button" data-bs-target="#imageCarousel" data-bs-slide="next">
                            <span class="carousel-control-next-icon"></span>
                        </button>

                        <!-- Indicators -->
                        <div class="carousel-indicators">
                            <button type="button" data-bs-target="#imageCarousel" data-bs-slide-to="0" class="active"></button>
                            <button type="button" data-bs-target="#imageCarousel" data-bs-slide-to="1"></button>
                        </div>
                    </div>
                    <div class="img_details mt-2">
                        <div class="imgDetails img-taken">
                            <div id="startdateTimeTaken"></div>
                        </div>

                        <div class="imgDetails img-taken">
                            <div id="enddateTimeTaken"></div>
                        </div>

                        <div class="imgDetails img-uploaded">
                            <div id="dateTimeUpld"></div>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>

    <!-- <div id="uploadModal" class="modal fade" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content tadi-modal-content">
                <div class="tadi-modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-cloud-upload-alt me-2"></i>Upload Attachment
                    </h5>
                    <button type="button" class="tadi-close-btn" id="uploadcloseModalBtn">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="modal-body p-3">
                    <div class="upload-area">
                        <div class="upload-icon"><i class="fas fa-file-image"></i></div>
                        <p class="upload-hint">Select an image file (JPG, JPEG, PNG)</p>
                        <input type="file" class="form-control profUpload" name="attach" id="attach"
                               accept=".jpg,.jpeg,.png">
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn tadi-cancel-btn" data-bs-dismiss="modal">Cancel</button>
                    <button id="profUploadBtn" class="btn tadi-upload-submit-btn profUploadBtn" value="">
                        <i class="fas fa-upload me-2"></i>Upload
                    </button>
                </div>
            </div>
        </div>
    </div> -->

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