// ==========================================
// FUNDERLAND - WHERE THE FUN BEGINS!
// MAIN JAVASCRIPT FILE
// ==========================================


const API_BASE = '../api';


// ==========================================
// UTILITY FUNCTIONS
// ==========================================


// Get visitor from session storage
function getVisitor() {
  const visitor = sessionStorage.getItem('visitor');
  return visitor ? JSON.parse(visitor) : null;
}


// Set visitor in session storage
function setVisitor(visitor) {
  sessionStorage.setItem('visitor', JSON.stringify(visitor));
}


// Clear visitor from session storage
function clearVisitor() {
  sessionStorage.removeItem('visitor');
}


// Check if user is logged in
function checkAuth() {
  const visitor = getVisitor();
  if (!visitor) {
    window.location.href = 'login.html';
    return false;
  }
  return true;
}


// Show alert message
function showAlert(message, type = 'success') {
  const alertDiv = document.createElement('div');
  alertDiv.className = `alert alert-${type}`;
  alertDiv.innerHTML = message;
  
  const container = document.querySelector('.container');
  if (container) {
    container.insertBefore(alertDiv, container.firstChild);
    
    // Auto-remove after 5 seconds
    setTimeout(() => {
      alertDiv.remove();
    }, 5000);
  }
}


// Show loading spinner
function showLoading(elementId) {
  const element = document.getElementById(elementId);
  if (element) {
    element.innerHTML = `
      <div class="spinner"></div>
      <p class="loading-text">Loading...</p>
    `;
  }
}


// Format currency
function formatCurrency(amount) {
  return '₹' + parseFloat(amount).toFixed(2);
}


// Format rating stars
function formatStars(rating) {
  const fullStars = Math.floor(rating);
  const halfStar = rating % 1 >= 0.5 ? 1 : 0;
  const emptyStars = 5 - fullStars - halfStar;
  
  let stars = '';
  for (let i = 0; i < fullStars; i++) stars += '⭐';
  if (halfStar) stars += '⭐';
  for (let i = 0; i < emptyStars; i++) stars += '☆';
  
  return stars + ' ' + rating.toFixed(1);
}


// API call wrapper
async function apiCall(endpoint, method = 'GET', data = null) {
  try {
    const options = {
      method: method,
      headers: {
        'Content-Type': 'application/json',
      },
    };
    
    if (data && method !== 'GET') {
      options.body = JSON.stringify(data);
    }
    
    const url = method === 'GET' && data 
      ? `${API_BASE}/${endpoint}?${new URLSearchParams(data)}`
      : `${API_BASE}/${endpoint}`;
    
    const response = await fetch(url, options);
    const result = await response.json();
    
    return result;
  } catch (error) {
    console.error('API Error:', error);
    return { success: false, message: 'Network error occurred' };
  }
}


// ==========================================
// LOAD RIDES FOR FEEDBACK
// ==========================================


async function loadRidesForFeedback(selectElementId) {
  const visitor = getVisitor();
  if (!visitor) {
    showAlert('Please login first', 'error');
    return;
  }
  
  const selectElement = document.getElementById(selectElementId);
  if (!selectElement) {
    console.error('Select element not found:', selectElementId);
    return;
  }
  
  // Show loading state
  selectElement.innerHTML = '<option value="">Loading rides...</option>';
  selectElement.disabled = true;
  
  try {
    const result = await apiCall('get_rides_for_feedback.php', 'GET', {
      visitor_number: visitor.Number || visitor.number
    });
    
    // Clear existing options
    selectElement.innerHTML = '';
    
    if (result.success && result.rides && result.rides.length > 0) {
      // Add default option
      const defaultOption = document.createElement('option');
      defaultOption.value = '';
      defaultOption.textContent = '-- Select a ride you enjoyed --';
      selectElement.appendChild(defaultOption);
      
      // Add rides
      result.rides.forEach(ride => {
        const option = document.createElement('option');
        option.value = ride.rideID;
        option.textContent = ride.rideName;
        selectElement.appendChild(option);
      });
      
      selectElement.disabled = false;
    } else {
      // No rides taken yet
      const noRidesOption = document.createElement('option');
      noRidesOption.value = '';
      noRidesOption.textContent = 'No rides taken yet. Enjoy a ride first!';
      selectElement.appendChild(noRidesOption);
      selectElement.disabled = true;
      
      showAlert('You need to enjoy a ride before giving feedback!', 'info');
    }
  } catch (error) {
    console.error('Error loading rides:', error);
    selectElement.innerHTML = '<option value="">Error loading rides</option>';
    selectElement.disabled = true;
    showAlert('Failed to load rides. Please try again.', 'error');
  }
}


// ==========================================
// STAR RATING COMPONENT
// ==========================================


function initStarRating(containerId, callback) {
  const container = document.getElementById(containerId);
  if (!container) return;
  
  container.className = 'star-rating';
  let selectedRating = 0;
  
  for (let i = 1; i <= 5; i++) {
    const star = document.createElement('span');
    star.className = 'star';
    star.textContent = '★';
    star.dataset.rating = i;
    
    star.addEventListener('click', function() {
      selectedRating = parseInt(this.dataset.rating);
      updateStars();
      if (callback) callback(selectedRating);
    });
    
    star.addEventListener('mouseenter', function() {
      highlightStars(parseInt(this.dataset.rating));
    });
    
    container.appendChild(star);
  }
  
  container.addEventListener('mouseleave', function() {
    updateStars();
  });
  
  function updateStars() {
    const stars = container.querySelectorAll('.star');
    stars.forEach((star, index) => {
      if (index < selectedRating) {
        star.classList.add('active');
      } else {
        star.classList.remove('active');
      }
    });
  }
  
  function highlightStars(rating) {
    const stars = container.querySelectorAll('.star');
    stars.forEach((star, index) => {
      if (index < rating) {
        star.classList.add('active');
      } else {
        star.classList.remove('active');
      }
    });
  }
}


// ==========================================
// LOGOUT FUNCTION
// ==========================================


function logout() {
  if (confirm('Are you sure you want to logout?')) {
    clearVisitor();
    window.location.href = 'index.html';
  }
}


// ==========================================
// INITIALIZE ON PAGE LOAD
// ==========================================


document.addEventListener('DOMContentLoaded', function() {
  console.log('🎢 Funderland - Where the Fun Begins! 🎉');
  
  // Auto-load rides for feedback if on feedback page
  const rideSelect = document.getElementById('ride-select') || document.getElementById('rideSelect');
  if (rideSelect) {
    loadRidesForFeedback(rideSelect.id);
  }
});
