export async function subjects(){
    try{
        const req = await fetch(`controller/student.php`,{
            method: "POST",
            headers: {"Content-Type": "application/x-www-form-urlencoded"},
            body: new URLSearchParams({
                type: "GET_SUBJECT_LIST"
            })
        });

        const res = await req.json();
        return res;
    }catch(error){
        console.error("Failed to fetch subject list:", error);
    }
}