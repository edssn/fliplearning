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
            "axios": M.cfg.wwwroot + '/local/student_reports/js/axios',
            "sortablejs": M.cfg.wwwroot + '/local/student_reports/js/sortablejs',
            "draggable": M.cfg.wwwroot + '/local/student_reports/js/draggable',
            "datepicker": M.cfg.wwwroot + '/local/student_reports/js/datepicker',
            "moment": M.cfg.wwwroot + '/local/student_reports/js/moment',
            "alertify": M.cfg.wwwroot + '/local/student_reports/js/alertify',
            "highcharts": M.cfg.wwwroot + '/local/fliplearning/js/highcharts/'
        },
        shim: {
            'vue' : {exports: 'vue'},
            'vuetify': {deps: ['vue'] , exports: 'vuetify'},
            'axios': {exports: 'axios'},
            'sortablejs': {deps: ['vue'], exports: 'sortablejs'},
            'draggable': {deps: ['sortablejs'], exports: 'draggable'},
            'datepicker': {deps: ['vue'], exports: 'datepicker'},
            'moment': {deps: ['vue'], exports: 'moment'},
            'alertify': {exports: 'alertify'},
        }
    });
});
