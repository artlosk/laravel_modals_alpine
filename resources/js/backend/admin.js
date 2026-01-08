import '../../js/bootstrap';
import 'bootstrap';
import 'admin-lte';

import Alpine from 'alpinejs';

import './darkMode';
import { initDateInputs } from './dateInput';
import './users';
import './components/manager-modals';
import './posts/postList';
import './users/userList';

window.Alpine = Alpine;
Alpine.start();

initDateInputs();

if (typeof window.updateDarkModeButtonState === 'function') {
    if (document.readyState === 'complete') {
        window.updateDarkModeButtonState();
    } else {
        window.addEventListener('load', function () {
            window.updateDarkModeButtonState();
        });
    }
}
