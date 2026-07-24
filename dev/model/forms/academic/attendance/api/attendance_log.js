export async function attendanceLog(subjectId = null){

    let res = [];
    let url = `controller/server.php`;

     try{
        const req = await fetch(url,{
            method: "POST",
            headers: {"Content-Type": "application/x-www-form-urlencoded"},
            body: new URLSearchParams({
                type: "FETCH_ATTENDANCE_LOGS",
                subjectId: subjectId
            })
        });

        res = await req.json();
    }catch(error){
        console.error("Failed to fetch attendance logs:", error);
    }
    
    return res;
}