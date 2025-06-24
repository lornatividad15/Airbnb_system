//FOR ADMIN PAGE SCRIPTS
//FOR ADMIN DROPDOWN MENU
document.addEventListener("DOMContentLoaded", function () {
  const toggle = document.querySelector(".dropdown-toggle");
  const dropdown = document.querySelector(".dropdown");

  toggle.addEventListener("click", function (e) {
    e.stopPropagation(); 
    dropdown.classList.toggle("show");
  });

  document.addEventListener("click", function () {
    dropdown.classList.remove("show");
  });

  //FOR ADMIN DELETE MODAL
  const deleteModal = document.getElementById('adminDeleteModal');
  const deleteForm = document.getElementById('adminDeleteForm');
  const deleteBookingIdInput = document.getElementById('adminDeleteBookingId');
  const cancelBtn = document.getElementById('adminDeleteCancelBtn');
  document.querySelectorAll('.admin-delete-modal-btn').forEach(btn => {
    btn.addEventListener('click', function(e) {
      e.preventDefault();
      const bookingId = this.closest('form').querySelector('input[name="booking_id"]').value;
      deleteBookingIdInput.value = bookingId;
      deleteModal.classList.add('active');
    });
  });
  cancelBtn.addEventListener('click', function() {
    deleteModal.classList.remove('active');
    deleteBookingIdInput.value = '';
  });
  //FOR ADMIN DELETE MODAL OUTSIDE CLICK
  deleteModal.addEventListener('click', function(e) {
    if (e.target === deleteModal) {
      deleteModal.classList.remove('active');
      deleteBookingIdInput.value = '';
    }
  });
});
