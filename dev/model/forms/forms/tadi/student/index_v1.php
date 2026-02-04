<section>
    <link rel="stylesheet" href="tadi/student/css_tadi.css">
    <div class="container-fluid mt-2">
        <div class="m-2">
            <h3>TADI - Student</h3>
        </div>
        <div class="card shadow-sm">
            <div class="card-body">
                <table class="table table-hover table-responsive" style="line-height: 2.5;" id="TadiStudentTadi">
                    <thead style="background-color: #091976;">
                        <tr>
                            <th scope="col" class="text-light">Subject Code</th>
                            <th scope="col" class="text-light">Description</th>
                            <th scope="col" class="text-light">Instructor</th>
                            <th scope="col" class="text-light"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="4" class="text-center">
                                <div id="loadingSpinner" class="spinner-border text-primary m-5" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal" tabindex="-1" aria-labelledby="tadiModalLabel1" aria-hidden="true"
        data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header flex justify-content-between align-items-start"
                    style="background-color: #181a46; color: white;padding-inline: 2rem;">
                    <div class="subject-info">
                        <h5 class="modal-title" id="tadi_modal_label"></h5>
                        <p id="subject_details" class="subject-details mb-0"></p>
                        <p id="date_now" class="mb-0"></p>
                    </div>
                    <button type="button" id="close_modal" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body" style="padding-inline: 2rem;">
                    <form id="tadiForm" enctype="multipart/form-data" novalidate>
                        <input type="text" style="display: none;" id="subjoff_id" name="subjoff_id">
                        <div class="row my-4">
                            <div class="col">
                                <label for="instructor" class="form-label">Instructor <span
                                        class="text-danger">*</span></label>
                                <select class="form-select" name="instructor" id="instructor" required>
                                    <option>Select Instructor</option>
                                </select>
                                <div class="invalid-feedback">Please select an instructor</div>
                            </div>

                            <div class="col">
                                <label for="learning_delivery_modalities" class="form-label">Learning Delivery
                                    Modalities <span class="text-danger">*</span></label>
                                <select class="form-select" name="learning_delivery_modalities"
                                    id="learning_delivery_modalities" required>
                                    <option value="" selected disabled>Select Mode</option>
                                    <option value="online_learning">Online Learning</option>
                                    <option value="onsite_learning">Onsite Learning</option>
                                </select>
                                <div class="invalid-feedback">Please select a learning delivery mode</div>
                            </div>

                            <div class="col">
                                <label for="session_type" class="form-label">Session Type <span
                                        class="text-danger">*</span></label>
                                <select class="form-select" name="session_type" id="session_type" required>
                                    <option value="" selected disabled>Select Type</option>
                                    <option value="regular">Regular Class</option>
                                    <option value="makeup">Make-Up Class</option>
                                </select>
                                <div class="invalid-feedback">Please select a session type</div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-4">
                                <label for="classStartDateTime" class="form-label">Class Start Time <span
                                        class="text-danger">*</span></label>
                                <input type="time" class="form-control" name="classStartDateTime"
                                    id="classStartDateTime" required>
                                <div class="invalid-feedback">Please enter a start time</div>
                            </div>

                            <div class="col-4">
                                <label for="classEndDateTime" class="form-label">Class End Time
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="time" class="form-control" name="classEndDateTime"
                                    id="classEndDateTime" required>
                                <div class="invalid-feedback">Please enter an end time</div>
                            </div>

                            <div class="col-4">
                                <label for="attach" class="form-label">Attachment
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="file" class="form-control" name="attach" id="attach" accept=".jpg,.jpeg,.png" required>
                            </div>
                            <input type="hidden" name="prof_id" value="0" id="prof_id">
                        </div>

                        <div class="mb-4">
                            <label for="comments" class="form-label">Remarks <span
                                    class="text-danger">*</span></label>
                            <textarea class="form-control" name="comments" id="comments" rows="5"
                                placeholder="Enter any additional comments or notes here..." required></textarea>
                            <div class="invalid-feedback">Please enter remarks</div>
                        </div>

                        <div class="alert alert-danger alert-dismissible fade show d-none" id="error_alert"
                            role="alert">
                            <strong>Error!</strong> <span id="errorAlertMessage"></span>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn submitTadi" id="confirmBtn"
                        style="background-color: #181a46; color: white;">Submit</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast container -->
    <div class="toast-container position-fixed top-0 end-0 p-3">
        <div id="successToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true"
            data-bs-autohide="true">
            <div class="toast-header bg-success text-white">
                <strong class="me-auto">Success</strong>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"
                    aria-label="Close"></button>
            </div>
            <div class="toast-body">
                <span id="toastMessage"></span>
            </div>
        </div>
    </div>
</section>
<script src="tadi/student/view/index-function.js?t=<?php echo time(); ?>"></script>
<script src="tadi/student/view/index-script.js?t=<?php echo time(); ?>"></script>