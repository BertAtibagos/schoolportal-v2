<link rel="stylesheet" href="forms/tadi/student/css/css_tadi_stud.css?t=<?php echo time(); ?>">
<section id="student_tadi_section">
    <div class="container-fluid mt-4 px-3 px-md-4">
        <div class="tadi-section-header">
            <h4>My Subjects</h4>
            <p>Select a subject to submit or view your TADI records.</p>
        </div>
        <div id="card_container" class="d-flex flex-wrap gap-3 justify-content-start">
        </div>
    </div>

    <!-- TADI Submission Modal -->
    <div class="modal fade" id="modal" tabindex="-1" aria-labelledby="tadiModalLabel1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header d-flex justify-content-between align-items-start">
                    <div class="subject-info">
                        <h5 class="modal-title" id="tadi_modal_label"></h5>
                        <p id="subject_details" class="subject-details mb-0"></p>
                        <p id="date_now" class="mb-0"></p>
                    </div>
                    <button type="button" id="close_modal" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="tadiForm" enctype="multipart/form-data" novalidate>
                        <div class="row g-3 mb-3">
                            <input type="hidden" id="subjoff_id" name="subjoff_id">

                            <div class="col-md-6 col-lg-4">
                                <label for="instructor" class="form-label">Faculty <span class="text-danger">*</span></label>
                                <select class="form-select" name="instructor" id="instructor" required>
                                    <option>Select Faculty</option>
                                </select>
                                <div class="invalid-feedback">Please select a faculty</div>
                            </div>

                            <div class="col-md-6 col-lg-4">
                                <label for="learning_delivery_modalities" class="form-label">Learning Delivery Modalities <span class="text-danger">*</span></label>
                                <select class="form-select" name="learning_delivery_modalities"
                                    id="learning_delivery_modalities" required>
                                    <option value="" selected disabled>Select Mode</option>
                                    <option value="online_learning">Online Learning</option>
                                    <option value="onsite_learning">Onsite Learning</option>
                                </select>
                                <div class="invalid-feedback">Please select a learning delivery mode</div>
                            </div>

                            <div class="col-md-6 col-lg-4">
                                <label for="session_type" class="form-label">Session Type <span class="text-danger">*</span></label>
                                <select class="form-select" name="session_type" id="session_type" required>
                                    <option value="" selected disabled>Select Type</option>
                                    <option value="regular">Regular Class</option>
                                    <option value="makeup">Make-Up Class</option>
                                </select>
                                <div class="invalid-feedback">Please select a session type</div>
                                <div class="d-none" id="makeup_date_section"></div>
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6 col-lg-4">
                                <label for="classStartDateTime" class="form-label">Class Start Time <span class="text-danger">*</span></label>
                                <input type="time" class="form-control" name="classStartDateTime"
                                    id="classStartDateTime" required>
                                <div class="invalid-feedback">Please enter a start time</div>
                            </div>

                            <div class="col-md-6 col-lg-4">
                                <label for="classEndDateTime" class="form-label">Class End Time <span class="text-danger">*</span></label>
                                <input type="time" class="form-control" name="classEndDateTime" id="classEndDateTime" required>
                                <div class="invalid-feedback">Please enter an end time</div>
                            </div>

                            <div class="col-md-6 col-lg-4">
                                <label for="attach" class="form-label">Attachment <span class="text-danger">*</span></label>
                                <input type="file" class="form-control" name="attach" id="attach" accept=".jpg,.jpeg,.png,.webp" required>
                                <div class="invalid-feedback">Please upload an image</div>
                            </div>
                            <input type="hidden" name="prof_id" value="0" id="prof_id">
                        </div>

                        <div id="chckbox_late_submss_div" class="d-none mb-3">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="chck_late_submt">
                                <label class="form-check-label" for="chck_late_submt">Late Submission</label>
                            </div>
                        </div>

                        <div class="d-none" id="late_submss_section"></div>

                        <div class="mb-3">
                            <label for="comments" class="form-label">Remarks <span class="text-danger">*</span></label>
                            <textarea class="form-control" name="comments" id="comments" rows="4"
                                placeholder="Enter any additional comments or notes here..." required></textarea>
                            <div class="invalid-feedback">Please enter remarks</div>
                        </div>

                        <div class="alert alert-danger fade show d-none" id="error_alert" role="alert">
                            <strong>Error!</strong> <span id="errorAlertMessage"></span>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-submit-tadi submitTadi" id="confirmBtn">
                        <span class="submit-label">Submit</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- View Records Modal -->
    <div class="modal fade tadirecord_list" id="Instructor_Tadi_List" tabindex="-1" aria-labelledby="tadiModalLabel" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered modal-lg rcrd-preview-mdl">
            <div class="modal-content">
                <div class="modal-header d-flex justify-content-between align-items-center">
                    <div id="tadi-date"></div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" id="closeTadiModal1"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive table_tadi_responsive">
                        <nav>
                            <div class="nav nav-tabs" id="nav-tab" role="tablist"></div>
                        </nav>
                        <div class="tab-content" id="nav-tabContent"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="border-radius: 8px;">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Image Preview Modal -->
    <div id="imageModal" class="modal fade" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content fixed-modal" style="margin:auto">
                <div class="modalHead">
                    <button type="button" class="btn-close" id="closeModalBtn"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="img-container">
                        <img id="attchPrev" src="" alt="Image Preview" />
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

    <!-- Toast -->
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
<script src="forms/tadi/student/view/index-function.js?t=<?php echo time(); ?>"></script>
<script src="forms/tadi/student/view/index-script.js?t=<?php echo time(); ?>"></script>