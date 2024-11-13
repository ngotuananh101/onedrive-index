import axios from 'axios';

window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

import jquery from 'jquery';

window.$ = window.jQuery = jquery;

import Swal from 'sweetalert2';

window.Swal = Swal;
