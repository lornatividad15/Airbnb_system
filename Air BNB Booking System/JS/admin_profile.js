//FOR ADMIN PROFILE SCRIPTS
document.addEventListener("DOMContentLoaded", () => {
    const modal = document.getElementById("infoModal");
    if (modal) {
        modal.style.display = "flex";
    }

    const toggle = document.querySelector(".dropdown-toggle");
    const dropdown = document.getElementById("adminDropdown");

    if (toggle && dropdown) {
        toggle.addEventListener("click", function (e) {
            e.stopPropagation();
            dropdown.classList.toggle("show");
        });

        document.addEventListener("click", function () {
            dropdown.classList.remove("show");
        });
    }
});

function togglePassword(id) {
    const input = document.getElementById(id);
    if (input.type === "password") {
        input.type = "text";
    } else {
        input.type = "password";
    }
}

function closeModal() {
    const modal = document.getElementById("infoModal");
    if (modal) {
        modal.style.display = "none";
    }
}