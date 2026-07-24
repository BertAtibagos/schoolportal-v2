import { subjects } from "../api/subjects.js";

export async function subjectFilterComponent(){

    const res = await subjects();

    const html = `<div class="col-md-4">
                    <label class="form-label">Subject</label>
                    <select class="form-select" id="subjectSelect">
                        <option value="">All Subjects</option>
                        ${res.map(subj => `
                            <option value="${subj.subj_id}">${subj.subj_desc}</option>
                        `).join("")}
                    </select>
                </div>`

    return html;
}