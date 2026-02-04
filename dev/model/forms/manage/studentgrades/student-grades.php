<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="../../../../css/bootstrap-5.2.2/dist/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="../../../../css/bootstrap-5.2.2/font-awesome-4.7.0/css/font-awesome.min.css"/>
    
    <link rel="stylesheet" href="../../../../css/custom/student-grades-style.css">

    <script src="../../../../js/custom/student-grades-js.js"></script>

    <title>Student Grades</title>
</head>
<body>
    <div class="container" style="border-bottom: 2px lightslategray solid">
      <div style="display: flex">
        <h1> Student Grades </h1>
        <!-- <p></p> -->
      </div>

      <hr>
      <br>
      
        <div class=""> 
            <div class="col-md-3">
                <label class="form-label" for=""> ACADEMIC YEAR </label>
                <select id="" name="" class="form-control" required>
                    <option value=''> 2022 - 2023 </option>;
                    <option value=''> 2021 - 2022 </option>;
                </select>                            
            

                <label class="form-label" for="" > PERIOD </label>
                <select id="" name="" class="form-control" required>
                    <option value=''> 1ST SEM </option>;
                    <option value=''> 2ND SEM </option>;
                    <option value=''> SUMMER </option>;
                </select>
            </div>

            <div class="">
                <br>
                <table id='subtbl' class='table table-hover table-responsive table-bordered'>
                    <thead class='table table-primary'>
                        <tr>
                            <th scope='col'> # </th>
                            <th scope='col'> Subject Code </th>
                            <th scope='col' style="width: 35%; text-align: left"> Decsription </th>
                            <th scope='col'> Unit </th>
                            <th scope='col'> Section </th>
                            <th scope='col'> Student List </th>
                            <th scope='col'> Grading Template </th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr>
                            <td><label type='label'> 1 </td>
                            <td><label type='label'> GE101 </td>
                            <td style="text-align: left"><label type='label'> Understanding the Self </td>
                            <td><label type='label'> 3.0 </td>
                            <td><label type='label'> BSN 1A </td>
                            <td><label type='label'><button type='button' class='btn btn-success' onclick="addComp()"> View Class List </button></td>
                            <td><label type='label'><button type='button' class='btn btn-success'><a href="#" class="viewgt">View Grading Template </a></button></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="container" id="tblhid" style="display:none; margin-top: 2%;">
        <h1 class="titlez">Class List</h1>
        <div class="pagination">
            <a href="#" class="active">Prelim >> </a> 
            <a href="#">Midterm >> </a>
            <a href="#">Finals >> </a>
            <a href="#">Reports >></a>
        </div>
        <table id='subtbl1' class='table table-hover table-responsive table-bordered'>
            <thead class='table table-primary'>
                <tr>
                    <th scope='col'> # </th>
                    <th style="width: 50%; text-align: left"> Name </th>
                    <th scope='col'> Gender </th>
                    <th scope='col'> Course </th>
                    <th scope='col'> Year </th>
                    <th scope='col' class='table-success'> Prelim </th>
                </tr>
            </thead>

            <tbody>
                <tr>
                    <td><label type='label'> 1 </td>
                    <td style="text-align: left"><label type='label'> Ibarra, Maria Clara D.</td>
                    <td><label type='label'> Female </td>
                    <td><label type='label'> BSN </td>
                    <td><label type='label'> 1st </td>
                    <td class='grades'><label type='label'>
                        <input type="number" name="prelim" id="prelim">
                    </td>
                </tr>
                <tr>
                    <td><label type='label'> 2 </td>
                    <td style="text-align: left"><label type='label'> Ibarra, Crisostomo R.</td>
                    <td><label type='label'> Male </td>
                    <td><label type='label'> BSN </td>
                    <td><label type='label'> 1st </td>
                    <td class='grades'><label type='label'>
                        <input type="number" name="prelim" id="prelim">
                    </td>
                </tr>
            </tbody>
        </table>

        <table id='subtbl1' class='table table-hover table-responsive table-bordered'>
            <thead class='table table-primary'>
                <tr>
                    <th scope='col'> # </th>
                    <th style="width: 50%; text-align: left"> Name </th>
                    <th scope='col'> Gender </th>
                    <th scope='col'> Course </th>
                    <th scope='col'> Year </th>
                    <th scope='col' class='table-success'> Final Grade </th>
                    <th scope='col' class='table-success'> Equivalent </th>
                    <th scope='col' class='table-success'> Remarks </th>
                </tr>
            </thead>

            <tbody>
                <tr>
                    <td><label type='label'> 1 </td>
                    <td style="text-align: left"><label type='label'> Ibarra, Maria Clara D.</td>
                    <td><label type='label'> Female </td>
                    <td><label type='label'> BSN </td>
                    <td><label type='label'> 1st </td>
                    <td><label type='label'> 90.44 </td>
                    <td><label type='label'> 1.50 </td>
                    <td>
                        <select id="" name="" class="form-control" required>
                            <option value="" selected="selected" hidden="hidden"></option>
                            <option value=''> Passed </option>;
                            <option value=''> Failed </option>;
                            <option value=''> In Progress </option>;
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><label type='label'> 2 </td>
                    <td style="text-align: left"><label type='label'> Ibarra, Crisostomo R.</td>
                    <td><label type='label'> Male </td>
                    <td><label type='label'> BSN </td>
                    <td><label type='label'> 1st </td>
                    <td><label type='label'> 90.44 </td>
                    <td><label type='label'> 1.50 </td>
                    <td>
                        <select id="" name="" class="form-control" required>
                            <option value="" selected="selected" hidden="hidden"></option>
                            <option value=''> Passed </option>;
                            <option value=''> Failed </option>;
                            <option value=''> In Progress </option>;
                        </select>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    
</body>
</html>