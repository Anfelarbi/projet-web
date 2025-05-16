/**
 * Aura Deco - Admin Dashboard JavaScript
 * 
 * Contains the functionality specific to the admin dashboard
 */

document.addEventListener('DOMContentLoaded', function() {
  // Initialize sidebar toggle
  initSidebar();
  
  // Initialize profile dropdown
  initProfileDropdown();
  
  // Initialize data tables
  initDataTables();
  
  // Initialize forms
  initForms();
  
  // Check for active page in URL
  setActivePage();
});

/**
 * Sidebar Toggle Functionality
 */
function initSidebar() {
  const sidebarToggle = document.querySelector('.sidebar-toggle');
  const sidebar = document.querySelector('.admin-sidebar');
  const mainContent = document.querySelector('.admin-main');
  
  if (!sidebarToggle || !sidebar || !mainContent) return;
  
  sidebarToggle.addEventListener('click', function() {
    sidebar.classList.toggle('collapsed');
    mainContent.classList.toggle('expanded');
    
    // Save state in localStorage
    localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
  });
  
  // Check localStorage for saved state
  const sidebarState = localStorage.getItem('sidebarCollapsed');
  
  if (sidebarState === 'true') {
    sidebar.classList.add('collapsed');
    mainContent.classList.add('expanded');
  }
  
  // Handle mobile sidebar toggle
  const mobileToggle = document.querySelector('.mobile-toggle');
  
  if (mobileToggle) {
    mobileToggle.addEventListener('click', function() {
      sidebar.classList.toggle('mobile-expanded');
    });
    
    // Close sidebar when clicking outside on mobile
    document.addEventListener('click', function(event) {
      const isMobile = window.innerWidth <= 768;
      
      if (isMobile && 
          !sidebar.contains(event.target) && 
          !mobileToggle.contains(event.target) &&
          sidebar.classList.contains('mobile-expanded')) {
        sidebar.classList.remove('mobile-expanded');
      }
    });
  }
}

/**
 * Profile Dropdown Toggle
 */
function initProfileDropdown() {
  const profileToggle = document.querySelector('.profile-toggle');
  const profileDropdown = document.querySelector('.profile-dropdown');
  
  if (!profileToggle || !profileDropdown) return;
  
  profileToggle.addEventListener('click', function() {
    profileDropdown.classList.toggle('show');
  });
  
  // Close dropdown when clicking outside
  document.addEventListener('click', function(event) {
    if (!profileToggle.contains(event.target) && !profileDropdown.contains(event.target)) {
      profileDropdown.classList.remove('show');
    }
  });
}

/**
 * Data Tables Functionality
 */
function initDataTables() {
  // Search functionality
  const searchInputs = document.querySelectorAll('.table-search');
  
  searchInputs.forEach(input => {
    input.addEventListener('keyup', function() {
      const searchTerm = this.value.toLowerCase();
      const tableId = this.getAttribute('data-table');
      const table = document.getElementById(tableId);
      
      if (!table) return;
      
      const rows = table.querySelectorAll('tbody tr');
      
      rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchTerm) ? '' : 'none';
      });
    });
  });
  
  // Sort functionality
  const sortHeaders = document.querySelectorAll('.sort-header');
  
  sortHeaders.forEach(header => {
    header.addEventListener('click', function() {
      const tableId = this.closest('table').id;
      const columnIndex = Array.from(this.parentNode.children).indexOf(this);
      const isAscending = this.classList.contains('sort-asc');
      
      // Remove sort classes from all headers
      document.querySelectorAll(`#${tableId} th`).forEach(th => {
        th.classList.remove('sort-asc', 'sort-desc');
      });
      
      // Add appropriate sort class to clicked header
      this.classList.add(isAscending ? 'sort-desc' : 'sort-asc');
      
      // Sort table
      sortTable(tableId, columnIndex, !isAscending);
    });
  });
}

/**
 * Sort table by column
 */
