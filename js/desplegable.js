    document.addEventListener('DOMContentLoaded', function() {
            const userDropdownButton = document.getElementById('user-dropdown-button');
            const userDropdown = document.getElementById('user-dropdown');

            if (userDropdownButton && userDropdown) {
                userDropdownButton.addEventListener('click', function() {
                    userDropdown.classList.toggle('hidden');
                    this.setAttribute('aria-expanded', !userDropdown.classList.contains('hidden'));
                });

                // Cerrar el desplegable si se hace clic fuera
                document.addEventListener('click', function(event) {
                    if (!userDropdownButton.contains(event.target) && !userDropdown.contains(event.target)) {
                        userDropdown.classList.add('hidden');
                        userDropdownButton.setAttribute('aria-expanded', false);
                    }
                });
            }
        });