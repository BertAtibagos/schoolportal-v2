<section class="modal fade" id="submitted-grades-master-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel">Manage Student Academic Grades</h5>
                <button type="button" class="btn-close close" data-dismiss="modal" aria-label="Close" aria-hidden="true"></button>
            </div>
            <div id='div-message' class="text-danger"></div>

            <div class="modal-body">
                <div class="container-fluid">
                    <table id='table-offered-subject' class='table table-bordered table-hover table-responsive'>
                        <thead class='table-primary'>
                            <tr>
                                <th>Name:</th>
                                <td id='th-student-name'></td>
                            </tr>
                            <tr>
                                <th>A.Y. & Period:</th>
                                <td id='th-year-period'></td>
                            </tr>
                        </thead>
                    </table>
                    <div id="div-tbl-gscale" class="form-group input-group">
                    </div>
                    <p class="text-end mb-0">
                        FINAL AVERAGE (100%): <b id='b-final-average' class="text-danger fw-medium text-decoration-underline">0.0%</b>
                    </p>
                    <p class="text-start mb-0"> FINAL AVERAGE STATUS:
                    <p id='p-final-average-status' class="text-danger fw-medium text-decoration-underline"> PASSED </p></p>
                </div>
            </div>
        </div>
    </div>
</section>
<?php
    echo "<script src='../../js/custom/submitted-grades-modal-script.js'></script>";
?>