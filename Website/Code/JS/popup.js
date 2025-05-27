document.addEventListener("DOMContentLoaded", function () {
    /***********************************
     * 1) Show All Tables (Website Mode)
     ***********************************/
    function showAllTables() {
        document.querySelectorAll(".news-table").forEach((table) => {
            table.style.display = "table";
        });
    }

    /***********************************
     * 2) Hide All Tables (Extension Mode)
     ***********************************/
    function hideAllTables() {
        document.querySelectorAll(".news-table").forEach((table) => {
            table.style.display = "none";
        });
    }

    /***********************************
     * 3) Load Ratings from localStorage
     ***********************************/
    function loadRatings() {
        document.querySelectorAll(".community-rating").forEach((input) => {
            let site = input.dataset.site;
            let savedVal = localStorage.getItem("rating_" + site);
            if (savedVal) input.value = savedVal;
        });

        document.querySelectorAll(".professional-rating").forEach((input) => {
            let proSite = input.dataset.site;
            let savedVal = localStorage.getItem("rating_" + proSite);
            if (savedVal) input.value = savedVal;
        });
    }

    /***********************************
     * 4) Save Ratings as user types
     ***********************************/
    function saveRating(event) {
        if (window.location.pathname.includes("popup.php")) return; // prevent on popup

        let el = event.target;
        if (el.classList.contains("community-rating")) {
            localStorage.setItem("rating_" + el.dataset.site, el.value);
        }
        if (el.classList.contains("professional-rating") && !el.readOnly) {
            localStorage.setItem("rating_" + el.dataset.site, el.value);
        }
    }

    /***********************************
     * 5) Detect if this is Running in a Chrome Extension
     ***********************************/
    function isExtension() {
        return typeof chrome !== "undefined" && chrome.tabs;
    }

    /***********************************
     * 6) Adjust UI for Extension Mode or Web
     ***********************************/
    if (isExtension()) {
        setTimeout(() => {
            const signIn = document.getElementById("signIn");
            const signUp = document.getElementById("signup");
    
            if (signIn) signIn.style.display = "none";
            if (signUp) signUp.style.display = "none";
            console.log("Extension mode: hiding login forms");
        }, 300);
        
        // continue with rest of extension setup...           

        hideAllTables();

        chrome.tabs.query({ active: true, currentWindow: true }, function (tabs) {
            let url = tabs[0].url || "";

            if (url.includes("nbcnews.com")) {
                document.getElementById("nbcTable").style.display = "table";
            } else if (url.includes("foxnews.com")) {
                document.getElementById("foxTable").style.display = "table";
            } else if (url.includes("cnn.com")) {
                document.getElementById("cnnTable").style.display = "table";
            } else if (url.includes("wsj.com")) {
                document.getElementById("wsjTable").style.display = "table";
            } else if (url.includes("nytimes.com")) {
                document.getElementById("nytTable").style.display = "table";
            } else if (url.includes("cbsnews.com")) {
                document.getElementById("cbsTable").style.display = "table";
            } else {
                document.body.innerHTML = "<p style='text-align:center;'>This site is not supported.</p>";
            }

            loadRatings();
        });
    } else {
        // Not extension — it's website
        hideAllTables();
        loadRatings();

        const toggleBtn = document.createElement("button");
        toggleBtn.textContent = "Show News Tables";
        toggleBtn.style.position = "fixed";
        toggleBtn.style.bottom = "20px";
        toggleBtn.style.right = "20px";
        toggleBtn.style.padding = "10px 15px";
        toggleBtn.style.backgroundColor = "#162938";
        toggleBtn.style.color = "white";
        toggleBtn.style.border = "none";
        toggleBtn.style.borderRadius = "6px";
        toggleBtn.style.cursor = "pointer";
        toggleBtn.style.zIndex = "1000";

        document.body.appendChild(toggleBtn);

        let tablesVisible = false;

        toggleBtn.addEventListener("click", () => {
            tablesVisible = !tablesVisible;
            document.querySelectorAll(".news-table").forEach((table) => {
                table.style.display = tablesVisible ? "table" : "none";
            });
            toggleBtn.textContent = tablesVisible ? "Hide News Tables" : "Show News Tables";
        });
    }

    /***********************************
     * 7) Watch for Input Changes (skip if on popup)
     ***********************************/
    if (!window.location.pathname.includes("popup.php")) {
        document.addEventListener("input", saveRating);
    }

    /***********************************
     * 8) Switch between Login & Register
     ***********************************/
    const signUpButton = document.getElementById('signUpButton');
    const signInButton = document.getElementById('signInButton');
    const signInForm = document.getElementById('signIn');
    const signUpForm = document.getElementById('signup');

    if (signUpButton && signInButton && signInForm && signUpForm) {
        signUpButton.addEventListener('click', function () {
            signInForm.style.display = "none";
            signUpForm.style.display = "block";
        });

        signInButton.addEventListener('click', function () {
            signInForm.style.display = "block";
            signUpForm.style.display = "none";
        });
    }

    // Step 9: Star Rating Logic for Both Logged In and Logged Out
    document.querySelectorAll('.star-rating').forEach(container => {
        const site = container.dataset.site;
        const type = container.dataset.type;
        const isReadOnly = container.dataset.readonly === "true";

        const maxStars = 5;
        let currentRating = 0;

        const stars = [];

        for (let i = 1; i <= maxStars; i++) {
            const star = document.createElement('i');
            star.classList.add('fa', 'fa-star');

            if (!isReadOnly) {
                star.addEventListener('click', () => {
                    currentRating = i;
                    updateStars();
                    saveRating();
                });
            }

            stars.push(star);
            container.appendChild(star);
        }

        fetch(`get_${type}_rating.php?site=${site}`)
            .then(res => res.text())
            .then(rating => {
                currentRating = parseFloat(rating) || 0;
                updateStars();
            });

        function updateStars() {
            stars.forEach((star, index) => {
                star.classList.toggle('filled', index < currentRating);
            });
        }

        function saveRating() {
            if (isExtension()) {
                localStorage.setItem(`rating_${site}`, currentRating);
            } else {
                if (type === 'community') {
                    fetch('update_rating.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: `site=${site}&rating=${currentRating}`
                    }).then(res => res.text()).then(() => {
                        refreshOverall(site); // <<<<<<<<<< ADD THIS
                    });
                } else if (!isReadOnly) {
                    fetch('update_professional_rating.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: `site=${site}&rating=${currentRating}`
                    }).then(res => res.text());
                }
            }
        }        
    });

