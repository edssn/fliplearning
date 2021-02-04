define(["local_fliplearning/vue",
        "local_fliplearning/vuetify",
        "local_fliplearning/chartdynamic",
    ],
    function(Vue, Vuetify, ChartDynamic) {
        "use strict";
        let wwwroot = M.cfg.wwwroot;

        function init(content) {
            console.log({chart: content.chart});

            Vue.use(Vuetify);
            Vue.component('chart', ChartDynamic);

            new Vue({
                delimiters: ["[[", "]]"],
                el: "#graph1",
                vuetify: new Vuetify(),
                data() {
                    return {
                        chart: content.chart,
                    };
                },
                mounted() {
                    document.querySelector("#pd-loader").style.display = "none";
                    document.querySelector("#graph1").style.display = "block";
                },
                methods: {
                }
            });

        }

        return {
            init: init
        };
    });