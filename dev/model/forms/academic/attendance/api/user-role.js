export async function userRole() {
    try{
        const req = await fetch(`controller/student.php`,{
            method: "POST",
            headers: {"Content-Type": "application/x-www-form-urlencoded"},
            body: new URLSearchParams({
                type: "GET_USER_ROLE"
            })
        });

        const res = await req.json();
        return res;
    }catch(error){
        console.error("Failed to fetch user role:", error);
    }
}