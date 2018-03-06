// Get the modal
var modal = document.getElementById("modal");

// Get the close button
var close = document.getElementsByClassName("close")[0];

// When the user clicks on close button, close the modal
close.onclick = function() {
    modal.style.display = "none";
}

// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
    if (event.target == modal) {
        modal.style.display = "none";
    }
}
