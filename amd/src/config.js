define([],function() {
    "use strict";
    window.requirejs.config({
        packages: [{
            name: 'highcharts',
            main: 'highcharts'
        }],
        paths: {
            "vue" : M.cfg.wwwroot + '/local/fliplearning/js/vue',
            "vuetify" : M.cfg.wwwroot + '/local/fliplearning/js/vuetify',
            "highcharts": M.cfg.wwwroot + '/local/fliplearning/js/highcharts/'
        },
        shim: {
            'vue' : {exports: 'vue'},
            'vuetify': {deps: ['vue'] , exports: 'vuetify'},
        }
    });
});
