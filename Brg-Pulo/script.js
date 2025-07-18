// === SIDEBAR TOGGLE & OVERLAY ===
const sidebarToggle = document.getElementById('sidebarToggle');
const sidebar = document.getElementById('sidebar');
const sidebarOverlay = document.getElementById('sidebarOverlay');

if (sidebarToggle && sidebar) {
  sidebarToggle.addEventListener('click', () => {
    const open = !sidebar.classList.contains('open');
    sidebar.classList.toggle('open', open);
    sidebarToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
    // Overlay
    if (sidebarOverlay) sidebarOverlay.style.display = open ? 'block' : 'none';
    // Focus first nav link if opening
    if (open) {
      setTimeout(() => {
        let firstLink = sidebar.querySelector('nav a');
        if (firstLink) firstLink.focus();
      }, 180);
    }
  });
  // Overlay click closes sidebar
  if (sidebarOverlay) {
    sidebarOverlay.addEventListener('click', () => {
      sidebar.classList.remove('open');
      sidebarToggle.setAttribute('aria-expanded', 'false');
      sidebarOverlay.style.display = 'none';
    });
  }
  // Click outside closes on mobile
  window.addEventListener('click', function(e) {
    if (
      window.innerWidth <= 900 &&
      sidebar.classList.contains('open') &&
      !sidebar.contains(e.target) &&
      !sidebarToggle.contains(e.target) &&
      (!sidebarOverlay || !sidebarOverlay.contains(e.target))
    ) {
      sidebar.classList.remove('open');
      sidebarToggle.setAttribute('aria-expanded', 'false');
      if (sidebarOverlay) sidebarOverlay.style.display = 'none';
    }
  });
}

// === SECTION NAVIGATION & PERSIST ===
const sectionLinks = document.querySelectorAll('.sidebar nav a');
const sections = document.querySelectorAll('.content-section');
function showSectionById(sectionId, updateStorage = true) {
  sections.forEach(sec => sec.style.display = sec.id === sectionId ? 'block' : 'none');
  sectionLinks.forEach(link => {
    link.classList.toggle('active', link.getAttribute('data-section') === sectionId);
  });
  const sectionTitle = document.getElementById(sectionId)?.querySelector('h2')?.textContent;
  if (sectionTitle) document.title = sectionTitle + ' – Employer Dashboard';
  if (updateStorage) localStorage.setItem('activeSection', sectionId);
  document.getElementById('mainContent')?.focus();
}
sectionLinks.forEach(link => {
  link.addEventListener('click', function(e) {
    e.preventDefault();
    const target = this.getAttribute('data-section');
    showSectionById(target);
    // Close sidebar on mobile
    if(window.innerWidth <= 900) {
      sidebar.classList.remove('open');
      if (sidebarOverlay) sidebarOverlay.style.display = 'none';
    }
  });
  link.addEventListener('keydown', function(e) {
    if (e.key === "Enter" || e.key === " ") {
      e.preventDefault();
      this.click();
    }
  });
});
window.addEventListener('DOMContentLoaded', () => {
  let lastSection = localStorage.getItem('activeSection') || 'dashboard';
  showSectionById(lastSection, false);
});

// === IMAGE PREVIEW FUNCTION ===
window.previewImage = function(event) {
  const input = event.target;
  const previewImage = document.getElementById('preview-image');
  if (input.files && input.files[0]) {
    const reader = new FileReader();
    reader.onload = function(e) {
      previewImage.src = e.target.result;
    };
    reader.readAsDataURL(input.files[0]);
  }
};

// === MODAL FUNCTIONALITY FOR "View All" BUTTONS ===
document.querySelectorAll('.view-all-btn').forEach(btn => {
  btn.addEventListener('click', function () {
    const barangay = this.getAttribute('data-barangay');
    const modalId = 'modal-' + barangay.toLowerCase().replace(/\s+/g, '-');
    const modal = document.getElementById(modalId);
    if (modal) modal.style.display = 'block';
  });
});

// === CLOSE MODAL BUTTON ===
document.querySelectorAll('.modal .close, .modal .close-btn').forEach(closeBtn => {
  closeBtn.addEventListener('click', function () {
    this.closest('.modal').style.display = 'none';
  });
});

// === CLOSE MODAL IF CLICK OUTSIDE MODAL CONTENT ===
window.addEventListener('click', function (event) {
  document.querySelectorAll('.modal').forEach(modal => {
    if (event.target === modal) {
      modal.style.display = 'none';
    }
  });
});
