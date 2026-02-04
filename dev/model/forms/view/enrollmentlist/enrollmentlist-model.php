<section class="px-4">
    <style>
        #divStudentList {
            overflow: auto;
            max-height: 85vh;
        }
    </style>
    <div class="row mt-4">
        <div class="col-md-3 mb-3">
            <div class="row align-items-center mb-2">
                <div class="col-md-3">
                    <label for="acadlevel"><b>Level:</b></label>
                </div>
                <div class="col-md-9">
                    <select name="acadlevel" id="acadlevel" class="form-select"></select>
                </div>
            </div>
            <div class="row align-items-center mb-2">
                <div class="col-md-3">
                    <label for="acadyear"><b>Year:</b></label>
                </div>
                <div class="col-md-9">
                    <select name="acadyear" id="acadyear" class="form-select"></select>
                </div>
            </div>
            <div class="row align-items-center mb-2">
                <div class="col-md-3">
                    <label for="acadperiod"><b>Period:</b></label>
                </div>
                <div class="col-md-9">
                    <select name="acadperiod" id="acadperiod" class="form-select"></select>
                </div>
            </div>
            <div class="row align-items-center mb-2">
                <div class="col-md-3">
                    <label for="acadyearlevel"><b>Year Level:</b></label>
                </div>
                <div class="col-md-9">
                    <select name="acadyearlevel" id="acadyearlevel" class="form-select"></select>
                </div>
            </div>
            <div class="row align-items-center mb-2">
                <div class="col-md-3">
                    <label for="acadcourse"><b>Course:</b></label>
                </div>
                <div class="col-md-9">
                    <select name="acadcourse" id="acadcourse" class="form-select"></select>
                </div>
            </div>
            <div class="row align-items-center mb-2">
                <div class="col-md-3">
                    <label for="acadsection"><b>Section:</b></label>
                </div>
                <div class="col-md-9">
                    <select name="acadsection" id="acadsection" class="form-select"></select>
                </div>
            </div>
            <div class=""><input type="submit" id="btnSearch" class="btn btn-primary" value="Search"></div>
        </div>
        <div class="col-md-9" id="">
            <div id="divStudentCount">
            </div>
            <div class="" id="divStudentList">
                <table class="table table-bordered table-hover" style="border: solid 1px gray;">
                    <thead class="bg-primary bg-opacity-25">
                        <th>#</th>
                        <th>Student ID</th>
                        <th>Student Name</th>
                        <th>Section & Year Level</th>
                        <th class="text-center">Gender</th>
                        <th>Mobile</th>
                        <th>Email Address</th>
                    </thead>
                    <tbody id="table-body">
                    </tbody>
                </table>
            </div>
         </div>
    </div>    
</section>

<script src="../../js/custom/enrollmentlist-script.js?d=<?php echo time();?>"></script>