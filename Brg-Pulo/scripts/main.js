// ===== Sidebar Navigation Toggle =====
function showSection(id, event) {
    // Hide all content sections
    document.querySelectorAll('.content-section').forEach(section => {
        section.style.display = 'none';
    });

    // Remove active class from all nav links
    document.querySelectorAll('.sidebar nav a').forEach(link => {
        link.classList.remove('active');
    });

    // Show the selected section
    document.getElementById(id).style.display = 'block';

    // Highlight the active link
    if (event && event.currentTarget) {
        event.currentTarget.classList.add('active');
    }
}

// Show the dashboard section by default when page loads
window.addEventListener('DOMContentLoaded', () => {
    // Sidebar section show
    if (document.querySelector('.sidebar nav a.active')) {
        showSection('dashboard', { currentTarget: document.querySelector('.sidebar nav a.active') });
    }
});

// ====== Preview Selected Image Before Upload ======
function previewImage(event) {
    const input = event.target;
    const previewImage = document.getElementById('preview-image');

    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            previewImage.src = e.target.result;
        };
        reader.readAsDataURL(input.files[0]);
    }
}

// ====== Modal Functionality for "View All" Buttons (Sidebar Modals) ======
document.querySelectorAll('.view-all-btn').forEach(btn => {
    btn.addEventListener('click', function () {
        const barangay = this.getAttribute('data-barangay');
        const modalId = 'modal-' + barangay.toLowerCase().replace(/\s+/g, '-');
        const modal = document.getElementById(modalId);
        if (modal) modal.style.display = 'block';
    });
});

// ====== Close Modal when the close (×) is clicked ======
document.querySelectorAll('.modal .close').forEach(closeBtn => {
    closeBtn.addEventListener('click', function () {
        this.closest('.modal').style.display = 'none';
    });
});

// ====== Close Modal if user clicks outside the modal content ======
window.addEventListener('click', function (event) {
    if (event.target.classList && event.target.classList.contains('modal')) {
        event.target.style.display = 'none';
    }
});

// ===== Responsive Navbar Hamburger Toggle =====
const navToggle = document.getElementById('navToggle');
const navLinks = document.getElementById('navLinks');
if (navToggle && navLinks) {
    navToggle.onclick = function() {
        navLinks.classList.toggle('open');
        navToggle.classList.toggle('active');
    };
    // Auto-close mobile menu when link is clicked
    document.querySelectorAll('#navLinks a').forEach(link => {
        link.addEventListener('click', () => {
            if (window.innerWidth <= 850) navLinks.classList.remove('open');
        });
    });
}

// ===== General Modal Logic for Service Request (Global Scope) =====
function openModal(serviceName) {
    if (document.getElementById('formTitle'))
        document.getElementById('formTitle').innerText = serviceName + " Request Form";
    if (document.getElementById('formModal'))
        document.getElementById('formModal').style.display = 'flex';
}
function closeModal() {
    if (document.getElementById('formModal'))
        document.getElementById('formModal').style.display = 'none';
}
// Make openModal and closeModal global so HTML onclick can access
window.openModal = openModal;
window.closeModal = closeModal;
