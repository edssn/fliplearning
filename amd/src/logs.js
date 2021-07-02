define(["local_fliplearning/vue",
        "local_fliplearning/vuetify",
        "local_fliplearning/axios",
        "local_fliplearning/alertify",
        "local_fliplearning/pageheader",
    ],
    function(Vue, Vuetify, Axios, Alertify, PageHeader) {
        "use strict";

        function init(content) {
            // console.log(content);

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

                    helpDialog: false,
                    helpTitle: null,
                    helpDescription: null,
                    helpTableHeaders: null,
                    helpTableItems: null,
                    moodle_logs: "moodle_logs",
                    fliplearning_logs: "fliplearning_logs",

                    pluginSectionName: "teacher_download_logs",
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
                            Alertify.error(this.strings.logsWithoutDatesValidation);
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
                            if (response.status == 200 && response.data.ok) {
                                let filename = response.data.message;
                                let url = `${M.cfg.wwwroot}/local/fliplearning/downloads/${filename}`;
                                let link = document.createElement('a');
                                link.href = url;
                                link.click();
                                Alertify.success(this.strings.logsSuccessDownload);
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

                    openChartHelp (component) {
                        if (component === this.moodle_logs || component === this.fliplearning_logs) {
                            this.prepareHelpData(component);
                        } else {
                            return;
                        }
                        this.helpDialog = true;
                        this.saveInteraction (component, "viewed", "help_dialog", 7);
                    },

                    prepareHelpData (component) {
                        this.helpTableHeaders = [
                            { text: this.strings.logsHelpTableHeaderColumn, value: 'column' },
                            { text: this.strings.logsHelpTableHeaderDescription, value: 'description', sortable: false },
                        ];
                        if (component === this.moodle_logs) {
                            this.helpTitle = this.strings.moodleLogsHelpTitle;
                            this.helpDescription = this.strings.moodleLogsHelpDescription;
                            this.helpTableItems = [
                                { column: this.strings.logsHeaderLogid, description: this.strings.logsHeaderLogIdHelpDescription },
                                { column: this.strings.logsHeaderUserId, description: this.strings.logsHeaderUserIdHelpDescription },
                                { column: this.strings.logsHeaderUsername, description: this.strings.logsHeaderUsernameHelpDescription },
                                { column: this.strings.logsHeaderFirstname, description: this.strings.logsHeaderFirstnameHelpDescription },
                                { column: this.strings.logsHeaderLastname, description: this.strings.logsHeaderLastnameHelpDescription },
                                { column: this.strings.logsHeaderRoles, description: this.strings.logsHeaderRolesHelpDescription },
                                { column: this.strings.logsHeaderCourseId, description: this.strings.logsHeaderCourseIdHelpDescription },
                                { column: this.strings.logsHeaderCoursename, description: this.strings.logsHeaderCoursenameHelpDescription },
                                { column: this.strings.logsHeaderContextLevel, description: this.strings.logsHeaderContextLevelHelpDescription },
                                { column: this.strings.logsHeaderComponent, description: this.strings.logsHeaderComponentHelpDescription },
                                { column: this.strings.logsHeaderAction, description: this.strings.logsHeaderActionHelpDescription },
                                { column: this.strings.logsHeaderTarget, description: this.strings.logsHeaderTargetHelpDescription },
                                { column: this.strings.logsHeaderActivitytype, description: this.strings.logsHeaderActivitytypeHelpDescription },
                                { column: this.strings.logsHeaderActivityname, description: this.strings.logsHeaderActivitynameHelpDescription },
                                { column: this.strings.logsHeaderSectionnumber, description: this.strings.logsHeaderSectionnumberHelpDescription },
                                { column: this.strings.logsHeaderSectionname, description: this.strings.logsHeaderSectionnameHelpDescription },
                                { column: this.strings.logsHeaderTimecreated, description: this.strings.logsHeaderTimecreatedHelpDescription },
                            ];
                        } else if (component === this.fliplearning_logs) {
                            this.helpTitle = this.strings.fliplearningLogsHelpTitle;
                            this.helpDescription = this.strings.fliplearningLogsHelpDescription;
                            this.helpTableItems = [
                                { column: this.strings.logsHeaderLogid, description: this.strings.logsHeaderLogIdHelpDescription },
                                { column: this.strings.logsHeaderUserId, description: this.strings.logsHeaderUserIdHelpDescription },
                                { column: this.strings.logsHeaderUsername, description: this.strings.logsHeaderUsernameHelpDescription },
                                { column: this.strings.logsHeaderFirstname, description: this.strings.logsHeaderFirstnameHelpDescription },
                                { column: this.strings.logsHeaderLastname, description: this.strings.logsHeaderLastnameHelpDescription },
                                { column: this.strings.logsHeaderRoles, description: this.strings.logsHeaderRolesHelpDescription },
                                { column: this.strings.logsHeaderCourseId, description: this.strings.logsHeaderCourseIdHelpDescription },
                                { column: this.strings.logsHeaderCoursename, description: this.strings.logsHeaderCoursenameHelpDescription },
                                { column: this.strings.logsHeaderPluginsection, description: this.strings.logsHeaderPluginsectionHelpDescription },
                                { column: this.strings.logsHeaderComponent, description: this.strings.logsHeaderComponentHelpDescription },
                                { column: this.strings.logsHeaderAction, description: this.strings.logsHeaderActionHelpDescription },
                                { column: this.strings.logsHeaderTarget, description: this.strings.logsHeaderTargetHelpDescription },
                                { column: this.strings.logsHeaderUrl, description: this.strings.logsHeaderUrlHelpDescription},
                                { column: this.strings.logsHeaderTimecreated, description: this.strings.logsHeaderTimecreatedHelpDescription },
                                { column: this.strings.logsHeaderDescription, description: this.strings.logsHeaderDescriptionHelpDescription },
                            ];
                        }
                    },

                    saveInteraction (component, interaction, target, interactiontype) {
                        let data = {
                            action : "saveinteraction",
                            pluginsection : this.pluginSectionName,
                            component,
                            interaction,
                            target,
                            url: window.location.href,
                            interactiontype,
                            courseid : this.courseId,
                            userid : this.userId,
                        };
                        Axios({
                            method:'get',
                            url: `${M.cfg.wwwroot}/local/fliplearning/ajax.php`,
                            params : data,
                        }).then((r) => {}).catch((e) => {});
                    },
                }
            });
        }

        return {
            init: init
        };
    });