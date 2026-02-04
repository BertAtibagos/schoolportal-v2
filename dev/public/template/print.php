<style>
    body, * {
        font-size: 13px;
    }

    .text-center {
        text-align: center;
    }

    .text-end {
        text-align: end;
    }

    .value, tfoot tr td {
        font-weight: bold;
    }

    table th {
        text-align: center;
    }

    table td {
        text-align: left;
    }

    .table-container table tbody td {
        padding-inline: 1rem;
        vertical-align: top;
    }

    #table_sign {
        width: 100%;
        margin-block: 50px;
    }

    #table_sign td {
        text-align: center;
        width: 33%;
    }
</style>
<table id='school_info'>
    <tr>
        <td>
            <img src='https://schoolportal.fcpc.edu.ph/images/FCPC LOGO.jpg' alt='FCPC_logo' class='img-fluid' width='90' height='80'>
        </td>
        <td style='width: 65%; padding-left: 10px;'>
            <b> First City Providential College </b><br>
            Brgy. Narra, Francisco Homes Subd., City of San Jose Del Monte, <br>
            Bulacan, Philippines <br>
            (044) 815-6814
        </td>
        <td>
            <b> Report of Grades </b><br>
            Academic Year: {{year}}<br>
            {{level}}<br>
            {{period}}<br>
            Print Date: {{current_date}}
        </td>
    </tr>
</table>
<br>
<hr>
<br>
<table>
    <tr class='student_info'>
        <td>
            Student ID:
        </td>
        <td>
            <p id='stud_idno' style='padding-left: 2.5rem;'>{{idno}}</p>
        </td>
    </tr>
    <tr class='student_info'>
        <td>
            Name:
        </td>
        <td>
            <p id='full_name' style='padding-left: 2.5rem;'>{{name}}</p>
        </td>
    </tr>
    <tr class='student_info'>
        <td>
            Course:
        </td>
        <td>
            <p id='acad_course' style='padding-left: 2.5rem;'>{{crse}}</p>
        </td>
    </tr>
    <tr class='student_info'>
        <td>
            Year Level:
        </td>
        <td>
            <p id='acad_yrlvl' style='padding-left: 2.5rem;'>{{yrlvl}}</p>
        </td>
    </tr>
</table>
<hr>
<div class="table-container">
    {{grade_table}}
</div>
<hr>
<table id='table_sign'>
    <tr>
        <td>
            Prepared By:
        </td>
        <td></td>
        <td>
            Approved By:
        </td>
    </tr>
    <tr style='height:25px;'></tr>
    <tr>
        <td>
            Frances Jomalyn S. Anastacio, LPT
        </td>
        <td></td>
        <td>
            Echel Simon-Antero, PhD
        </td>
    </tr>
    <tr>
        <td>
            Assistant Registrar
        </td>
        <td></td>
        <td>
            Registrar
        </td>
    </tr>
</table>
<hr>
<h3>{{level}} Grading System</h3>
{{equiv_container}}

<script>
    window.onafterprint = function() { window.close(); };
    window.onload = function() { window.focus(); window.print(); };
</script>