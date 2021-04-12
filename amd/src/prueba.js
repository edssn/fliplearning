define(["local_fliplearning/vue",
        "local_fliplearning/vuetify",
        "local_fliplearning/axios",
        "local_fliplearning/moment",
        "local_fliplearning/pagination",
        "local_fliplearning/chartdynamic",
        "local_fliplearning/pageheader",
    ],
    function(Vue, Vuetify, Axios, Moment, Pagination, ChartDynamic, Pageheader) {
        "use strict";

        function init(content) {
            console.log(content);
            Vue.use(Vuetify)
            Vue.component('pagination', Pagination);
            Vue.component('chart', ChartDynamic);
            Vue.component('pageheader', Pageheader);
            let vue = new Vue({
                delimiters: ["[[", "]]"],
                el: "#prueba",
                vuetify: new Vuetify(),
                data() {
                    return {
                        strings: content.strings,
                        groups: content.groups,
                        userid: content.userid,
                        courseid: content.courseid,
                        timezone: content.timezone,
                        render_has: content.profile_render,
                        loading: false,
                        errors: [],
                        pages: content.pages,
                    }
                },
                mounted() {
                    document.querySelector("#sessions-loader").style.display = "none";
                    document.querySelector("#prueba").style.display = "block";
                },
                methods: {
                    get_help_content() {
                        let helpcontents = `Texto de Ayuda`;
                        return helpcontents;
                    },

                    get_timezone() {
                        let information = `${this.strings.change_timezone} ${this.timezone}`
                        return information;
                    },
                }
            })
        }

        return {
            init: init
        };
    });