document.querySelectorAll('.overall-stars').forEach(container => {
    const site = container.closest('table').querySelector('.star-rating[data-site]').dataset.site;

    Promise.all([
        fetch(`get_community_rating.php?site=${site}`).then(res => res.text()),
        fetch(`get_professional_rating.php?site=${site}`).then(res => res.text())
    ])
    .then(([community, professional]) => {
        const comm = parseFloat(community) || 0;
        const prof = parseFloat(professional) || 0;
        const avg = (comm && prof) ? ((comm + prof) / 2) : (comm || prof || 0);

        container.innerHTML = '';

        const maxStars = 5;
        const fullStars = Math.floor(avg);
        const halfStar = (avg % 1) >= 0.25 && (avg % 1) < 0.75;
        const roundUp = (avg % 1) >= 0.75;

        for (let i = 1; i <= maxStars; i++) {
            const star = document.createElement('i');

            if (i <= fullStars) {
                star.className = 'fas fa-star';
            } else if (i === fullStars + 1 && halfStar) {
                star.className = 'fas fa-star-half-alt';
            } else if (i <= fullStars + 1 && roundUp) {
                star.className = 'fas fa-star';
            } else {
                star.className = 'far fa-star';
            }

            container.appendChild(star);
        }

        const score = document.createElement('span');
        score.textContent = ` (${avg.toFixed(1)})`;
        score.style.marginLeft = '6px';
        container.appendChild(score);
    });
});
function refreshOverall(site) {
    const overallContainers = document.querySelectorAll(`.overall-stars[data-site="${site}"]`);
    
    overallContainers.forEach(container => {
        Promise.all([
            fetch(`get_community_rating.php?site=${site}`).then(res => res.text()),
            fetch(`get_professional_rating.php?site=${site}`).then(res => res.text())
        ])
        .then(([community, professional]) => {
            const comm = parseFloat(community) || 0;
            const prof = parseFloat(professional) || 0;
            const avg = (comm && prof) ? ((comm + prof) / 2) : (comm || prof || 0);

            container.innerHTML = '';

            const maxStars = 5;
            const fullStars = Math.floor(avg);
            const halfStar = (avg % 1) >= 0.25 && (avg % 1) < 0.75;
            const roundUp = (avg % 1) >= 0.75;

            for (let i = 1; i <= maxStars; i++) {
                const star = document.createElement('i');

                if (i <= fullStars) {
                    star.className = 'fas fa-star';
                } else if (i === fullStars + 1 && halfStar) {
                    star.className = 'fas fa-star-half-alt';
                } else if (i <= fullStars + 1 && roundUp) {
                    star.className = 'fas fa-star';
                } else {
                    star.className = 'far fa-star';
                }

                container.appendChild(star);
            }

            const score = document.createElement('span');
            score.textContent = ` (${avg.toFixed(1)})`;
            score.style.marginLeft = '6px';
            container.appendChild(score);
        });
    });
}


});
