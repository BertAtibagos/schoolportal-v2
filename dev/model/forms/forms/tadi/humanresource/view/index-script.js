document.getElementById('perCutoffByDate').addEventListener('change', (e)=>{
    const selectValue = e.target.value;

    if(selectValue == "date"){
        document.querySelectorAll('.date-search').forEach(select =>{
            select.classList.remove('hide');
        })
    }else{
        document.querySelectorAll('.date-search').forEach(select =>{
            select.classList.add('hide');
        })
    }
})

document.getElementById('byAllNameDept').addEventListener('change', (e)=>{
    const selectValue = e.target.value;

    if(selectValue == "byName"){
        document.querySelector('.name-search').classList.remove('hide');
        document.querySelector('.dept-select').classList.add('hide');
    }
    else if(selectValue == "byDept"){
        document.querySelector('.dept-select').classList.remove('hide');
        document.querySelector('.name-search').classList.add('hide');
    }
    else{
        document.querySelector('.name-search').classList.add('hide');
        document.querySelector('.dept-select').classList.add('hide');
    }
})

document.getElementById('filterMode').addEventListener('change', (e)=>{
    const selectValue = e.target.value;

    if(selectValue == 'summary'){
        document.getElementById('byAllNameDept').classList.add('hide');
        document.querySelector('.name-search').classList.add('hide');
        document.querySelector('.dept-select').classList.remove('hide');
        document.querySelector('.cutoff-search').classList.remove('hide');
        document.querySelectorAll('.acad-lvl').forEach(acadFld => {
            acadFld.classList.add('hide');
        });
    }else if(selectValue == 'tabulation'){
        document.querySelector('.cutoff-search').classList.add('hide');
        document.querySelector('.dept-select').classList.add('hide');
        document.getElementById('byAllNameDept').classList.remove('hide');
        document.querySelectorAll('.acad-lvl').forEach(acadFld => {
            acadFld.classList.remove('hide');
        });
        GET_ACADEMICLEVEL();
    }else{
        document.getElementById('byAllNameDept').classList.remove('hide');
        document.querySelector('.dept-select').classList.add('hide');
        document.querySelector('.cutoff-search').classList.remove('hide');
        document.querySelectorAll('.acad-lvl').forEach(acadFld => {
            acadFld.classList.add('hide');
        });
    }
});