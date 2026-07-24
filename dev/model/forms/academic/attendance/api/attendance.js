const session = window.__SESSION__;

export async function attendanceLog(subjectId, dateStart, dateEnd) {

    let res = [];
    let link = `controller/student.php`;

    if(session.CATEGORY == 'INSTRUCTOR'){
        link = `controller/employee.php`;
    }

     try{
        const params = new URLSearchParams({
            type: "FETCH_ATTENDANCE_LOGS",
        });

        if (subjectId) params.append("subjectId", subjectId);
        if (dateStart) params.append("dateStart", dateStart);
        if (dateEnd) params.append("dateEnd", dateEnd);

        const req = await fetch(link,{
            method: "POST",
            headers: {"Content-Type": "application/x-www-form-urlencoded"},
            body: params
        });

        res = await req.json();
    }catch(error){
        console.error("Failed to fetch attendance logs:", error);
    }
    
    return res;
}