<?php  
    echo '<script>console.log("Session Data:", ' . json_encode($_SESSION) . ');</script>';
?>
<link rel="stylesheet" href="forms/tadi/dean/css/css_dean_tadi.css?t=<?php echo time(); ?>">
<section class="tadi-section">
    <div class="tadi-main-card mx-3 mt-2">

        <div class="tadi-page-header">
            <h1 class="tadi-page-title">
                <span class="tadi-title-chip"><i class="fas fa-chalkboard-teacher fa-sm"></i></span>
                <span id="tadiTitle">TADI &mdash; Dean</span>
            </h1>
            <div class="tadi-header-actions">
                <button class="tadi-btn tadi-btn-outline-white" id="exportBtn">
                    <i class="fa-solid fa-chart-area"></i> Generate Report
                </button>
                <button class="tadi-btn tadi-btn-ghost-gold" id="tadiBtn" style="display:none">
                    <i class="fas fa-arrow-left"></i> Back to TADI
                </button>
            </div>
        </div>

        <div class="px-3 px-md-4">
            <div class="tadi-filter-card">
                <div class="row g-3 align-items-end">

                    <div class="col-12 col-sm-6 col-lg">
                        <label class="tadi-filter-label">Academic Level</label>
                        <select class="form-select" id="academiclevel" name="academiclevel"></select>
                    </div>

                    <div class="col-12 col-sm-6 col-lg">
                        <label class="tadi-filter-label">Year Level</label>
                        <select class="form-select" id="academicyearlevel" name="academicyearlevel">
                            <option value="" disabled selected>Year Level</option>
                        </select>
                    </div>

                    <div class="col-12 col-sm-6 col-lg">
                        <label class="tadi-filter-label">Period</label>
                        <select class="form-select" id="academicperiod" name="academicperiod">
                            <option value="" disabled selected>Period</option>
                        </select>
                    </div>

                    <div class="col-12 col-sm-6 col-lg">
                        <label class="tadi-filter-label">Academic Year</label>
                        <select class="form-select" id="acadyear" name="acadyear">
                            <option value="" disabled selected>Academic Year</option>
                        </select>
                    </div>

                    <div class="col-12 col-sm-6 col-lg date-range-xport" style="display:none">
                        <label class="tadi-filter-label">Start Date</label>
                        <input type="text" class="form-control" id="startDate" name="startDate" placeholder="Start Date">
                    </div>

                    <div class="col-12 col-sm-6 col-lg date-range-xport" style="display:none">
                        <label class="tadi-filter-label">End Date</label>
                        <input type="text" class="form-control" id="endDate" name="endDate" placeholder="End Date">
                    </div>

                    <div class="col-12 col-sm-6 col-lg box box-one" style="display:none;">
                        <label class="tadi-filter-label">Subject</label>
                        <input type="text" class="form-control" id="searchInput" placeholder="Search Subject">
                    </div>
                    <div class="col-12 col-sm-6 col-lg box box-two" style="display:none;">
                        <label class="tadi-filter-label">Instructor</label>
                        <input type="text" class="form-control" id="searchValInstr" placeholder="Search Instructor">
                    </div>

                    <div class="col-12 col-sm-6 col-lg-auto">
                        <button type="button" id="search_button" class="tadi-btn tadi-btn-primary w-100 tadi-search" style="margin-bottom:3px;">
                            <i class="fas fa-search"></i> Search
                        </button>
                        <button type="button" id="reportSearch" class="tadi-btn tadi-btn-success w-100 tadi-search" style="margin-bottom:3px; display:none">
                            <i class="fas fa-chart-bar"></i> Generate Report
                        </button>
                    </div>

                </div>
            </div>
        </div>

        <div class="px-3 px-md-4 pb-4">

            <div class="instr-table" style="display:none;">
                <div class="tadi-content-card">
                    <div class="tadi-content-card-header">
                        <h5><i class="fas fa-users me-2" style="opacity:.75"></i>Instructor List</h5>
                    </div>
                    <div class="tadi-table-wrapper">
                        <table class="tadi-table inst_list_tbl">
                            <thead>
                                <tr>
                                    <th>Name of Instructor</th>
                                    <th style="width:160px; text-align:center;">Action</th>
                                </tr>
                            </thead>
                            <tbody id="instructor">
                                <tr>
                                    <td colspan="2" class="tadi-empty-state">
                                        <i class="fas fa-search d-block text-center mb-2" style="font-size:1.8rem; opacity:.35"></i>
                                        Select filters above and click <strong>Search</strong>.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="export-header" style="display:none;">
                <div class="tadi-report-header export-content"></div>
            </div>

            <div id="reportContainer" class="tadi-report-container" style="display:none"></div>

        </div>
    </div>


    <div class="subj-table px-3" style="display:none;">
        <div class="tadi-content-card mt-3 mb-4">
            <div class="tadi-content-card-header">
                <h5><i class="fas fa-book me-2" style="opacity:.75"></i>Subject List</h5>
            </div>
            <div class="tadi-table-wrapper">
                <table class="tadi-table by_subj_table">
                    <thead>
                        <tr>
                            <th style="width:140px;">Subject Code</th>
                            <th>Description</th>
                            <th style="width:140px; text-align:center;">Action</th>
                        </tr>
                    </thead>
                    <tbody id="subject">
                        <tr><td colspan="3" class="tadi-empty-state">No data available.</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade tadi-modal" id="Instructor_Subject_List" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title tadi_inst_name" id="tadi_inst_name">Instructor Name</h5>
                        <div class="modal-subtitle">Subject List</div>
                    </div>
                </div>
                <div class="modal-body">

                    <div class="tadi-modal-search-bar">
                        <div class="row g-2 align-items-end">
                            <div class="col-12 col-sm-6 col-md">
                                <label class="tadi-filter-label">Subject Description</label>
                                <input class="form-control" type="text" id="BySubjDesc" placeholder="e.g. Mathematics">
                            </div>
                            <div class="col-12 col-sm-6 col-md">
                                <label class="tadi-filter-label">Subject Code</label>
                                <input class="form-control" type="text" id="ByCode" placeholder="e.g. MATH101">
                            </div>
                            <div class="col-12 col-sm-6 col-md">
                                <label class="tadi-filter-label">Section</label>
                                <input class="form-control" type="text" id="BySection" placeholder="e.g. A1">
                            </div>
                            <div class="col-12 col-sm-6 col-md-auto">
                                <button id="searchSubjBtn" class="tadi-btn tadi-btn-accent w-100">
                                    <i class="fas fa-search"></i> Search
                                </button>
                            </div>
                        </div>
                        <div class="err-message-box mt-2 p-2 rounded"
                             style="display:none; background:#fff8e1; border:1px solid #ffe082; font-size:.82rem; color:#856404;"></div>
                    </div>

                    <div class="tadi-modal-table-card">
                        <div class="tadi-table-wrapper" style="max-height:45vh;">
                            <table class="tadi-table">
                                <thead>
                                    <tr>
                                        <th>Section</th>
                                        <th>Subject Code</th>
                                        <th>Subject Description</th>
                                        <th style="width:160px; text-align:center;">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="subj_list"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="tadi-btn tadi-btn-muted" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade tadi-modal tadirecord_list" id="Instructor_Tadi_List" tabindex="-1"
         aria-labelledby="tadiModalLabel" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered modal-xl" style="max-width:92%; width:auto;">
            <div class="modal-content">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title" id="tadi_subj_name">Subject Name</h5>
                        <div class="modal-subtitle" id="section_name">Section Name</div>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close" id="closeTadiModal1"></button>
                </div>
                <div class="modal-body">
                    <div class="tadi-modal-table-card">
                        <div class="tadi-table-wrapper" style="max-height:62vh;">
                            <table class="tadi-table" id="tadi_tbl">
                                <thead>
                                    <tr style="text-align:center;">
                                        <th>Student Name</th>
                                        <th>Date &amp; Time</th>
                                        <th>Class Type</th>
                                        <th>Make-up Date</th>
                                        <th>Activity</th>
                                        <th>Status</th>
                                        <th>Attachment</th>
                                        <th>Approval</th>
                                    </tr>
                                </thead>
                                <tbody id="prof_tadi_list_table"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="tadi-btn tadi-btn-muted" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade tadi-modal" id="Instructor_Section_List" tabindex="-1"
         aria-labelledby="tadiModalLabel" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered modal-xl" style="width:auto;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="inst_subj_name">Section List</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="tadi-modal-table-card">
                        <div class="tadi-table-wrapper" style="max-height:55vh;">
                            <table class="tadi-table">
                                <thead>
                                    <tr>
                                        <th>Section</th>
                                        <th style="width:150px; text-align:center;">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="prof_section_list_table"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="tadi-btn tadi-btn-muted" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div id="imageModal" class="modal fade" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered" >
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

    <div class="modal fade tadi-modal" id="Subject_Instructor_List" tabindex="-1"
         aria-labelledby="tadiModalLabel" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="subj_inst_name">Instructor List</h5>
                </div>
                <div class="modal-body">
                    <div class="tadi-modal-table-card">
                        <div class="tadi-table-wrapper" style="max-height:50vh;">
                            <table class="tadi-table">
                                <thead>
                                    <tr>
                                        <th>Instructor Name</th>
                                        <th style="width:160px; text-align:center;">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="subj_instr_list"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="tadi-btn tadi-btn-muted" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade tadi-modal tadi-alert-modal" id="alertModal" tabindex="-1"
         aria-labelledby="alertModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width:400px;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="alertModalLabel">
                        <i class="fas fa-info-circle me-2"></i>Notice
                    </h5>
                </div>
                <div class="modal-body" id="alertModalBody"
                     style="background:#fff; font-size:.9rem; color:#374151; padding:20px 24px;"></div>
                <div class="modal-footer">
                    <button type="button" class="tadi-btn tadi-btn-muted" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Close
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
<script src="https://unpkg.com/xlsx/dist/xlsx.full.min.js"></script>
<script src="forms/tadi/dean/view/index-ui.js?t=<?php echo time(); ?>"></script>
<script src="forms/tadi/dean/view/index-api.js?t=<?php echo time(); ?>"></script>