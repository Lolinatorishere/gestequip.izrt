var dropdown = document.querySelector('.profile-dropdown');
var profileUsername = document.querySelector('.profile-username');

profileUsername.addEventListener('click', function(event) {
    event.stopPropagation();
    dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
});

document.addEventListener('click', function(event) {
    var isClickInside = profileUsername.contains(event.target) || dropdown.contains(event.target);
    if (!isClickInside) {
        dropdown.style.display = 'none';
    }
});