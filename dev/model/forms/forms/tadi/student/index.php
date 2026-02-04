<style>
    #student_tadi_section .activity-text {
        white-space: pre-wrap;
        word-wrap: break-word;
        max-width: 600px;
        display: inline-block;
        text-align: justify;
    }

    #student_tadi_section .fixed-modal {
        width: 600px;
        /* fixed width */
        height: 500px;
        /* fixed height */
        max-width: 90vw;
        /* still responsive on small screens */
        max-height: 90vh;
        font-size: 13px;
    }

    #student_tadi_section .img-container {
        width: 100%;
        height: 400px;
        /* space reserved for image */
        display: flex;
        justify-content: center;
        align-items: center;
        overflow: hidden;
        /* prevents image from spilling out */
    }

    #student_tadi_section .img-container img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
        /* keeps aspect ratio, fits inside */
    }

    #student_tadi_section .modalHead {
        display: flex;
        flex-direction: row-reverse;
        padding: 5px;
    }

    #student_tadi_section .rcrd-preview-mdl {
        max-width: 90%;
        max-height: 90%;
        width: 50%;
        margin: auto
    }

    @media only screen and (max-width: 600px) {
        #student_tadi_section .faculty-list {
            font-size: 0.7rem;
        }

        #student_tadi_section .vw_tadi_rec {
            margin-top: 10%;
        }

        #student_tadi_section .faculty-record {
            font-size: 0.8rem;
        }

        #student_tadi_section .rcrd-preview-mdl {
            max-width: 90%;
            max-height: 90%;
            width: auto;
            margin: auto
        }
		#student_tadi_section .subj-list-wrapper{
            max-height: 80vh;
            overflow-y: auto;
        }
    }

    #student_tadi_section .label {
        font-weight: bold;
    }

    #student_tadi_section .table_tadi_responsive {
        max-height: 60vh;
        overflow-y: auto;
    }

    #student_tadi_section .loading {
        display: none;
    }

    #student_tadi_section .loading.active {
        display: block;
    }
    #student_tadi_section .thlabel{
        /** color:white; **/
    }
</style>
<section id="student_tadi_section">
    <div class="container-fluid mt-4" style="margin:1rem">
        <div class="mt-4" >
            <div class="shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <div class="subj-list-wrapper">
                        <table class="table table-hover" style="line-height: 2.5;">
                            <thead style="background-color: #181a46; position:sticky; top:0; z-index:2">
                                <tr>
                                    <th class="thlabel" scope="col">Subject Code</th>
                                    <th class="thlabel" scope="col">Description</th>
                                    <th class="thlabel" scope="col">Faculty</th>
                                    <th class="thlabel" scope="col"></th>
                                </tr>
                            </thead>
                            <tbody class="faculty-list">

                            </tbody>
                        </table>
                    </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal" tabindex="-1" aria-labelledby="tadiModalLabel1" aria-hidden="true" data-bs-backdrop="static">
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
                <div class="modal-body" style="padding-inline: 2rem; max-height:50vh; overflow-y:auto;">
                    <form id="tadiForm" enctype="multipart/form-data" novalidate>
                        <div class="container">
                            <div class="row my-4">

                                <div class="col-md-6 col-lg-4">
                                    <input type="text" style="display: none;" id="subjoff_id" name="subjoff_id">
                                    <label for="instructor" class="form-label">Faculty <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select" name="instructor" id="instructor" required>
                                        <option>Select Faculty</option>
                                    </select>
                                    <div class="invalid-feedback">Please select a faculty</div>
                                </div>

                                <div class="col-md-6 col-lg-4">
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

                                <div class="col-md-6 col-lg-4">
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
                                <div class="col-md-6 col-lg-4">
                                    <label for="classStartDateTime" class="form-label">Class Start Time <span
                                            class="text-danger">*</span></label>
                                    <input type="time" class="form-control" name="classStartDateTime"
                                        id="classStartDateTime" required>
                                    <div class="invalid-feedback">Please enter a start time</div>
                                </div>

                                <div class="col-md-6 col-lg-4">
                                    <label for="classEndDateTime" class="form-label">Class End Time
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="time" class="form-control" name="classEndDateTime" id="classEndDateTime" required>
                                    <div class="invalid-feedback">Please enter an end time</div>
                                </div>

                                <div class="col-md-6 col-lg-4">
                                    <label for="attach" class="form-label">Attachment
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="file" class="form-control" name="attach" id="attach" accept=".jpg,.jpeg,.png" required>
                                    <div class="invalid-feedback">Please upload an image</div>
                                </div>
                                <input type="hidden" name="prof_id" value="0" id="prof_id">
                            </div>

                            <div class="d-none" id="late_submss_section">
                                
                            </div>
                            
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
                        style="background-color: #181a46; color: white;">
                        <span class="submit-label">Submit</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade tadirecord_list" id="Instructor_Tadi_List" tabindex="-1" aria-labelledby="tadiModalLabel" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered modal-xl rcrd-preview-mdl">
            <div class="modal-content">
                <div class="modal-header d-flex justify-content-between align-items-start" style="background-color: #181a46; color: white;">
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" id="closeTadiModal1"></button>
                </div>
                <div class="modal-body" style="background-color: rgb(238, 238, 246); max-height: 80vh;">
                    <div>
                        <div class="shadow-sm">
                            <div class="card-body">
                                <div class="table-responsive table_tadi_responsive">
                                    <div class="faculty-record">
                                        <nav>
                                            <div class="nav nav-tabs" id="nav-tab" role="tablist">
                                            </div>
                                        </nav>
                                        <div class="tab-content" id="nav-tabContent">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div id="imageModal" class="modal fade" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content fixed-modal" style="margin:auto">
                <div class="modalHead">
                    <button type="button" class="btn-close" id="closeModalBtn"></button>
                </div>
                <div class="modal-body">
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

    <div class="modal fade" id="confirmSubmitModal" tabindex="-1" aria-labelledby="confirmSubmitModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmSubmitModalLabel">Confirm Submission</h5>
            </div>
            <div class="modal-body">
                Are you sure you want to submit this TADI?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmSubmitBtn">Submit</button>
            </div>
            </div>
        </div>
    </div>
</section>
<script src="forms/tadi/student/view/index-function.js?t=<?php echo time(); ?>"></script>
<script src="forms/tadi/student/view/index-script.js?t=<?php echo time(); ?>"></script>