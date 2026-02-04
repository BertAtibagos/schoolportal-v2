export async function buildDropdown(url, id, data = {}) {
    const params = new URLSearchParams(data);
    const response = await fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: params.toString()
    });

    if (!response.ok) {
        throw new Error('Network Error!');
    }

    const html = await response.text();

    // ✅ Set the innerHTML of the select tag here, fetch return is html code of options
    document.getElementById(id).innerHTML = html;
    
    return document.getElementById(id).value;
}

export async function fetchData(url, data) {
    const params = new URLSearchParams(data);
    const response = await fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: params.toString()
    });

    if (!response.ok) {
        throw new Error('Network Error!');
    }
    
    const raw = await response.text();
    try {
        return JSON.parse(raw); // try parsing JSON
    } catch {
        return raw; // fallback to plain text
    }
}