function sortTable(tableId, columnIndex, ascending) {
  const table = document.getElementById(tableId);
  if (!table) return;
  
  const tbody = table.querySelector('tbody');
  const rows = Array.from(tbody.querySelectorAll('tr'));
  
  // Sort rows
  rows.sort((a, b) => {
    const aValue = a.children[columnIndex].textContent.trim();
    const bValue = b.children[columnIndex].textContent.trim();
    
    // Check if values are numbers
    if (!isNaN(aValue) && !isNaN(bValue)) {
      return ascending 
        ? parseFloat(aValue) - parseFloat(bValue)
        : parseFloat(bValue) - parseFloat(aValue);
    }
    
    // Sort as strings
    return ascending
      ? aValue.localeCompare(bValue)
      : bValue.localeCompare(aValue);
  });
  
  // Reorder rows in the table
  rows.forEach(row => tbody.appendChild(row));
}

/**
 * Form Functionality
 */
function initForms() {
  // Image preview for file inputs
  const fileInputs = document.querySelectorAll('input[type="file"][data-preview]');
  
  fileInputs.forEach(input => {
    input.addEventListener('change', function() {
      const previewId = this.getAttribute('data-preview');
      const preview = document.getElementById(previewId);
      
      if (!preview) return;
      
      if (this.files && this.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
          const img = document.createElement('img');
          img.src = e.target.result;
          
          // Clear previous preview
          preview.innerHTML = '';
          preview.appendChild(img);
        }
        
        reader.readAsDataURL(this.files[0]);
      }
    });
  });
  
  // Form validation
  const forms = document.querySelectorAll('.needs-validation');
  
  forms.forEach(form => {
    form.addEventListener('submit', function(event) {
      if (!form.checkValidity()) {
        event.preventDefault();
        event.stopPropagation();
      }
      
      form.classList.add('was-validated');
    });
  });
}

/**
 * Set active page in navigation
 */
function setActivePage() {
  // Get current page from URL
  const currentPath = window.location.pathname;
  const pageName = currentPath.split('/').pop().replace('.php', '');
  
  // Find matching navigation link
  const navLinks = document.querySelectorAll('.nav-link');
  
  navLinks.forEach(link => {
    const linkHref = link.getAttribute('href');
    
    if (linkHref.includes(pageName) || 
        (pageName === 'dashboard' && linkHref.includes('index.php'))) {
      link.classList.add('active');
    } else {
      link.classList.remove('active');
    }
  });
}

/**
 * Show confirmation dialog
 */
function confirmAction(message, callback) {
  if (confirm(message)) {
    callback();
  }
}

/**
 * Delete record
 */
function deleteRecord(id, type) {
  confirmAction(`Êtes-vous sûr de vouloir supprimer cet élément ?`, () => {
    // Here you would send a request to delete the record
    console.log(`Deleting ${type} with ID: ${id}`);
    
    // Example implementation (commented out as we don't have actual API access):
    /*
    fetch(`api/delete-${type}.php`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({ id }),
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        // Remove from DOM
        document.getElementById(`${type}-${id}`).remove();
        showAlert('success', `${type.charAt(0).toUpperCase() + type.slice(1)} supprimé avec succès.`);
      } else {
        showAlert('error', `Erreur lors de la suppression: ${data.message}`);
      }
    })
    .catch(error => {
      showAlert('error', 'Une erreur est survenue.');
      console.error('Error:', error);
    });
    */
    
    // For demo, just remove the element from DOM
    const element = document.getElementById(`${type}-${id}`);
    if (element) {
      element.remove();
      showAlert('success', `${type.charAt(0).toUpperCase() + type.slice(1)} supprimé avec succès.`);
    }
  });
}

/**
 * Show alert message
 */
function showAlert(type, message) {
  // Create alert element
  const alertElement = document.createElement('div');
  alertElement.className = `alert alert-${type}`;
  alertElement.textContent = message;
  
  // Add close button
  const closeButton = document.createElement('button');
  closeButton.type = 'button';
  closeButton.className = 'btn-close';
  closeButton.addEventListener('click', () => alertElement.remove());
  alertElement.appendChild(closeButton);
  
  // Add to page
  const alertContainer = document.querySelector('.alert-container') || document.querySelector('.admin-main');
  alertContainer.prepend(alertElement);
  
  // Auto-hide after 5 seconds
  setTimeout(() => {
    alertElement.remove();
  }, 5000);
}