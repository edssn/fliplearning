define(["local_fliplearning/vue",
        "local_fliplearning/vuetify",
        "local_fliplearning/axios",
        "local_fliplearning/moment",
        "local_fliplearning/pagination",
        "local_fliplearning/chartstatic",
        "local_fliplearning/pageheader",
        "local_fliplearning/helpdialog",
    ],
    function(Vue, Vuetify, Axios, Moment, Pagination, ChartStatic, PageHeader, HelpDialog) {
        "use strict";

        function init(content) {
            // console.logs(content);
            Vue.use(Vuetify);
            Vue.component('pagination', Pagination);
            Vue.component('chart', ChartStatic);
            Vue.component('pageheader', PageHeader);
            Vue.component('helpdialog', HelpDialog);
            let vue = new Vue({
                delimiters: ["[[", "]]"],
                el: "#teacher_sessions",
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
                        hours_sessions: content.indicators.sessions,
                        session_count: content.indicators.count,
                        inverted_time: content.indicators.time,
                        hours_sessions_colors: content.hours_sessions_colors,
                        inverted_time_colors: content.inverted_time_colors,
                        sessions_count_colors: content.sessions_count_colors,
                        search: null,

                        help_dialog: false,
                        help_contents: [],

                        pluginSectionName: "teacher_sessions",
                        inverted_time_chart: "inverted_time_chart",
                        sessions_by_hours_and_days_chart: "sessions_by_hours_and_days_chart",
                        week_sessions_chart: "week_sessions_chart",
                    }
                },
                mounted() {
                    document.querySelector("#teacher_sessions_loader").style.display = "none";
                    document.querySelector("#teacher_sessions").style.display = "block";
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

                    update_interactions(week) {
                        this.loading = true;
                        this.errors = [];
                        let data = {
                            action: "worksessions",
                            userid: this.userid,
                            courseid: this.courseid,
                            weekcode: week.weekcode,
                            profile: this.render_has,
                            url: window.location.href,
                        }
                        Axios({
                            method: 'get',
                            url: M.cfg.wwwroot + "/local/fliplearning/ajax.php",
                            params: data,
                        }).then((response) => {
                            if (response.status == 200 && response.data.ok) {
                                this.hours_sessions = response.data.data.indicators.sessions;
                                this.session_count = response.data.data.indicators.count;
                                this.inverted_time = response.data.data.indicators.time;
                            } else {
                                this.error_messages.push(this.strings.error_network);
                            }
                        }).catch((e) => {
                            this.errors.push(this.strings.api_error_network);
                        }).finally(() => {
                            this.loading = false;
                        });
                        return this.data;
                    },

                    get_point_category_name(point, dimension) {
                        let series = point.series,
                            isY = dimension === 'y',
                            axis = series[isY ? 'yAxis' : 'xAxis'];
                        return axis.categories[point[isY ? 'y' : 'x']];
                    },

                    build_sessions_by_hours_and_days_chart() {
                        let chart = new Object();
                        chart.title = {
                            text: null,
                        };
                        chart.chart = {
                            type: 'heatmap',
                            backgroundColor: null,
                            style: { fontFamily: 'poppins' },
                        };
                        chart.xAxis = {
                            categories: this.strings.days,
                        };
                        chart.yAxis = {
                            categories: this.strings.hours,
                            title: null,
                            reversed: true,
                        };
                        chart.colorAxis = {
                            min: 0,
                            minColor: this.hours_sessions_colors[0],
                            maxColor: this.hours_sessions_colors[1]
                        };
                        chart.legend = {
                            layout: 'horizontal',
                            verticalAlign: 'bottom',
                        };
                        chart.tooltip = {
                            useHTML: true,
                            formatter: function() {
                                let xCategoryName = vue.get_point_category_name(this.point, 'x');
                                let yCategoryName = vue.get_point_category_name(this.point, 'y');
                                return '<small>' + xCategoryName + ' ' + yCategoryName + '</small><br/>' +
                                    '<b style="color: ' + vue.hours_sessions_colors[1] + ';">• </b>' +
                                    vue.capitalizeFirstLetter(vue.strings.sessions_text) + ': ' +
                                    this.point.value + '<br/>';
                            }
                        };
                        chart.series = [{
                            borderWidth: 2,
                            borderColor: '#FAFAFA',
                            data: this.hours_sessions,
                        }];
                        return chart;
                    },

                    build_inverted_time_chart() {
                        let chart = new Object();
                        chart.chart = {
                            type: 'bar',
                            backgroundColor: null,
                            style: { fontFamily: 'poppins' },
                        };
                        chart.title = {
                            text: null,
                        };
                        chart.colors = this.inverted_time_colors;
                        chart.xAxis = {
                            type: 'category',
                            crosshair: true,
                        };
                        chart.yAxis = {
                            title: {
                                text: this.strings.time_inverted_x_axis,
                            }
                        };
                        chart.tooltip = {
                            shared: true,
                            useHTML: true,
                            formatter: function() {
                                let category_name = this.points[0].key;
                                let time = vue.convert_time(this.y);
                                return `<small>${category_name}</small></br><b>${time}</b>`;
                            }
                        };
                        chart.legend = {
                            enabled: false
                        };
                        chart.series = [{
                            colorByPoint: true,
                            data: this.inverted_time.data
                        }];
                        return chart;
                    },

                    build_week_sessions_chart() {
                        let chart = new Object();
                        chart.chart = {
                            backgroundColor: null,
                            style: { fontFamily: 'poppins' },
                        };
                        chart.title = {
                            text: null,
                        };
                        chart.colors = this.sessions_count_colors;
                        chart.yAxis = {
                            title: {
                                text: this.strings.session_count_yaxis_title,
                            },
                            allowDecimals: false
                        };
                        chart.xAxis = {
                            categories: this.session_count.categories,
                        };
                        chart.tooltip = {
                            valueSuffix: this.strings.session_count_tooltip_suffix,
                        };
                        chart.legend = {
                            layout: 'horizontal',
                            verticalAlign: 'bottom',
                        };
                        chart.series = this.session_count.data
                        return chart;
                    },

                    convert_time(time) {
                        time *= 3600; // pasar las horas a segundos
                        let h = this.strings.hours_short;
                        let m = this.strings.minutes_short;
                        let s = this.strings.seconds_short;
                        let hours = Math.floor(time / 3600);
                        let minutes = Math.floor((time % 3600) / 60);
                        let seconds = Math.floor(time % 60);
                        let text;
                        if (hours >= 1) {
                            if (minutes >= 1) {
                                text = `${hours}${h} ${minutes}${m}`;
                            } else {
                                text = `${hours}${h}`;
                            }
                        } else if ((minutes >= 1)) {
                            if (seconds >= 1) {
                                text = `${minutes}${m} ${seconds}${s}`;
                            } else {
                                text = `${minutes}${m}`;
                            }
                        } else {
                            text = `${seconds}${s}`;
                        }
                        return text;
                    },

                    capitalizeFirstLetter(string) {
                        return string.charAt(0).toUpperCase() + string.slice(1);
                    },

                    openChartHelp(chart) {
                        let contents = [];
                        if (chart == this.inverted_time_chart) {
                            contents.push({
                                title: this.strings.inverted_time_help_title,
                                description: this.strings.inverted_time_help_description_p1,
                            });
                            contents.push({
                                description: this.strings.inverted_time_help_description_p2,
                            });
                        } else if (chart == this.sessions_by_hours_and_days_chart) {
                            contents.push({
                                title: this.strings.hours_sessions_help_title,
                                description: this.strings.hours_sessions_help_description_p1,
                            });
                            contents.push({
                                description: this.strings.hours_sessions_help_description_p2,
                            });
                        } else if (chart == this.week_sessions_chart) {
                            contents.push({
                                title: this.strings.sessions_count_help_title,
                                description: this.strings.sessions_count_help_description_p1,
                            });
                            contents.push({
                                description: this.strings.sessions_count_help_description_p2,
                            });
                        }
                        this.help_contents = contents;
                        if (this.help_contents.length) {
                            this.help_dialog = true;
                            this.saveInteraction (chart, "viewed", "chart_help_dialog", 7);
                        }
                    },

                    update_help_dialog(value) {
                        this.help_dialog = value;
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
                            courseid : this.courseid,
                            userid : this.userid,
                        };
                        Axios({
                            method:'get',
                            url: `${M.cfg.wwwroot}/local/fliplearning/ajax.php`,
                            params : data,
                        }).then((r) => {}).catch((e) => {});
                    },

                    get_timezone() {
                        return `${this.strings.change_timezone} ${this.timezone}`;
                    },
                }
            })
        }

        return {
            init: init
        };
    });