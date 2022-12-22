//ENV
let workItems = 0 + preloadedWorkItems
refreshWorkHeader()
let totalWorkIterations = 0
let eduItems = 0 + preloadedEduItems
refreshEduHeader()
let totalEduIterations = 0
let languageItems = 0 + preloadedLanguageItems
refreshLanguageHeader()
let totalLanguageIterations = 0

//REMOVE WORK BUTTON FOR PRELOADED ITEMS
for (let i = 1; i <= preloadedWorkItems; i++) {
    document.getElementById('work-remove-' + i).addEventListener("click", event => {
        document.getElementById(event.target.getAttribute('data-id')).remove()
        document.getElementById('work-size').value = workItems
        workItems--
        preloadedWorkItems--
        refreshWorkHeader()
        document.getElementById('total-work-size').value = totalWorkIterations
        document.getElementById('preloaded-work-size').value = preloadedWorkItems
    })

    document.getElementById('work-job-isActive-' + i).addEventListener("click", event => {
        if (event.target.checked) {
            document.getElementById('work-job-until-div-' + event.target.getAttribute('data-key')).style.display = 'none'
        } else {
            document.getElementById('work-job-until-div-' + event.target.getAttribute('data-key')).style.display = 'flex'
        }
    })
}

//REMOVE EDUCATION BUTTON FOR PRELOADED ITEMS

for (let i = 1; i <= preloadedEduItems; i++) {
    document.getElementById('edu-remove-' + i).addEventListener("click", event => {
        document.getElementById(event.target.getAttribute('data-id')).remove()
        document.getElementById('edu-size').value = eduItems
        eduItems--
        preloadedEduItems--
        refreshEduHeader()
        document.getElementById('total-edu-size').value = totalEduIterations
        document.getElementById('preloaded-edu-size').value = preloadedEduItems
    })
}

//REMOVE LANGUAGE BUTTON FOR PRELOADED ITEMS

for (let i = 1; i <= preloadedLanguageItems; i++) {
    document.getElementById('language-remove-' + i).addEventListener("click", event => {
        document.getElementById(event.target.getAttribute('data-id')).remove()
        document.getElementById('language-size').value = languageItems
        languageItems--
        preloadedLanguageItems--
        refreshLanguageHeader()
        document.getElementById('total-language-size').value = totalLanguageIterations
        document.getElementById('preloaded-language-size').value = preloadedLanguageItems
    })
}


//ADD WORK BUTTON
let addWorkButton = document.getElementById('addWorkButton')
addWorkButton.addEventListener("click", event => {
    workItems++
    totalWorkIterations++
    document.getElementById('work').insertAdjacentHTML('beforeend', `
        <div id="1` + totalWorkIterations + `">
            <div class="inline">
                <h5 class="my-4" style="color: red;">Remove</h5>
                <div class="remove" id="01` + totalWorkIterations + `" data-id="1` + totalWorkIterations + `">-</div>
            </div>
        
            <div class="form-group mb-3 row">
                <label for="job-title12" class="col-md-5 col-form-label">Job title</label>
                <div class="col-md-7">
                    <input type="text" class="form-control form-control-lg" id="job-title12" name="job-title-` + totalWorkIterations + `" required>
                </div>
            </div>
            <div class="form-group mb-3 row"><label for="company13" class="col-md-5 col-form-label">Company</label>
                <div class="col-md-7"><input type="text" class="form-control form-control-lg" id="company13" name="company-` + totalWorkIterations + `" required></div>
            </div>
            <div class="form-group mb-3 row"><label for="salary14" class="col-md-5 col-form-label">Salary</label>
                <div class="col-md-7">
                    <input type="text" class="form-control form-control-lg" id="salary14" name="salary-` + totalWorkIterations + `" required>
                    <small class="form-text text-muted"> per month</small>
                </div>
            </div>
            <div class="form-group mb-3 row"><label for="job-description16" class="col-md-5 col-form-label">Job description</label>
                <div class="col-md-7">
                    <textarea class="form-control form-control-lg" id="job-description16" name="job-description-` + totalWorkIterations + `" required></textarea></div>
                </div>
            <div class="inline date">
                <label for="from">Started on</label>
                <input type="date" name="job-from-` + totalWorkIterations + `" id="job-from-` + totalWorkIterations + `">
            </div>
            <div class="inline date" id="job-until-div-` + totalWorkIterations + `">
                <label for="dateofbirth">Worked until</label>
                <input type="date" name="job-until-` + totalWorkIterations + `" id="job-until-` + totalWorkIterations + `">
            </div>
            <div class="inline switch">
                <h6>I am currently working here</h6>
                <label class="toggle">
                  <input class="toggle-checkbox" type="checkbox" name="job-isActive-` + totalWorkIterations + `" id="job-isActive-` + totalWorkIterations + `" data-key="` + totalWorkIterations + `">
                  <div class="toggle-switch"></div>
                </label>
            </div>
        </div>
        `)

    //REMOVE WORK BUTTON
    document.getElementById('01' + totalWorkIterations).addEventListener("click", event => {
        document.getElementById(event.target.getAttribute('data-id')).remove()
        document.getElementById('work-size').value = workItems
        workItems--
        refreshWorkHeader()
        document.getElementById('total-work-size').value = totalWorkIterations
    })
    document.getElementById('total-work-size').value = totalWorkIterations
    document.getElementById('work-size').value = workItems
    refreshWorkHeader()

    document.getElementById('job-isActive-' + totalWorkIterations).addEventListener("click", event => {
        if (event.target.checked) {
            document.getElementById('job-until-div-' + event.target.getAttribute('data-key')).style.display = 'none'
        } else {
            document.getElementById('job-until-div-' + event.target.getAttribute('data-key')).style.display = 'flex'
        }
    })
})

