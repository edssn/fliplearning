define(["local_fliplearning/vue",
        "local_fliplearning/vuetify",
        "local_fliplearning/axios",
        "local_fliplearning/alertify",
        "local_fliplearning/pageheader",
    ],
    function(Vue, Vuetify, Axios, Alertify, PageHeader) {
        "use strict";

        function init(content) {
            console.log(content);

            const timeout = 60 * 120 * 1000;
            Axios.defaults.timeout = timeout;

            Vue.use(Vuetify);
            Vue.component('pageheader', PageHeader);
            const app = new Vue({
                delimiters: ["[[", "]]"],
                el: "#logs",
                vuetify: new Vuetify(),
                data: {
                    strings: content.strings,
                    courseId: content.courseId,
                    userId: content.userId,
                    timezone: content.timezone,

                    dates: [],
                    datesLabels: [],

                    loadingBtnFliplearningLogs: false,
                    loadingBtnMoodleLogs: false,
                },
                mounted() {
                    document.querySelector("#logs-loader").style.display = "none";
                    document.querySelector("#logs").style.display = "block";
                },
                computed: {
                    dateRangeText () {
                        let dates_array = this.dates.map(date => new Date(date));
                        dates_array.sort(this.sortDates);
                        this.datesLabels = dates_array.map(date => this.formatDate(date));
                        return this.datesLabels.join(' → ');
                    },
                },
                methods: {
                    getHelpContent() {
                        let contents = [];
                        contents.push({
                            title: this.strings.sectionHelpTitle,
                            description: this.strings.sectionHelpDescription,
                        });
                        return contents;
                    },

                    getFile(type) {
                        if (!this.datesLabels.length){
                            Alertify.error("Por favor, selecciona un rango de fechas");
                            return;
                        }

                        if (type === 'moodle') {
                            this.loadingBtnMoodleLogs = true;
                        } else if (type === 'fliplearning') {
                            this.loadingBtnFliplearningLogs = true;
                        } else {
                            return;
                        }

                        let startdate = this.datesLabels[0].split('/').reverse().join('-');
                        let enddate = this.datesLabels[1]
                            ? this.datesLabels[1].split('/').reverse().join('-')
                            : startdate;

                        console.log({startdate, enddate});

                        let data = {
                            action: "downloadlogs",
                            logstype: type,
                            userid: this.userId,
                            courseid: this.courseId,
                            startdate,
                            enddate,
                            url: window.location.href,
                        }
                        Axios({
                            method: 'post',
                            url: M.cfg.wwwroot + "/local/fliplearning/ajax.php",
                            timeout : timeout,
                            params: data
                        }).then((response) => {
                            console.log(response);
                            if (response.status == 200 && response.data.ok) {
                                console.log(response.data);
                                let filename = response.data.message;
                                let url = `${M.cfg.wwwroot}/local/fliplearning/downloads/${filename}`;
                                console.log({url});
                                let link = document.createElement('a');
                                link.href = url;
                                link.click();
                                Alertify.success(this.strings.successDownload);
                            } else if (response.data.message) {
                                Alertify.error(response.data.message);
                            } else {
                                Alertify.error(this.strings.apiErrorNetwork);
                            }
                        }).catch((e) => {
                            Alertify.error(this.strings.apiErrorNetwork);
                        }).finally(() => {
                            (type === 'moodle')
                                ? this.loadingBtnMoodleLogs = false
                                : this.loadingBtnFliplearningLogs = false;
                        });
                    },

                    sortDates (a, b) {
                        return b.getTime() > a.getTime() ? -1 : b.getTime() < a.getTime() ? 1 : 0;
                    },

                    formatDate (date) {
                        const year = date.getUTCFullYear();
                        const month_number = date.getUTCMonth() + 1;
                        const month = month_number < 10 ? `0${month_number}` : month_number;
                        const day_number = date.getUTCDate();
                        const day = day_number < 10 ? `0${day_number}` : day_number;
                        return `${day}/${month}/${year}`;
                    },

                    getTimezone() {
                        let information = `${this.strings.changeTimezone} ${this.timezone}`
                        return information;
                    },
                }
            });
        }

        return {
            init: init
        };
    });