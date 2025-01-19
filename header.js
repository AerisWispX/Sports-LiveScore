
        // Update the navigation on page load
document.addEventListener('DOMContentLoaded', () => {
  const currentPage = window.location.pathname.split('/').pop().replace('.php', '');
  updateNavigation(currentPage);
});

// Update the navigation on link click
const topHeaderLinks = document.querySelectorAll('.top-header .nav-item');
topHeaderLinks.forEach(link => {
  link.addEventListener('click', () => {
    const currentPage = link.getAttribute('data-page');
    updateNavigation(currentPage);
  });
});

const sidebarLinks = document.querySelectorAll('.sidebar .nav-item');
sidebarLinks.forEach(link => {
  link.addEventListener('click', () => {
    const currentPage = link.getAttribute('data-page');
    updateNavigation(currentPage);
    toggleSidebar();
  });
});

const bottomNavLinks = document.querySelectorAll('.bottom-nav .nav-item');
bottomNavLinks.forEach(link => {
  link.addEventListener('click', () => {
    const currentPage = link.getAttribute('data-page');
    updateNavigation(currentPage);
  });
});

function updateNavigation(currentPage) {
  // Update top header navigation active state
  const topHeaderLinks = document.querySelectorAll('.top-header .nav-item');
  topHeaderLinks.forEach(link => {
    if (link.getAttribute('data-page') === currentPage) {
      link.classList.add('active');
    } else {
      link.classList.remove('active');
    }
  });

  // Update sidebar navigation active state
  const sidebarLinks = document.querySelectorAll('.sidebar .nav-item');
  sidebarLinks.forEach(link => {
    if (link.getAttribute('data-page') === currentPage) {
      link.classList.add('active');
    } else {
      link.classList.remove('active');
    }
  });

  // Update bottom navigation active state
  const bottomNavLinks = document.querySelectorAll('.bottom-nav .nav-item');
  bottomNavLinks.forEach(link => {
    if (link.getAttribute('data-page') === currentPage) {
      link.classList.add('active');
    } else {
      link.classList.remove('active');
    }
  });

  // Update the body class to indicate the current page
  document.body.className = `${currentPage}-page`;
}

function toggleSidebar() {
  const sidebar = document.querySelector('.sidebar');
  sidebar.classList.toggle('open');
}
        
    