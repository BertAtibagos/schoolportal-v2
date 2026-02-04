<?php
    function dropdownModule(array $data, $size) : string {
        $options = '';
        if(isset($_SESSION['STUDENT']['ID'])){
            $options .= "<option value='student'>STUDENT</option>";
        }
        
        if(isset($_SESSION['EMPLOYEE']['ID'])){
            $options .= "<option value='instructor'>INSTRUCTOR</option>";
        }
        $size_list = [
            "small" => '-sm',
            "medium" => '',
            "large" => '-lg',
        ];

        $size = $size_list[strtolower($size)] ?? '';

        // Predefined HTML for each dropdown/input
        $elements = [
            'type' => "
                <div class='col-lg-2 mb-2'>
                    <select id='acadtype' name='acadtype' class='form-select form-select$size'>
                        $options
                    </select>
                </div>",
            'level' => "
                <div class='col-lg-2 mb-2'>
                    <select id='acadlevel' name='acadlevel' class='form-select form-select$size'>
                    </select>
                </div>",
            'year' => "
                <div class='col-lg-2 mb-2'>
                    <select id='acadyear' name='acadyear' class='form-select form-select$size'>
                    </select>
                </div>",
            'period' => "
                <div class='col-lg-2 mb-2'>
                    <select id='acadperiod' name='acadperiod' class='form-select form-select$size'>
                    </select>
                </div>",
            'yearlevel' => "
                <div class='col-lg-2 mb-2'>
                    <select id='acadyearlevel' name='acadyearlevel' class='form-select form-select$size'>
                    </select>
                </div>",
            'course' => "
                <div class='col-lg-6 mb-2'>
                    <select id='acadcourse' name='acadcourse' class='form-select form-select$size'>
                    </select>
                </div>",
            'section' => "
                <div class='col-lg-2 mb-2'>
                    <select id='acadsection' name='acadsection' class='form-select form-select$size'>
                    </select>
                </div>",
            'search' => "
                <div class='col-lg-4 mb-2'>
                    <input type='text' id='acadsearchtext' name='acadsearchtext' class='form-control form-control$size' placeholder='Search'>
                </div>",
            'search_type' => "
                <div class='col-lg-2 mb-2'>
                    <select id='acadsearchtype' name='acadsearchtype' class='form-select form-select$size'>
                        <option value='lastname'>Last Name</option>
                        <option value='firstname'>First Name</option>
                    </select>
                </div>"
        ];

        // Search button
        $searchButton = "
            <div class='col-lg-2 mb-2'>
                <button id='btnSearch' name='btnSearch' class='btn btn-primary btn$size'> Search </button>
            </div>";

        $html = "<div>";

        // Row definitions
        $rows = [
            ['type'],
            ['level','year','period','yearlevel'],
            ['course','section'],
            ['search','search_type']
        ];

        foreach ($rows as $row) {
            $html .= "<div class='row'>";
            foreach ($row as $item) {
                if (($key = array_search($item, $data)) !== false) {
                    $html .= $elements[$item];
                    unset($data[$key]); // remove consumed item
                }
            }

            // Check if valid items remain
            $remainingValid = array_intersect($data, array_keys($elements));
            if (empty($remainingValid)) {
                $html .= $searchButton;
                $html .= "</div>";
                break;
            }

            $html .= "</div>";
        }

        $html .= "</div> <hr>";
        return $html;
    }

    /**
     * Builds HTML <option> tags for a <select> element using the same $data format as the table.
     * Uses the first column as value and second column as label.
     *
     * @param array $data The query result (array of associative arrays)
     * @return string The generated <option> tags
     */
    function populateDropdown(array $data){
        if (empty($data)) {
            echo '<option value="">NONE</option>';
        }

        // Get first two column names
        $columns = array_keys($data[0]);
        $valueKey = $columns[0];
        $labelKey = $columns[1];

        $html = '';
        foreach ($data as $row) {
            $value = htmlspecialchars($row[$valueKey]);
            $label = htmlspecialchars($row[$labelKey]);
            $html .= "<option value='$value'>$label</option>";
        }

        echo $html;
    }
?>
