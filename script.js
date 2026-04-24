const campaigns = [
    { 
        id: 1, 
        title: "Educate the Girl Child", 
        desc: "Providing secondary education funds for girls.", 
        purpose: "Our mission is to break the cycle of poverty through education.",
        longDesc: "• Providing full scholarships for secondary school tuition.<br>• Supplying essential learning kits, books, and uniforms.<br>• Establishing community mentorship programs for career guidance.",
        goal: 500, reached: 375, color: "#f8bbd0" 
    },
    { 
        id: 2, 
        title: "Tech Mentorship 2024", 
        desc: "Connecting female students with industry leaders.", 
        purpose: "Bridging the gender gap in the technology sector.",
        longDesc: "• One-on-one coding bootcamps with industry experts.<br>• Exclusive networking sessions with Top Tech companies.<br>• Internship placement assistance for final year students.",
        goal: 200, reached: 80, color: "#e1bee7" 
    },
    { 
        id: 3, 
        title: "Sanitary Hygiene Drive", 
        desc: "Distributing hygiene kits in urban slums.", 
        purpose: "Promoting health, dignity, and menstrual awareness.",
        longDesc: "• Distribution of eco-friendly and sustainable hygiene products.<br>• Conducting medical workshops led by female health professionals.<br>• Setting up local disposal units for better community sanitation.",
        goal: 1000, reached: 900, color: "#fce4ec" 
    },
    { 
        id: 4, 
        title: "Start-Up Seed Fund", 
        desc: "Providing interest-free loans to female founders.", 
        purpose: "Empowering women to become independent job creators.",
        longDesc: "• Offering zero-interest micro-loans for initial setup costs.<br>• Providing legal assistance for business registration.<br>• Access to a network of investors and marketing consultants.",
        goal: 50, reached: 12, color: "#d1c4e9" 
    }
];

let currentUser = {
    name: (typeof loggedInUser !== 'undefined') ? (loggedInUser.user_name || '') : '',
    age: (typeof loggedInUser !== 'undefined') ? (loggedInUser.user_age || '') : '',
    gender: (typeof loggedInUser !== 'undefined') ? (loggedInUser.user_gender || '') : '',
    enrolled: [] 
};

window.onload = function() {
    renderCampaigns();
};

function showDashboard() {
    document.getElementById('dashboard-content').style.display = 'block';
    document.getElementById('profile-view').style.display = 'none';
}

function showProfile() {
    const nameElem = document.getElementById('prof-name');
    const ageElem = document.getElementById('prof-age');
    const genderElem = document.getElementById('prof-gender');

    if (nameElem && ageElem && genderElem) {
        nameElem.innerText = currentUser.name;
        ageElem.innerText = currentUser.age;
        genderElem.innerText = currentUser.gender;
    }

    document.getElementById('dashboard-content').style.display = 'none';
    document.getElementById('profile-view').style.display = 'block';
    
    fetch('get_enrollments.php')
    .then(response => response.json())
    .then(enrolledCampaigns => {
        const list = document.getElementById('enrolled-list');
        if(!list) return;

        if(enrolledCampaigns.length === 0) {
            list.innerHTML = "<p>You haven't joined any campaigns yet.</p>";
        } else {
            list.innerHTML = enrolledCampaigns.map(campTitle => {
                return `<div class="enrolled-item"><strong>${campTitle}</strong> - Status: Active Participation</div>`;
            }).join('');
        }
    })
    .catch(err => console.error("Error fetching enrollments:", err));
}

function renderCampaigns() {
    const container = document.getElementById('campaignContainer');
    if (!container) return;

    container.innerHTML = '';

    campaigns.forEach(c => {
        const percent = (c.reached / c.goal) * 100;
        const card = `
            <div class="card" onclick="openModal(${c.id})">
                <div class="card-img" style="background: ${c.color}">${c.title}</div>
                <div class="card-content">
                    <div class="card-title" style="color:#e91e63; font-weight:bold;">${c.title}</div>
                    <p class="card-desc">${c.desc}</p>
                    <div class="progress-container">
                        <div class="progress-bar" style="width: ${percent}%;"></div>
                    </div>
                    <div style="display:flex; justify-content:space-between; font-size:0.8rem; font-weight:bold;">
                        <span>Goal: ${c.goal}</span>
                        <span>${c.reached} Joined</span>
                    </div>
                </div>
            </div>
        `;
        container.innerHTML += card;
    });
}

function openModal(id) {
    const c = campaigns.find(item => item.id === id);
    if(!c) return;

    document.getElementById('modal-title').innerText = c.title;
    
    // We use .innerHTML to make sure the <br> tags render correctly
    const modalPurpose = document.getElementById('modal-purpose');
    modalPurpose.innerHTML = `
        <p style="font-weight: bold; color: #9c27b0; margin-bottom: 12px;">${c.purpose}</p>
        <div style="text-align: left; line-height: 1.6; font-size: 1rem; color: #333; background: #f9f9f9; padding: 15px; border-radius: 8px; border-left: 5px solid #e91e63;">
            ${c.longDesc}
        </div>
    `;
    
    const actionArea = document.getElementById('modal-action-area');
    actionArea.innerHTML = `<button class="btn-support" onclick="joinCampaign(${c.id})">Join This Campaign</button>`;

    document.getElementById('modal-overlay').style.display = 'flex';
}

function closeModal() {
    document.getElementById('modal-overlay').style.display = 'none';
}

function joinCampaign(id) {
    const campaign = campaigns.find(c => c.id === id);
    if(!campaign) return;

    const formData = new FormData();
    formData.append('campaign_name', campaign.title);

    fetch('enroll.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        if (data.trim() === "success") {
            showDonationDemo(campaign);
            campaign.reached += 1;
            renderCampaigns();
        } else if (data.trim() === "exists") {
            alert("Already enrolled!");
        } else {
            alert("Error: " + data);
        }
    })
    .catch(err => console.error("Enrollment error:", err));
}

function showDonationDemo(campaign) {
    const actionArea = document.getElementById('modal-action-area');
    const modalTitle = document.getElementById('modal-title');
    const modalPurpose = document.getElementById('modal-purpose');

    modalTitle.innerText = "Support " + campaign.title;
    modalPurpose.innerHTML = `
        <div style="text-align: center; padding: 10px;">
            <p style="color: #4CAF50; font-weight: bold; font-size: 1.2rem;">✔ Enrollment Successful!</p>
            <p>To support this mission, you can make a demo donation.</p>
            <h4 style="margin: 15px 0; color: #9c27b0;">Amount Required: ₹500</h4>
            <div style="background: #fff; padding: 10px; display: inline-block; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=NariShakti_Donation_${campaign.id}" 
                     alt="Demo QR Code">
            </div>
            <p style="font-size: 0.8rem; color: #777; margin-top: 10px;">(Scan to simulate payment - Lab Demo Only)</p>
        </div>
    `;
    actionArea.innerHTML = `<button class="btn-login" onclick="closeModal()">Complete & Close</button>`;
}
