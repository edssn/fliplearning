define(["local_fliplearning/vue",
        "local_fliplearning/vuetify",
        "local_fliplearning/axios",
        "local_fliplearning/sortablejs",
        "local_fliplearning/draggable",
        "local_fliplearning/datepicker",
        "local_fliplearning/moment",
        "local_fliplearning/alertify",
        "local_fliplearning/pageheader",
    ],
    function(Vue, Vuetify, Axios, Sortable, Draggable, Datepicker, Moment, Alertify, PageHeader) {
        "use strict";

        function init(content) {
            console.log(content);
            Vue.use(Vuetify);
            Vue.component('draggable', Draggable);
            Vue.component('datepicker', Datepicker);
            Vue.component('pageheader', PageHeader);
            const app = new Vue({
                delimiters: ["[[", "]]"],
                el: "#logs",
                vuetify: new Vuetify(),
                data: {
                    strings: content.strings,
                    courseid: content.courseid,
                    userid: content.userid,
                    timezone: content.timezone,
                    loading: false,
                    errors: [],

                    dates: [],
                    dates_labels: [],

                    loading_btn_fliplearning_logs: false,
                    loading_btn_moodle_logs: false,
                },
                mounted() {
                    document.querySelector("#logs-loader").style.display = "none";
                    document.querySelector("#logs").style.display = "block";
                },
                computed: {
                    dateRangeText () {
                        let dates_array = this.dates.map(date => new Date(date));
                        dates_array.sort(this.sort_dates);
                        this.dates_labels = dates_array.map(date => this.format_date(date));
                        console.log(this.dates_labels);
                        return this.dates_labels.join(' → ');
                    },
                },
                methods: {
                    get_help_content() {
                        let contents = [];
                        contents.push({
                            title: this.strings.section_help_title,
                            description: this.strings.section_help_description,
                        });
                        return contents;
                    },

                    sort_dates (a, b) {
                        return b.getTime() > a.getTime() ? -1 : b.getTime() < a.getTime() ? 1 : 0;
                    },

                    format_date (date) {
                        const year = date.getUTCFullYear();
                        const month_number = date.getUTCMonth() + 1;
                        const month = month_number < 10 ? `0${month_number}` : month_number;
                        const day_number = date.getUTCDate();
                        const day = day_number < 10 ? `0${day_number}` : day_number;
                        return `${day}/${month}/${year}`;
                    },

                    get_timezone() {
                        let information = `${this.strings.change_timezone} ${this.timezone}`
                        return information;
                    },
                }
            });
        }

        return {
            init: init
        };
    });