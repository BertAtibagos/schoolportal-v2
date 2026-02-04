
<?php
    
    include 'Curriculum-controller.php';

?>

<section id="curriculum">
    <div class="container">
        <br>
        <h1 align="center" style="font-size: 40px; font-family: 'Times New Roman', Times, serif;color: blue;">
            
            <?php
               echo $fetchcurrheader['CRSE_NAME'];
            ?>

        </h1>
        <div align="center" style="font-size: 30px;font-style: normal;">
            <?php
               echo $fetchcurrheader['CURR_NAME'];
            ?>
        </div>
    </div>
    <hr>
    <div class="container" id="table-list">

        <?php

            // FOR GETTING EXISTING 

            $YRLVL = array();
            foreach($fetchcurrsubjects as $regitem)
            {   

                if (!in_array( $regitem['ACAD_YRLVL_NAME'] ,$YRLVL) )
                {
                    array_push($YRLVL, $regitem['ACAD_YRLVL_NAME'] );
                }
            }


            $YRPRD = array();

            foreach($fetchcurrsubjects as $regitem)
            {   

                if (!in_array( $regitem['ACAD_PRD_NAME'] ,$YRPRD) )
                {
                    array_push($YRPRD, $regitem['ACAD_PRD_NAME'] );
                }
            }

            FOR($year_counter = 0; $year_counter < COUNT($YRLVL); $year_counter++)
            {      
                ECHO  "<BR> ";

                //echo "<div align='center' style='font-size: 40px;font-style: normal;'> <b> " . $fetchyrlvl_yrprd[$year_counter]['ACAD_YRLVL_NAME'] . " </b> </div>";

                echo "<div align='center' style='font-size: 40px;font-style: normal;'> <b> " . $YRLVL[$year_counter] . " </b> </div>";


                 
                FOR($period_counter = 0; $period_counter < COUNT($YRPRD); $period_counter++ )
                {   

                    
                    $createTable  = "<table class='table'>";
                    $createTable .= "<tbody>";
                    $createTable .= "      <tr>";
                    $createTable .= "          <td style='text-align:LEFT;' width='350px'><label type='label' class='text-primary'><b> " . $YRPRD[$period_counter] . "</b></label></td>";
                    $createTable .= "      </tr>";
                    $createTable .= "    </tbody>";
                    $createTable .= "</table>";

                    $createTable .= "<table id='regtable' class='table table-responsive table-bordered'>";
                    $createTable .= "    <thead class='table table-primary'>";
                    $createTable .= "        <tr>";
                    $createTable .= "           <th scope='col' style='text-align:center;'>Course Code</th>";
                    $createTable .= "           <th scope='col' style='text-align:center;'>Course Description</th>";
                    $createTable .= "           <th scope='col' style='text-align:center;'>Units.</th>";
                    $createTable .= "           <th scope='col' style='text-align:center;'>Lec.</th>";
                    $createTable .= "           <th scope='col' style='text-align:center;'>Lab</th>";
                    $createTable .= "       </tr>";
                    $createTable .= "   </thead>";

                    $total_units = 0;
                    $createTable .= "   <tbody>";

                    foreach ($fetchcurrsubjects as $regitem)
                    {   
                        if($regitem['ACAD_YRLVL_NAME'] == $YRLVL[$year_counter] && $regitem['ACAD_PRD_NAME'] == $YRPRD[$period_counter])
                        {   
                            $total_units += (int)$regitem['SUBJ_UNIT'];

                            $createTable .= "<tr>";
                            $createTable .= "   <td style='text-align:center;'><label type='label'>" . $regitem['SUBJ_CODE'] . "</label></td>";
                            $createTable .= "   <td style='text-align:center;'><label type='label'>" . $regitem['SUBJ_NAME'] . "</label></td>";
                            $createTable .= "   <td style='text-align:center;'><label type='label'>" . $regitem['SUBJ_UNIT'] . "</label></td>";
                            $createTable .= "   <td style='text-align:center;'><label type='label'>" . $regitem['SUBJ_LEC'] . "</label></td>";
                            $createTable .= "   <td style='text-align:center;'><label type='label'>" . $regitem['SUBJ_LAB'] . "</label></td>";
                            $createTable .= "</tr>";
                        }
                       
                    }

                    $createTable .= "   </tbody>";

                    $createTable .= "   <tfooter>";
                    $createTable .= "   <tr>";
                    $createTable .= "       <td style='text-align:right;' colspan='2' > <B><I> TOTAL </I></B> </td>";
                    $createTable .= "       <td style='text-align:center;'><B><I> ". $total_units ." </I></B></td>";
                    $createTable .= "   </tr>";
                    $createTable .= "   </tfooter>";
                    $createTable .= "</table>";

                    echo  $createTable;
                    // ending tag of PRD
                }
    


                // ending tag of YRLVL
            }

        
        ?>




    </div>
</section>