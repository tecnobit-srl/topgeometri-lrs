
require('@/bootstrap');

window.Vue = require('vue');

Vue.use(require('./lib').default);

require('./app/main');