//ADD EDUCATION BUTTON
let addEduButton = document.getElementById('addEduButton')
addEduButton.addEventListener("click", event => {
    eduItems++
    totalEduIterations++
    document.getElementById('edu').insertAdjacentHTML('beforeend', `
        <div id="2` + totalEduIterations + `">
            <div class="inline">
                <h3 class="my-4" style="color: red;">Remove</h3>
                <div class="remove" id="02` + totalEduIterations + `" data-id="2` + totalEduIterations + `">-</div>
            </div>
            <div class="form-group mb-3 row">
                <label for="institution19" class="col-md-5 col-form-label">Institution</label>
                <div class="col-md-7">
                    <input type="text" class="form-control form-control-lg" id="institution19" name="institution-` + totalEduIterations + `" required></div>
                </div>
                <div class="form-group mb-3 row">
                    <label for="location20" class="col-md-5 col-form-label">Location</label>
                    <div class="col-md-7">
                        <input type="text" class="form-control form-control-lg" id="location20" name="location-` + totalEduIterations + `" required>
                    </div>
                </div>
                <div class="form-group mb-3 row">
                    <label for="profile21" class="col-md-5 col-form-label">Profile</label>
                    <div class="col-md-7">
                        <input type="text" class="form-control form-control-lg" id="profile21" name="profile-` + totalEduIterations + `" required>
                        <small class="form-text text-muted"> ex: Computer Science</small>
                    </div>
                </div>
                <div class="form-group mb-3 row"><label for="degree23" class="col-md-5 col-form-label">Degree</label>
                    <div class="col-md-7"><select class="form-select custom-select custom-select-lg" id="degree23" name="degree-` + totalEduIterations + `">
                            <option>Associate</option>
                            <option> Bachelor's</option>
                            <option> Master's</option>
                            <option> Doctoral</option>
                            </select>
                    </div>
                </div>
                <div class="inline date">
                    <label for="from">Graduated on</label>
                    <input type="date" name="graduated-on-` + totalEduIterations + `" id="graduated-on-` + totalEduIterations + `">
                </div>
            </div>
        `)

    //REMOVE EDUCATION BUTTON
    document.getElementById('02' + totalEduIterations).addEventListener("click", event => {
        document.getElementById(event.target.getAttribute('data-id')).remove()
        document.getElementById('edu-size').value = eduItems
        eduItems--
        document.getElementById('total-edu-size').value = totalEduIterations
        refreshEduHeader()
    })
    document.getElementById('total-edu-size').value = totalEduIterations
    document.getElementById('edu-size').value = eduItems
    refreshEduHeader()
})

//ADD LANGUAGE BUTTON
let addLanguageButton = document.getElementById('addLanguageButton')
addLanguageButton.addEventListener("click", event => {
    languageItems++
    totalLanguageIterations++
    document.getElementById('language').insertAdjacentHTML('beforeend', `
        <div id="3` + totalLanguageIterations + `">
            <div class="inline">
                <h3 class="my-4" style="color: red;">Remove</h3>
                <div class="remove" id="03` + totalLanguageIterations + `" data-id="3` + totalLanguageIterations + `">-</div>
            </div>
            <div class="form-group mb-3 row">
                <label for="name19" class="col-md-5 col-form-label">Language name</label>
                <div class="col-md-7">
                    <input type="text" class="form-control form-control-lg" id="name19" name="name-` + totalLanguageIterations + `" required></div>
                </div>
                <div class="form-group mb-3 row"><label for="level23" class="col-md-5 col-form-label">Level</label>
                    <div class="col-md-7"><select class="form-select custom-select custom-select-lg" id="level23" name="level-` + totalLanguageIterations + `">
                            <option>Beginner</option>
                            <option>Intermediate</option>
                            <option>Advanced</option>
                            <option>Native</option>
                            </select>
                    </div>
                </div>
            </div>
        `)

    //REMOVE LANGUAGE BUTTON
    document.getElementById('03' + totalLanguageIterations).addEventListener("click", event => {
        document.getElementById(event.target.getAttribute('data-id')).remove()
        document.getElementById('language-size').value = languageItems
        languageItems--
        document.getElementById('total-language-size').value = totalLanguageIterations
        refreshLanguageHeader()
    })
    document.getElementById('total-language-size').value = totalLanguageIterations
    document.getElementById('language-size').value = languageItems
    refreshLanguageHeader()
})

function refreshLanguageHeader() {
    if (languageItems === 0) {
        document.getElementById('languageHeader').innerText = 'Languages'
    } else if (languageItems === 1) {
        document.getElementById('languageHeader').innerText = 'Languages - ' + languageItems + ' item'
    } else {
        document.getElementById('languageHeader').innerText = 'Languages - ' + languageItems + ' items'
    }
}

function refreshEduHeader() {
    if (eduItems === 0) {
        document.getElementById('eduHeader').innerText = 'Education'
    } else if (eduItems === 1) {
        document.getElementById('eduHeader').innerText = 'Education - ' + eduItems + ' item'
    } else {
        document.getElementById('eduHeader').innerText = 'Education - ' + eduItems + ' items'
    }
}

function refreshWorkHeader() {
    if (workItems === 0) {
        document.getElementById('workHeader').innerText = 'Work experience'
    } else if (workItems === 1) {
        document.getElementById('workHeader').innerText = 'Work experience - ' + workItems + ' item'
    } else {
        document.getElementById('workHeader').innerText = 'Work experience - ' + workItems + ' items'
    }
}