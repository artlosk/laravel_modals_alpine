import '../../js/bootstrap';
import 'admin-lte';
import toastr from 'toastr';
import 'toastr/build/toastr.css';

import Alpine from 'alpinejs';

import './darkMode';
import { initDateInputs } from './dateInput';
import './users';
import './components/manager-modals';
import './posts/postList';
import './users/userList';

window.Alpine = Alpine;
Alpine.start();

window.toastr = toastr;
toastr.options = {
    closeButton: true,
    progressBar: true,
    positionClass: 'toast-top-right',
    timeOut: '5000'
};

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
