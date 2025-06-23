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
});
