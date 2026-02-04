    <script>
        function addComp()
        {
            var x = document.getElementById("table-list");
                if(x.style.display == "block"){
                    x.style.display = "none";
                } else {
                    x.style.display = "block";
                }
        }
    </script>
<section id="class-list">

	<br><br>
        <div class="container">
             <div class="row"> 
                <div class="col-md-3 vertical" style="border-right: 1px solid black;">



                    <div id="dropdown-academic-year">
                        
                    </div>




                    <label class="form-label" for=""> ACADEMIC YEAR </label>
                    <select id="" name="" class="form-control" required>
                        <option value=''> 2022 - 2023 </option>;
                    </select>                            
                

                    <label class="form-label" for="" > PERIOD </label>
                    <select id="" name="" class="form-control" required>
                        <option value=''> 1ST SEM </option>;
                        <option value=''> 2ND SEM </option>;
                    </select>
                </div>
                <div class="col-md-8">
                <br>
                <table id='regtable' class='table table-hover table-responsive table-bordered'>
                    <thead class='table table-primary'>
                        <tr>
                            <th scope='col' style='text-align:center;'>#</th>
                            <th scope='col' style='text-align:center;'>Course Code</th>
                            <th scope='col' style='text-align:center;'>Course Description</th>
                            <th scope='col' style='text-align:center;'>Class List</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr>
                            <td style='text-align:center;'><label type='label'> 1 </label></td>
                            <td style='text-align:center;'><label type='label'> COM 105 </label></td>
                            <td style='text-align:center;'><label type='label'> COMMUNICATION PLANNING </label></td>
                            <td style='text-align:center;'><label type='label'><button type='button' class='btn btn-success' onclick="addComp()">View Class List </button></label></td>
                        </tr>
                    </tbody>

                    </table>
        

                </div>
            </div>
        </div>
        <br><hr><br>
        <div class="container" id="table-list">
            <table id='regtable' class='table table-hover table-responsive table-bordered' >
                <tbody>
                    <tr>
                        <td style='text-align:left;'><label type='label'> <b> ACADEMIC YEAR / PERIOD:</b> </label></td>
                        <td style='text-align:left;'><label type='label'> 2022-2023 (1ST SEM) </label></td>
                    </tr>
                    <tr>
                        <td style='text-align:left;'><label type='label'> <b> SUBJECT CODE: </b> </label></td>
                        <td style='text-align:left;'><label type='label'> COM 105 </label></td>
                    </tr>
                    <tr>
                        <td style='text-align:left;'><label type='label'> <b> SUBJECT DESCRIPTION: </b> </label></td>
                        <td style='text-align:left;'><label type='label'> COMMUNICATION PLANNING </label></td>
                    </tr>
                    <tr>
                        <td style='text-align:left;'><label type='label'> <b> SUBJECT UNIT: </b> </label></td>
                        <td style='text-align:left;'><label type='label'> 3 </label></td>
                    </tr>
                    <tr>
                        <td style='text-align:left;'><label type='label'> <b> SECTION: </b> </label></td>
                        <td style='text-align:left;'><label type='label'> BAC 3A </label></td>
                    </tr>
                    <tr>
                        <td style='text-align:left;'><label type='label'> <b> INSTRUCTOR/PROFESSOR: </b> </label></td>
                        <td style='text-align:left;'><label type='label'> GAMAYO, GEORGE NA </label></td>
                    </tr>
                    <tr>
                        <td style='text-align:left;'><label type='label'> <b> SCHEDULE: </b> </label></td>
                        <td style='text-align:left;'><label type='label'> SATURDAY 12:00 NN- 3:00 PM </label></td>
                    </tr>
                </tbody>            
            </table>

            <table id='regtable' class='table table-hover table-responsive table-bordered'>
                <thead class='table table-primary'>
                    <tr>
                        <th scope='col' style='text-align:center;' rowspan="2">#</th>
                        <th scope='col' style='text-align:center;' rowspan="2">Name</th>
                        <th scope='col' style='text-align:center;' colspan="2">Gender</th>
                        <th scope='col' style='text-align:center;' rowspan="2">Course Code</th>
                        <th scope='col' style='text-align:center;' rowspan="2">Year Level</th>
                    </tr>
                    <tr>
                        <th scope='col' style='text-align:center;'>male</th>
                        <th scope='col' style='text-align:center;'>female</th>
                    </tr>                   
                </thead>

                <tbody>
                    <tr>
                        <td style='text-align:center;'><label type='label'> 1 </label></td>
                        <td style='text-align:center;'><label type='label'> CASTELLON, CHERRY JOICE </label></td>
                        <td style='text-align:center;'><label type='label'>   </label></td>
                        <td style='text-align:center;'><label type='label'> o </label></td>
                        <td style='text-align:center;'><label type='label'> BAC </label></td>
                        <td style='text-align:center;'><label type='label'> 3RD YEAR </label></td>
                    </tr>
                    <tr>
                        <td style='text-align:center;'><label type='label'> 2 </label></td>
                        <td style='text-align:center;'><label type='label'> IGNACIO, FRANCHESCA RAINA </label></td>
                        <td style='text-align:center;'><label type='label'>   </label></td>
                        <td style='text-align:center;'><label type='label'> o </label></td>
                        <td style='text-align:center;'><label type='label'> BAC </label></td>
                        <td style='text-align:center;'><label type='label'> 3RD YEAR </label></td>
                    </tr>
                </tbody>

            </table>
        

                
        </div>


</section>